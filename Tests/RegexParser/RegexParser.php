<?php

/**
 *
 * @name      ElkArte Forum
 * @copyright ElkArte Forum contributors
 * @license   BSD http://opensource.org/licenses/BSD-3-Clause
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:		BSD, See included LICENSE.TXT for terms and conditions.
 *
 *
 */

// @todo change to \StringParser\BBC
namespace BBC;

use \BBC\Codes;

/**
 * A tag is the name of a code. Like [url]. The tag is "url".
 * A code is the instructions, including the name (tag).
 * Each tag can have many codes
 */
class RegexParser
{
	protected $message;
	protected $return = '';
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
	protected $next_closing_bracket = -1;
	protected $do_smileys = true;
	protected $open_codes = array();
	protected $last_open_code = array();

	// Not at all how I want to do this. Just for testing
	protected $last_pos = 0;
	protected $pos = 0;
	protected $token_regex;


	public function __construct(RegexCodes $bbc)
	{
		$this->bbc = $bbc;

		$default = $bbc->getDefault();
		foreach ($default as $code)
		{
			$bbc->add($code, true);
		}

		$this->codes = $bbc->getCodes();
		$this->tags = $bbc->getTags();
		$this->opening_tags = $bbc->getOpeningTags();
		$this->closing_tags = $bbc->getClosingTags();

		$this->item_codes = $this->bbc->getItemCodes();
		$this->token_regex = $this->bbc->getTokenRegex();
	}

	public function resetParser()
	{
		//$this->tags = null;
		$this->pos = null;
		$this->pos1 = null;
		$this->pos2 = null;
		$this->last_pos = null;
		//$this->open_codes = new \SplStack;
		$this->open_codes = array();
		//$this->open_bbc = new \SplStack;
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

		$this->msg_parts = $msg_parts;
		$this->num_parts = count($msg_parts);
		unset($msg_parts);

		$break = false;

		// don't use a foreach so we can jump around the array without worrying about a cursor
		for ($num_parts = count($this->msg_parts), $this->pos = 0; $this->pos < $num_parts && !$break; $this->pos++)
		{
			list($part, $offset) = $this->msg_parts[$this->pos];

			// Need to lowercase the part if it is a tag
			if ($part[0] === '[')
			{
				// Remember: don't use $part if this isn't a tag. Use $msg_parts because $part has been modified.
				// @todo add a message/test that will check different cased bbc
				$part = strtolower($part);
			}
			// I think this makes sense because we really only want to work with tags. Then parse everything in between.
			else
			{
				// @todo maybe combine the array values to a string now?
				continue;
			}

			// @todo this needs to get rid of the substr. Better to just make the array account for [ and [/
			// What if we just searched for [ and then checked if the next element is a code?
			//$possible_tag = substr($part, 1);

			// This could also be done inside the isTag() block but that would mean doing a lot of looping.
			// I think this is better but it means it will still work if you don't have a closing tag.
			$possible_closer = isset($this->closing_tags[$part]);

			// If it's a closer, check if there is a matching opening tag.
			// If there was an opening tag, $this->closeCode();
			// If it wasn't a corresponding opening tag, continue;
			// Starts with a /, has an open tag, that tag matches our possible tag, and the closing bracket is next...
			// We have a closer!
			if ($possible_closer
				// This won't work if we have mangled BBC. Maybe just check if it is a closer and then close anything that isn't the last one?
				&& !empty($this->last_open_code[0]) && '[/' . $this->last_open_code[0][Codes::ATTR_TAG] . ']' === $part
			)
			{
				// When we close is actually when we handle all of the parsing like autolink, smilies, before/after/content
				// Then we put it in to a message string
				// Since we don't need to go backwards, we can pop off the previous array elements to save space
				$this->closeCode();
				continue;
			}

			if ($this->isTag($part))
			{
				// Don't open the tag yet, we need to look ahead and see if there is a ]
				// We might even already know where it's at
				//$next_closing_bracket = $next_closing_bracket > $this->pos ? $next_closing_bracket : $this->findNextClosingBracket();

				//if ($next_closing_bracket !== -1)
				if ($this->findNextClosingBracket() !== -1)
				{
					// Okay, open the tag now.
					$tag = $this->findTag($this->codes[$part], $this->msg_parts, $this->pos, $this->next_closing_bracket);

					// No tag found
					if ($tag === null)
					{
						continue;
					}

					// Itemcodes are tags too. Just very different
					if (!empty($tag[Codes::TYPE_ITEMCODE]))
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

		return $this->return;
	}

	// This needs to check if the closing bracket is inside of a quoted area.
	protected function findNextClosingBracket()
	{
		$in_quote = false;
		for ($i = $this->pos + 1; $i < $this->num_parts; $i++)
		{
			$part = $this->msg_parts[$i][0];

			// Skip ahead if we are inside of a quoted area
			if ($part === '&quot;')
			{
				$in_quote = !$in_quote;

				if ($in_quote)
				{
					continue;
				}
			}

			if ($part === ']')
			{
				return $this->next_closing_bracket = $i;
			}
		}

		// Ahead means it can never be less than 1
		return $this->next_closing_bracket = -1;
	}

	public function tokenize($message)
	{
		$msg_parts = preg_split($this->token_regex, $message, null, PREG_SPLIT_OFFSET_CAPTURE | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		/*var_dump(
		//$this->bbc_codes,
		//array_keys($this->bbc_codes),
			//$this->bbc->getTags(),
			//$split_chars,
			$split_string,
			$msg_parts
		);*/

		return $msg_parts;
	}

	protected function openCode($code)
	{
		$this->last_open_code = [$code, $this->next_closing_bracket + 1];

		// Not how I want to do it
		$this->last_pos = $this->next_closing_bracket;

		$this->open_codes[$this->next_closing_bracket] = $code;

		// @todo set disallowed children
	}

	protected function closeCode()
	{
		$this->handleTag($this->last_open_code[0]);

		// Remove the parts we already processed?
	}

	protected function handleTag($tag)
	{
		switch ($tag[Codes::ATTR_TYPE])
		{
			case Codes::TYPE_PARSED_CONTENT:
				return $this->handleTypeParsedContext($tag);

			// Don't parse the content, just skip it.
			case Codes::TYPE_UNPARSED_CONTENT:
				return $this->handleTypeUnparsedContext($tag);

			// Don't parse the content, just skip it.
			case Codes::TYPE_UNPARSED_EQUALS_CONTENT:
				return $this->handleUnparsedEqualsContext($tag);

			// A closed tag, with no content or value.
			case Codes::TYPE_CLOSED:
				return $this->handleTypeClosed($tag);

			// This one is sorta ugly... :/
			case Codes::TYPE_UNPARSED_COMMAS_CONTENT:
				return $this->handleUnparsedCommasContext($tag);

			// This has parsed content, and a csv value which is unparsed.
			case Codes::TYPE_UNPARSED_COMMAS:
				return $this->handleUnparsedCommas($tag);

			// A tag set to a value, parsed or not.
			case Codes::TYPE_PARSED_EQUALS:
			case Codes::TYPE_UNPARSED_EQUALS:
				return $this->handleEquals($tag);
		}

		return false;
	}

	protected function handleTypeParsedContext($tag)
	{
//var_dump($this->msg_parts, '', $this->last_pos, $this->pos);
		$chunk = $this->implodeChunk($this->msg_parts, '', $this->next_closing_bracket + 1, $this->pos);

		// Autolink
		// Smilies

		$this->return .= $tag[Codes::ATTR_BEFORE] . $chunk . $tag[Codes::ATTR_AFTER];

		//var_dump('aaaa', $this->msg_parts, '', $this->last_pos, $this->pos);

		return false;
		// @todo Check for end tag first, so people can say "I like that [i] tag"?
		$this->addOpenTag($tag);
		//$this->message = substr($this->message, 0, $this->pos) . "\n" . $tag[Codes::ATTR_BEFORE] . "\n" . substr($this->message, $this->pos1);
		//$this->message = substr_replace($this->message, "\n" . $tag[Codes::ATTR_BEFORE] . "\n", $this->pos, $this->pos1 - $this->pos);
		$tmp = $this->noSmileys($tag[Codes::ATTR_BEFORE]);
		$this->message = substr_replace($this->message, $tmp, $this->pos, $this->pos1 - $this->pos);
		//$this->pos += strlen($tag[Codes::ATTR_BEFORE]) + 1;
		$this->pos += strlen($tmp) - 1;

		return false;
	}

	protected function parseContext($start, $end)
	{
		$context = implodeChunk($this->msg_parts, '', $start, $end);
	}

	protected function isOpen($closing_tag)
	{
		// This isn't what I actually want. I want to check if it is open and is the last code open
		return isset($this->open_codes[$this->closing_tags[$closing_tag]]);
	}

	protected function isTag($possible_tag)
	{
		//return isset($bbc[$possible_tag]);
		return isset($this->opening_tags[$possible_tag]);
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

	// @todo make case-insensitive. Maybe change to "findTagAhead()" or something so we check for [ first
	protected function lookAhead($array, $pos, $look_for, $count = false)
	{
		$len = $count === false ? count($array) : $count;

		for ($i = $pos + 1; $i < $len; $i++)
		{
			if ($array[$i][0] === $look_for)
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
		$tag = null;
		$next_pos = $pos + 1;
		list($next_val, $next_offset) = $msg_parts[$next_pos];

		$next_char = $next_val[0];
		$is_equals = $next_char === '=';
		$is_closing_bracket = $next_char === ']';
		$is_forward_slash = $next_char === '/';
		$is_space = $next_char === ' ';

		foreach ($possible_codes as $possible)
		{
			// Let's start by checking the types

			// Check if it's an itemcode
			if (!$is_closing_bracket && !empty($possible[Codes::TYPE_ITEMCODE]))
			{
				return $possible;
			}

			// Parameters require a space
			if (!$is_space && !empty($possible[Codes::ATTR_PARAM]))
			{
				continue;
			}

			// Any type with COMMAS or EQUALS in it must have an equal sign as the next character
			if (!$is_equals && in_array($possible[Codes::ATTR_TYPE], array(Codes::TYPE_UNPARSED_EQUALS, Codes::TYPE_UNPARSED_COMMAS, Codes::TYPE_UNPARSED_COMMAS_CONTENT, Codes::TYPE_UNPARSED_EQUALS_CONTENT, Codes::TYPE_PARSED_EQUALS)))
			{
				continue;
			}

			// Closed tag?
			// @todo this might actually need substr_compare here
			//if ($possible[Codes::ATTR_TYPE] === Codes::TYPE_CLOSED && ())
			{
				//	continue;
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
				if ($next_char !== ' ')
				{
					continue;
				}
			}
			elseif ($possible[Codes::ATTR_TYPE] !== Codes::TYPE_PARSED_CONTENT)
			{
				// Do we need an equal sign?
				if ($next_char !== '=' && in_array($possible[Codes::ATTR_TYPE], array(Codes::TYPE_UNPARSED_EQUALS, Codes::TYPE_UNPARSED_COMMAS, Codes::TYPE_UNPARSED_COMMAS_CONTENT, Codes::TYPE_UNPARSED_EQUALS_CONTENT, Codes::TYPE_PARSED_EQUALS)))
				{
					continue;
				}
				// Maybe we just want a /...
				if ($next_char !== ']' && $possible[Codes::ATTR_TYPE] === Codes::TYPE_CLOSED && substr_compare($this->message, '/]', $this->pos + 1 + $possible[Codes::ATTR_LENGTH], 2) !== 0 && substr_compare($this->message, ' /]', $this->pos + 1 + $possible[Codes::ATTR_LENGTH], 3) !== 0)
				{
					continue;
				}
				// An immediate ]?
				if ($next_char !== ']' && $possible[Codes::ATTR_TYPE] == Codes::TYPE_UNPARSED_CONTENT)
				{
					continue;
				}
			}
			// parsed_content demands an immediate ] without parameters!
			elseif ($possible[Codes::ATTR_TYPE] === Codes::TYPE_PARSED_CONTENT)
			{
				if ($next_char !== ']')
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
//		var_dump($tag);

		return $tag;
	}

	public function parsingEnabled()
	{
		return !empty($GLOBALS['modSettings']['enableBBC']);
	}

	protected function closeRemainingCodes()
	{

	}

	protected function implodeChunk(array $array, $glue, $start, $end, $len = false)
	{
		$string = '';
		$len = $len === false ? count($array) : $len;
		for ($i = $start; $i < $end && $i < $len; $i++)
		{
			$string .= $array[$i][0] . $glue;
		}

		return $string;
	}
}