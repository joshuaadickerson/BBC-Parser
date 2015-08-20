<?php

// @todo change to \StringParser\BBC
namespace BBC;

use \BBC\Codes;

/**
 * A tag is the name of a code. Like [url]. The tag is "url".
 * A code is the instructions, including the name (tag).
 * Each tag can have many codes
 */
class PregParser
{
	protected $message;
	/**
	 * \BBC\Codes
	 */
	protected $bbc;
	/**
	 * An array of BBC.
	 * [$tag]* => [
	 *     [code],
	 *     [code]
	 * ]*
	 */
	protected $bbc_codes;
	protected $item_codes;
	protected $next_closing_bracket = 0;


	public function __construct(Codes $bbc)
	{
		$this->bbc = $bbc;

		$this->codes = $bbc->getCodes();
		$this->tags = $bbc->getTags();
		$this->closing_tags = $bbc->getClosingTags();

		$this->item_codes = $this->bbc->getItemCodes();
	}

	public function resetParser()
	{
		//$this->tags = null;
		$this->pos = null;
		$this->pos1 = null;
		$this->pos2 = null;
		$this->last_pos = null;
		$this->open_tags = array();
		$this->open_bbc = new \SplStack;
		$this->do_autolink = true;
		$this->inside_tag = null;
		$this->lastAutoPos = 0;
	}

	public function parse($message)
	{
		// Don't waste cycles
		if ($message === '')
		{
			return '';
		}

		// Clean up any cut/paste issues we may have
		$message = sanitizeMSCutPaste($message);

		// Unfortunately, this has to be done here because smileys are parsed as blocks between BBC
		// @todo remove from here and make the caller figure it out
		if (!$this->parsingEnabled())
		{
			if ($this->do_smileys)
			{
				parsesmileys($message);
			}

			return $message;
		}

		// @todo change this to <br> (it will break tests)
		$message = str_replace("\n", '<br />', $message);

		// @todo test to see if searching for '[' here makes sense
		// if (strpos($this->message, '[') === false)
		// return $this->message;

		// I guess if we made it this far...
		$this->message = $message;
		//$msg_parts = $this->tokenize($message);
		return $this->parseTokens($this->tokenize($message));
	}

	public function parseTokens(array $msg_parts)
	{
		$this->resetParser();

		$break = false;
		$next_closing_bracket = 0;

		// don't use a foreach so we can jump around the array without worrying about a cursor
		for ($num_parts = count($msg_parts), $pos = 0; $pos < $num_parts && !$break; $pos++)
		{
			list($part, $offset) = $msg_parts[$pos];

			// @todo this needs to get rid of the substr. Better to just make the array account for [ and [/
			// What if we just searched for [ and then checked if the next element is a code?
			//$possible_tag = substr($part, 1);

			$possible_closer = isset($this->closing_tags[$part]);
			// If it's a closer, check if there is a matching opening tag.
			// If there was an opening tag, $this->closeCode();
			// If it wasn't a corresponding opening tag, continue;

			if ($this->isTag($part))
			{
				// Don't open the tag yet, we need to look ahead and see if there is a ]
				// We might even already know where it's at
				$next_closing_bracket = $next_closing_bracket > $pos : $next_closing_bracket : $this->lookAhead($msg_parts, $pos, ']');

				if ($next_closing_bracket !== -1)
				{
					// Starts with a /, has an open tag, that tag matches our possible tag, and the closing bracket is next...
					// We have a closer!
					if ($possible_closer && !empty($last_open_tag) && $last_open_tag[Codes::ATTR_TAG] === $part && $next_closing_bracket === $pos + 1)
					{
						// When we close is actually when we handle all of the parsing like autolink, smilies, before/after/content
						// Then we put it in to a message string
						// Since we don't need to go backwards, we can pop off the previous array elements to save space
						$this->closeCode();
						continue;
					}

					// Okay, open the tag now.
					$tag = $this->findTag($bbc[$possible_tag], $msg_parts, $pos, $next_closing_bracket);

					// No tag found
					if ($tag === null)
					{
						continue;
					}

					// Itemcodes are tags too. Just very different
					if (!empty($tag[Codes::ATTR_ITEMCODE]))
					{
						$this->handleItemCode($tag);
					}

					// If this is block level and the last tag isn't, we need to close it.
					// Non-block level tags can't wrap block level tags
					if ($tag[Codes::ATTR_BLOCK_LEVEL] && !$last_open_tag[Codes::BLOCK_LEVEL])
					{
						$this->closeNonBlockLevelTags();
					}

					// Open the code.
					// Also sets the disallowed children
					$this->openCode($tag);
				}
			}
			// It wasn't a tag
			else
			{
				//
			}
		}

		// If there is anything remaining, close it
		$this->closeRemainingCodes();
	}

	protected function tokenize($message)
	{
		$split_string = $this->getTokenRegex();

		$msg_parts = preg_split($split_string, $message, null, PREG_SPLIT_OFFSET_CAPTURE | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		var_dump(
		//$this->bbc_codes,
		//array_keys($this->bbc_codes),
			$this->bbc->getTags(),
			$split_chars,
			$split_string,
			$msg_parts
		);

		return $msg_parts;
	}

	protected function isOpen($closing_tag)
	{
		// This isn't what I actually want. I want to check if it is open and is the last code open
		return isset($this->open_codes[$this->closing_tags[$closing_tag]]);
	}

	protected function isTag($possible_tag)
	{
		//return isset($bbc[$possible_tag]);
		return isset($this->tags[$possible_tag]);
	}

	protected function isClosingTag($possible_tag)
	{
		return isset($this->closing_tags[$possible_tag]);
	}

	protected function getCodesForTag($tag)
	{
		return $this->codes[$tag];
	}

	// This is used when opening a code. If there is no closing bracket, it's not actually a code.
	protected function getNextClosingBracket($array, $pos)
	{
		return $this->lookAhead($array, $pos, ']');
	}

	protected function lookAhead($array, $pos, $look_for, $count = false)
	{
		$len = $count === false ? count($array) : $count;
		for ($i = $pos + 1; $i < $len; $i++)
		{
			if ($array[$i] === $look_for)
			{
				return $i;
			}
		}

		// Ahead means it can never be less than 1
		return -1;
	}

	// This is pretty much the same as the old parser's findTag()
	protected function findTag($possible_codes, $msg_parts, $pos, $next_closing_bracket)
	{
		$next_pos = $pos + 1;
		list($next_val, $next_offset) = $msg_parts;

		$next_char = $next_val[0];
		$is_equals = $next_char === '=';
		$is_closing_bracket = $next_char === ']';
		$is_forward_slash = $next_char === '/';
		$is_space = $next_char === ' ';

		foreach ($possible_codes as $possible)
		{
			// Let's start by checking the types

			// Check if it's an itemcode
			if ($is_closing_bracket && !empty($possible[Codes::ATTR_ITEMCODE]))
			{
				return $possible;
			}

			// Parameters require a space
			if ($next_char !== ' ' && !empty($possible[Codes::ATTR_PARAM]))
			{
				continue;
			}

			// Any type with COMMAS or EQUALS in it must have an equal sign as the next character
			if ($next_char !== '=' && in_array($possible[Codes::ATTR_TYPE], array(Codes::TYPE_UNPARSED_EQUALS, Codes::TYPE_UNPARSED_COMMAS, Codes::TYPE_UNPARSED_COMMAS_CONTENT, Codes::TYPE_UNPARSED_EQUALS_CONTENT, Codes::TYPE_PARSED_EQUALS)))
			{
				continue;
			}

			// Closed tag?
			// @todo this might actually need substr_compare here
			if ($possible[Codes::ATTR_TYPE] === Codes::TYPE_CLOSED && ())
			{
				continue;
			}

/////********** //

			// A test validation?
			if (isset($possible[Codes::ATTR_TEST]) && preg_match('~^' . $possible[Codes::ATTR_TEST] . '~', substr($this->message, $this->pos + 1 + $possible[Codes::ATTR_LENGTH] + 1)) === 0)
			{
				continue;
			}
			// Do we want parameters?
			elseif (!empty($possible[Codes::ATTR_PARAM]))
			{
				if ($next_c !== ' ')
				{
					continue;
				}
			}
			elseif ($possible[Codes::ATTR_TYPE] !== Codes::TYPE_PARSED_CONTENT)
			{
				// Do we need an equal sign?
				if ($next_c !== '=' && in_array($possible[Codes::ATTR_TYPE], array(Codes::TYPE_UNPARSED_EQUALS, Codes::TYPE_UNPARSED_COMMAS, Codes::TYPE_UNPARSED_COMMAS_CONTENT, Codes::TYPE_UNPARSED_EQUALS_CONTENT, Codes::TYPE_PARSED_EQUALS)))
				{
					continue;
				}
				// Maybe we just want a /...
				if ($next_c !== ']' && $possible[Codes::ATTR_TYPE] === Codes::TYPE_CLOSED && substr_compare($this->message, '/]', $this->pos + 1 + $possible[Codes::ATTR_LENGTH], 2) !== 0 && substr_compare($this->message, ' /]', $this->pos + 1 + $possible[Codes::ATTR_LENGTH], 3) !== 0)
				{
					continue;
				}
				// An immediate ]?
				if ($next_c !== ']' && $possible[Codes::ATTR_TYPE] == Codes::TYPE_UNPARSED_CONTENT)
				{
					continue;
				}
			}
			// parsed_content demands an immediate ] without parameters!
			elseif ($possible[Codes::ATTR_TYPE] === Codes::TYPE_PARSED_CONTENT)
			{
				if ($next_c !== ']')
				{
					continue;
				}
			}

			// Check allowed tree?
			if (isset($possible[Codes::ATTR_REQUIRE_PARENTS]) && ($this->inside_tag === null || !in_array($this->inside_tag[Codes::ATTR_TAG], $possible[Codes::ATTR_REQUIRE_PARENTS])))
			{
				continue;
			}
			elseif (isset($this->inside_tag[Codes::ATTR_REQUIRE_CHILDREN]) && !in_array($possible[Codes::ATTR_TAG], $this->inside_tag[Codes::ATTR_REQUIRE_CHILDREN]))
			{
				continue;
			}
			// If this is in the list of disallowed child tags, don't parse it.
			elseif (isset($this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN]) && in_array($possible[Codes::ATTR_TAG], $this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN]))
			{
				continue;
			}
			// Not allowed in this parent, replace the tags or show it like regular text
			elseif (isset($possible[Codes::ATTR_DISALLOW_PARENTS]) && ($this->inside_tag !== null && in_array($this->inside_tag[Codes::ATTR_TAG], $possible[Codes::ATTR_DISALLOW_PARENTS])))
			{
				if (!isset($possible[Codes::ATTR_DISALLOW_BEFORE], $possible[Codes::ATTR_DISALLOW_AFTER]))
				{
					continue;
				}
				$possible[Codes::ATTR_BEFORE] = isset($possible[Codes::ATTR_DISALLOW_BEFORE]) ? $tag[Codes::ATTR_DISALLOW_BEFORE] : $possible[Codes::ATTR_BEFORE];
				$possible[Codes::ATTR_AFTER] = isset($possible[Codes::ATTR_DISALLOW_AFTER]) ? $tag[Codes::ATTR_DISALLOW_AFTER] : $possible[Codes::ATTR_AFTER];
			}

			$this->pos1 = $this->pos + 1 + $possible[Codes::ATTR_LENGTH] + 1;

			// This is long, but it makes things much easier and cleaner.
			if (!empty($possible[Codes::ATTR_PARAM]))
			{
				$match = $this->oldMatchParameters($possible, $matches);

				// Didn't match our parameter list, try the next possible.
				if (!$match)
				{
					continue;
				}

				$tag = $this->setupTagParameters($possible, $matches);
			}
			else
			{
				$tag = $possible;
			}

			// Quotes can have alternate styling, we do this php-side due to all the permutations of quotes.
			if ($tag[Codes::ATTR_TAG] === 'quote')
			{
				// Start with standard
				$quote_alt = false;
				foreach ($this->open_tags as $open_quote)
				{
					// Every parent quote this quote has flips the styling
					if ($open_quote[Codes::ATTR_TAG] === 'quote')
					{
						$quote_alt = !$quote_alt;
					}
				}
				// Add a class to the quote to style alternating blockquotes
				// @todo - Frankly it makes little sense to allow alternate blockquote
				// styling without also catering for alternate quoteheader styling.
				// I do remember coding that some time back, but it seems to have gotten
				// lost somewhere in the Elk processes.
				// Come to think of it, it may be better to append a second class rather
				// than alter the standard one.
				//  - Example: class="bbc_quote" and class="bbc_quote alt_quote".
				// This would mean simpler CSS for themes (like default) which do not use the alternate styling,
				// but would still allow it for themes that want it.
				$tag[Codes::ATTR_BEFORE] = str_replace('<blockquote>', '<blockquote class="bbc_' . ($quote_alt ? 'alternate' : 'standard') . '_quote">', $tag[Codes::ATTR_BEFORE]);
			}

			break;
		}

		return $tag;
	}

	protected function implodeChunk($array, $glue, $start, $end, $len = false)
	{
		$string = '';
		$len = $len === false ? count($array) : $len;
		for ($i = $start; $i < $end && $i < $len; $i++)
		{
			$string .= $array[$i] . $glue;
		}

		return $string;
	}
}