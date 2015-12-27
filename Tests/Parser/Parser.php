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
use \BBC\Autolink;

//define('BR', '<br />');
//define('BR_LEN', strlen(BR));

// Anywhere you see - 1 + 2 it's because you get rid of the ] and add 2 \n

class Parser
{
	const MAX_PERMUTE_ITERATIONS = 5040;

	protected $message;
	protected $bbc;
	// @todo are all of these pos properties necessary? Seems like most of them are purely local and the rest can probably be shared
	protected $pos;
	protected $param_start_pos;
	protected $last_pos;
	protected $do_smileys = true;
	protected $open_tags = array();
	// This is the actual tag that's open
	protected $inside_tag;

	protected $autolinker;
	protected $possible_html;
	protected $html_parser;

	protected $can_cache = true;
	protected $num_footnotes = 0;
	protected $smiley_marker = "\r";

	protected $tracked_content = array();

	protected $html_enabled = false;
	protected $autolink_enabled = true;

	/**
	 * @param \BBC\Codes $bbc
	 */
	public function __construct(Codes $bbc, Autolink $autolinker = null, HtmlParser $html_parser = null)
	{
		$this->bbc = $bbc;

		$this->bbc->getForParsing();

		$this->autolinker = $autolinker;
		$this->loadAutolink();

		$this->html_parser = $html_parser;
	}

	/**
	 * Reset the parser's properties for a new message
	 */
	public function resetParser()
	{
		$this->pos = -1;
		$this->param_start_pos = null;
		$this->last_pos = null;
		$this->open_tags = array();
		$this->inside_tag = null;
		$this->can_cache = true;
		$this->num_footnotes = 0;
		$this->has_bbc = false;
		$this->tracked_content = array();
	}

	/**
	 * Check if the message has BBC
	 */
	public function hasBBC()
	{
		return $this->has_bbc;
	}

	/**
	 * Check if the message could possibly contain BBC
	 */
	public function hasPossibleBBC()
	{
		// Does it have a [
		$open_bracket = strpos($this->message, '[');
		if ($open_bracket === false)
		{
			return false;
		}

		// Does it have a ]
		$close_bracket = strpos($this->message, ']');
		if ($close_bracket === false)
		{
			return false;
		}

		// Is the ] after the [
		// Is the difference >= 1
		if ($close_bracket - $open_bracket >= 1)
		{
			return false;
		}

		return true;
	}

	/**
	 * Set whether HTML can be parsed
	 *
	 * @param bool $toggle
	 */
	public function canParseHTML($toggle)
	{
		$this->html_enabled = (bool) $toggle;
	}

	/**
	 * Set whether autolinks should be parsed
	 *
	 * @param bool $toggle
	 */
	public function canParseAutolink($toggle)
	{
		$this->autolink_enabled = (bool) $toggle;
	}

	/**
	 * Parse the BBC in a string/message
	 *
	 * @param string $message
	 *
	 * @return string
	 */
	public function parse($message)
	{
		// The parser allows you to check some things about the message.
		// If you move this later, you might be talking about the last message.
		$this->resetParser();

		$this->triggerEvent('pre_parsebbc', array(&$message, $this->bbc));

		// Don't waste cycles
		if ($message === '')
		{
			return '';
		}

		// Clean up any cut/paste issues we may have
		$message = sanitizeMSCutPaste($message);

		$this->message = $message;
		unset($message);

		// @todo change this to <br> (it will break tests)
		$this->message = str_replace("\n", '<br />', $this->message);

		// Check if the message might have a link or email to save a bunch of parsing in autolink()
		if ($this->autolink_enabled)
		{
			$this->autolinker->setPossibleAutolink($this->message);
		}

		$this->possible_html = $this->html_enabled && strpos($this->message, '&lt;') !== false;

		// Don't load the HTML Parser unless we have to
		if ($this->possible_html && $this->html_parser === null)
		{
			$this->loadHtmlParser();
		}

		// This handles pretty much all of the parsing. It is a separate method so it is easier to override and profile.
		$this->parse_loop();

		// Close any remaining tags.
		while ($tag = $this->closeOpenedTag())
		{
			$this->message .= $this->noSmileys($tag[Codes::ATTR_AFTER]);
		}

		if (isset($this->message[0]) && $this->message[0] === ' ')
		{
			$this->message = substr_replace($this->message, '&nbsp;', 0, 1);
		}

		// Cleanup whitespace.
		$this->message = str_replace(array('  ', '<br /> ', '&#13;'), array('&nbsp; ', '<br />&nbsp;', "\n"), $this->message);

		// Finish footnotes if we have any.
		if ($this->num_footnotes > 0)
		{
			$this->handleFootnotes();
		}

		// Allow addons access to what the parser created
		$message = $this->message;
		$this->triggerEvent('post_parsebbc', array(&$message));
		$this->message = $message;

		return $this->message;
	}

	protected function parse_loop()
	{
		//while ($this->pos !== false)
		// @todo I changed this because I can't find a test that makes this infinite. If I do, I'll change it back
		while(true)
		{
			//$this->last_pos = isset($this->last_pos) ? max($this->pos, $this->last_pos) : $this->pos;
			$this->last_pos = max($this->pos, $this->last_pos);
			$this->pos = strpos($this->message, '[', $this->pos + 1);

			// Failsafe.
			if ($this->pos === false || $this->last_pos > $this->pos)
			{
				// Pretty much means: do betweenTags() and return;
				$this->pos = strlen($this->message) + 1;
			}

			// This should be much later. Like when we actually find a closing tag or when we find a new opening tag
			// The thing about this call that takes a while is running the regular expressions. If we increase the text per call that will decrease the cost
			$this->betweenTags();

			// Are we there yet?  Are we there yet?
			// if betweenTags() makes changes, it will move the pos back. When it adds a [url] from the autolinker, for instance
			// it needs to go back to the start of that and parse that again
			if ($this->pos >= strlen($this->message) - 1)
			{
				// We are at the last character (or greater than it when things get chopped)
				return;
			}

			$next_char = strtolower($this->message[$this->pos + 1]);

			// Possibly a closer?
			if ($next_char === '/')
			{
				if($this->hasOpenTags())
				{
					$this->handleOpenTags();
				}

				// We don't allow / to be used for anything but the closing character, so this can't be a tag
				continue;
			}

			// No tags for this character, so just keep going (fastest possible course.)
			if (!$this->bbc->hasChar($next_char))
			{
				continue;
			}

			$this->inside_tag = !$this->hasOpenTags() ? null : $this->getLastOpenedTag();

			// Is it an itemcode?
			if ($this->bbc->getItemCode($next_char) !== null && isset($this->message[$this->pos + 2]) && $this->message[$this->pos + 2] === ']' && !$this->bbc->isDisabled('list') && !$this->bbc->isDisabled('li'))
			{
				// Itemcodes cannot be 0 and must be preceeded by a semi-colon, space, tab, new line, or greater than sign
				if (!($this->message[$this->pos + 1] === '0' && !in_array($this->message[$this->pos - 1], array(';', ' ', "\t", "\n", '>'))))
				{
					// Item codes are complicated buggers... they are implicit [li]s and can make [list]s!
					$this->handleItemCode();
				}

				// No matter what, we have to continue here.
				continue;
			}
			else
			{
				$code = $this->findCode($this->bbc->getCodesByChar($next_char));
			}

			// Implicitly close lists and tables if something other than what's required is in them.
			// This is needed for itemcode.
			if ($code === null && $this->inside_tag !== null && !empty($this->inside_tag[Codes::ATTR_REQUIRE_CHILDREN]))
			{
				$this->closeOpenedTag();
				$this->addStringAtCurrentPos($this->inside_tag[Codes::ATTR_AFTER], 0);
				$this->pos--;
			}

			// No tag?  Keep looking, then.  Silly people using brackets without actual tags.
			if ($code === null)
			{
				continue;
			}

			$this->setDisallowedChildren($code);

			// Is this tag disabled?
			if ($this->bbc->isDisabled($code[Codes::ATTR_TAG]))
			{
				$this->handleDisabled($code);
			}

			// The only special case is 'html', which doesn't need to close things.
			if ($code[Codes::ATTR_BLOCK_LEVEL] && $code[Codes::ATTR_TAG] !== 'html' && !$this->inside_tag[Codes::ATTR_BLOCK_LEVEL])
			{
				$this->closeNonBlockLevel();
			}

			// This is the part where we actually handle the tags. I know, crazy how long it took.
			if($this->handleCode($code))
			{
				continue;
			}

			// If this is block level, eat any breaks after it.
			if ($code[Codes::ATTR_BLOCK_LEVEL] && isset($this->message[$this->pos + 1]) && substr_compare($this->message, '<br />', $this->pos + 1, 6) === 0)
			{
				$this->message = substr_replace($this->message, '', $this->pos + 1, 6);
			}

			// Are we trimming outside this tag?
			if (!empty($code[Codes::ATTR_TRIM]) && $code[Codes::ATTR_TRIM] !== Codes::TRIM_OUTSIDE)
			{
				$this->trimWhiteSpace($this->message, $this->pos + 1);
			}
		}
	}

	protected function setDisallowedChildren(array &$code)
	{
		// Propagate the list to the child (so wrapping the disallowed tag won't work either.)
		if (isset($this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN]))
		{
			$code[Codes::ATTR_DISALLOW_CHILDREN] = isset($code[Codes::ATTR_DISALLOW_CHILDREN]) ? $code[Codes::ATTR_DISALLOW_CHILDREN] + $this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN] : $this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN];
		}
	}

	protected function handleOpenTags()
	{
		// Next closing bracket after the first character
		$closing_bracket_pos = strpos($this->message, ']', $this->pos + 1);

		// Playing games? string = [/]
		if ($closing_bracket_pos === $this->pos + 2)
		{
			return;
		}

		// Get everything between [/ and ]
		$look_for = strtolower(substr($this->message, $this->pos + 2, $closing_bracket_pos - $this->pos - 2));
		$to_close = array();
		$block_level = null;

		// Find the code. Should be in order but sometimes it's not
		do
		{
			// Get the last opened code
			$code = $this->closeOpenedTag();

			// No open tags
			if (!$code)
			{
				break;
			}

			if ($code[Codes::ATTR_BLOCK_LEVEL])
			{
				// Only find out if we need to.
				if ($block_level === false)
				{
					$this->addOpenTag($code);
					// If one of the previous/child tags is block level and this one isn't, something is screwed up.
					break;
				}

				// The idea is, if we are LOOKING for a block level tag, we can close them on the way.
				if (isset($look_for[1]) && $this->bbc->hasChar($look_for[0]))
				{
					foreach ($this->bbc->getCodesByChar($look_for[0]) as $temp)
					{
						if ($temp[Codes::ATTR_TAG] === $look_for)
						{
							$block_level = $temp[Codes::ATTR_BLOCK_LEVEL];
							break;
						}
					}
				}

				if ($block_level !== true)
				{
					$block_level = false;
					$this->addOpenTag($code);
					break;
				}
			}

			$to_close[] = $code;
		} while ($code[Codes::ATTR_TAG] !== $look_for);

		// Did we just eat through everything and not find it?
		if (!$this->hasOpenTags() && (empty($code) || $code[Codes::ATTR_TAG] !== $look_for))
		{
			// The opened code can't be found
			$this->open_tags = $to_close;
			return;
		}
		elseif (!empty($to_close) && $code[Codes::ATTR_TAG] !== $look_for)
		{
			if ($block_level === null && isset($look_for[0]) && $this->bbc->hasChar($look_for[0]))
			{
				foreach ($this->bbc->getCodesByChar($look_for[0]) as $temp)
				{
					if ($temp[Codes::ATTR_TAG] === $look_for)
					{
						$block_level = !empty($temp[Codes::ATTR_BLOCK_LEVEL]);
						break;
					}
				}
			}

			// We're not looking for a block level tag (or maybe even a tag that exists...)
			if (!$block_level)
			{
				foreach ($to_close as $code)
				{
					$this->addOpenTag($code);
				}

				return;
			}
		}

		// This is where we actually close out the tags
		foreach ($to_close as $code)
		{
			if (!empty($code[Codes::TRACKED_CONTENT]))
			{
				$this->endTrackedContent($code, $this->pos);
			}

			$this->addStringAtCurrentPos($code[Codes::ATTR_AFTER], $closing_bracket_pos + 1 - $this->pos);
			$closing_bracket_pos = $this->pos - 1;

			// See the comment at the end of the big loop - just eating whitespace ;).
			if ($code[Codes::ATTR_BLOCK_LEVEL] && isset($this->message[$this->pos]) && substr_compare($this->message, '<br />', $this->pos, 6) === 0)
			{
				$this->message = substr_replace($this->message, '', $this->pos, 6);
			}

			// Trim inside whitespace
			if (!empty($code[Codes::ATTR_TRIM]) && $code[Codes::ATTR_TRIM] !== Codes::TRIM_INSIDE)
			{
				$this->trimWhiteSpace($this->message, $this->pos + 1);
			}
		}

		if (!empty($to_close))
		{
			$this->pos--;
		}
	}

	/**
	 * Turn smiley parsing on/off
	 * @param bool $toggle
	 * @return \BBC\Parser
	 */
	public function doSmileys($toggle)
	{
		$this->do_smileys = (bool) $toggle;
		return $this;
	}

	/**
	 * Load the HTML parser
	 */
	public function loadHtmlParser()
	{
		$parser = new HtmlParser;
		$this->triggerEvent('bbc_load_html_parser', array(&$parser));
		$this->html_parser = $parser;
	}

	/**
	 * Parse the HTML in a string
	 *
	 * @param string &$data
	 */
	protected function parseHTML(&$data)
	{
		$this->html_parser->parse($data);
	}

	/**
	 * Parse URIs and email addresses in a string to url and email BBC tags to be parsed by the BBC parser
	 *
	 * @param string &$data
	 */
	protected function autoLink(&$data)
	{
		if ($data === ''
			|| $data === $this->smiley_marker
			|| !$this->autolinker->hasPossible()
			// Are we inside tags that should be auto linked?
			|| !$this->insideAutolinkArea())
		{
			return;
		}

		$this->autolinker->parse($data);
	}

	protected function insideAutolinkArea()
	{
		if ($this->hasOpenTags())
		{
			foreach ($this->getOpenedTags() as $open_tag)
			{
				if (!$open_tag[Codes::ATTR_AUTOLINK])
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Load the autolink regular expression to be used in autoLink()
	 */
	protected function loadAutolink()
	{
		if ($this->autolinker === null)
		{
			$this->autolinker = new Autolink($this->bbc);
		}
	}

	/**
	 * Find if the current character is the start of a tag and get it
	 *
	 * @param array[] $possible_codes
	 *
	 * @return null|array the tag that was found or null if no tag found
	 */
	protected function findCode(array $possible_codes)
	{
		$tag = null;
		$last_check = null;

		foreach ($possible_codes as $possible)
		{
			// Skip tags that didn't match the next X characters
			if ($possible[Codes::ATTR_TAG] === $last_check)
			{
				continue;
			}

			// The character after the possible tag or nothing
			$next_char = isset($this->message[$this->pos + 1 + $possible[Codes::ATTR_LENGTH]]) ? $this->message[$this->pos + 1 + $possible[Codes::ATTR_LENGTH]] : '';

			// This only happens if the tag is the last character of the string
			if ($next_char === '')
			{
				break;
			}

			// The next character must be one of these or it's not a tag
			if ($next_char !== ' ' && $next_char !== ']' && $next_char !== '=' && $next_char !== '/')
			{
				$last_check = $possible[Codes::ATTR_TAG];
				continue;
			}

			// Not a match?
			if (substr_compare($this->message, $possible[Codes::ATTR_TAG], $this->pos + 1, $possible[Codes::ATTR_LENGTH]) !== 0)
			{
				$last_check = $possible[Codes::ATTR_TAG];
				continue;
			}

			// @todo maybe sort the BBC by length descending. If the message stub is a tag and the length changes, no need to continue. Just break by this point.

			$tag = $this->checkCodeAttributes($next_char, $possible, $tag);
			if($tag === null)
			{
				continue;
			}

			break;
		}

// @todo remove this. This is only for testing
//$GLOBALS['codes_used'][$GLOBALS['current_message']][] = $tag;
//$GLOBALS['codes_used_count'][$GLOBALS['current_message']][serialize($tag)] = isset($GLOBALS['codes_used_count'][$GLOBALS['current_message']][serialize($tag)]) ? $GLOBALS['codes_used_count'][$GLOBALS['current_message']][serialize($tag)] + 1 : 1;

		// If there is a code that says you can't cache, the message can't be cached
		if ($tag !== null && $this->can_cache !== false)
		{
			$this->can_cache = empty($tag[Codes::ATTR_NO_CACHE]);
		}

		if ($tag[Codes::ATTR_TAG] === 'footnote')
		{
			$this->num_footnotes++;
		}

		return $tag;
	}

	/**
	 * @param array &$code
	 * @param int $pos
	 */
	protected function startTrackedContent(array &$code, $pos)
	{
		$tag = $code[Codes::ATTR_TAG];
		if (!isset($this->tracked_content[$tag]))
		{
			$this->tracked_content[$tag] = array();
		}

		$code[Codes::TRACKED_CONTENT] = array('start' => $pos);

		$this->tracked_content[$tag][] = &$code;
	}

	/**
	 * @param array &$code
	 * @param int $pos
	 * @param bool|true $capture_content
	 */
	protected function endTrackedContent(array &$code, $pos, $capture_content = true)
	{
		$code[Codes::TRACKED_CONTENT]['end'] = $pos;

		if ($capture_content)
		{
			$start = $code[Codes::TRACKED_CONTENT]['start'];
			$end   = $code[Codes::TRACKED_CONTENT]['end'];

			$code[Codes::TRACKED_CONTENT]['content'] = substr($this->message, $start, $end - $start);
		}
	}

	public function getTrackedContent($tag = null)
	{
		if ($tag === null)
		{
			return $this->tracked_content;
		}
		else
		{
			return isset($this->tracked_content[$tag]) ? $this->tracked_content[$tag] : array();
		}
	}

	public function getTrackedContentCount($tag)
	{
		return isset($this->tracked_content[$tag]) ? $this->tracked_content[$tag] : 0;
	}

	/**
	 * Check if the current position matches the possible code attributes
	 *
	 * @param string $next_char
	 * @param array $possible
	 * @return array|void The possible code if it matches
	 */
	protected function checkCodeAttributes($next_char, array $possible)
	{
		// Do we want parameters?
		if (!empty($possible[Codes::ATTR_PARAM]))
		{
			if ($next_char !== ' ')
			{
				return;
			}
		}
		// parsed_content demands an immediate ] without parameters!
		elseif ($possible[Codes::ATTR_TYPE] === Codes::TYPE_PARSED_CONTENT)
		{
			if ($next_char !== ']')
			{
				return;
			}
		}
		else
		{
			// Do we need an equal sign?
			if ($next_char !== '=' && in_array($possible[Codes::ATTR_TYPE], array(Codes::TYPE_UNPARSED_EQUALS, Codes::TYPE_UNPARSED_COMMAS, Codes::TYPE_UNPARSED_COMMAS_CONTENT, Codes::TYPE_UNPARSED_EQUALS_CONTENT, Codes::TYPE_PARSED_EQUALS)))
			{
				return;
			}

			if ($next_char !== ']')
			{
				// An immediate ]?
				if ($possible[Codes::ATTR_TYPE] === Codes::TYPE_UNPARSED_CONTENT)
				{
					return;
				}
				// Maybe we just want a /...
				elseif ($possible[Codes::ATTR_TYPE] === Codes::TYPE_CLOSED && substr_compare($this->message, '/]', $this->pos + 1 + $possible[Codes::ATTR_LENGTH], 2) !== 0 && substr_compare($this->message, ' /]', $this->pos + 1 + $possible[Codes::ATTR_LENGTH], 3) !== 0)
				{
					return;
				}
			}
		}


		// Check allowed tree?
		if (isset($possible[Codes::ATTR_REQUIRE_PARENTS]) && ($this->inside_tag === null || !isset($possible[Codes::ATTR_REQUIRE_PARENTS][$this->inside_tag[Codes::ATTR_TAG]])))
		{
			return;
		}

		if ($this->inside_tag !== null)
		{
			// @todo this is effectively a whitelist. Is that what we want here?
			if (isset($this->inside_tag[Codes::ATTR_REQUIRE_CHILDREN]) && !isset($this->inside_tag[Codes::ATTR_REQUIRE_CHILDREN][$possible[Codes::ATTR_TAG]]))
			{
				return;
			}

			// If this is in the list of disallowed child tags, don't parse it.
			if (isset($this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN]) && isset($this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN][$possible[Codes::ATTR_TAG]]))
			{
				return;
			}

			// Not allowed in this parent, replace the tags or show it like regular text
			if (isset($possible[Codes::ATTR_DISALLOW_PARENTS]) && isset($possible[Codes::ATTR_DISALLOW_PARENTS][$this->inside_tag[Codes::ATTR_TAG]]))
			{
				if (!isset($possible[Codes::ATTR_DISALLOW_BEFORE], $possible[Codes::ATTR_DISALLOW_AFTER]))
				{
					return;
				}

				$possible[Codes::ATTR_BEFORE] = isset($possible[Codes::ATTR_DISALLOW_BEFORE]) ? $possible[Codes::ATTR_DISALLOW_BEFORE] : $possible[Codes::ATTR_BEFORE];
				$possible[Codes::ATTR_AFTER] = isset($possible[Codes::ATTR_DISALLOW_AFTER]) ? $possible[Codes::ATTR_DISALLOW_AFTER] : $possible[Codes::ATTR_AFTER];
			}
		}

		// Only done between = and ]
		if (isset($possible[Codes::ATTR_TEST]) && $this->handleTestAttribute($possible))
		{
			return;
		}

		// +1 for [, then the length of the tag, then a space
		$this->param_start_pos = $this->pos + 1 + $possible[Codes::ATTR_LENGTH] + 1;

		// This is long, but it makes things much easier and cleaner.
		if (!empty($possible[Codes::ATTR_PARAM]))
		{
			$matches = $this->matchParameters($possible);

			// Didn't match our parameter list, try the next possible.
			if ($matches === array())
			{
				return;
			}

			// @todo this is no longer the start position. This line turns it in to the end position
			$this->param_start_pos += strlen($matches[0]) - 1;

			return $this->setupTagParameters($possible, $this->getParameters($possible, $matches));
		}

		return $possible;
	}

	/**
	 * Handle Codes::ATTR_TEST
	 * Checks if the content after the equals but before the ] matches the regex in Codes::ATTR_TEST
	 * @param array $possible
	 * @return bool
	 */
	protected function handleTestAttribute(array $possible)
	{
		return preg_match('~^' . $possible[Codes::ATTR_TEST] . '~', substr($this->message, $this->pos + 2 + $possible[Codes::ATTR_LENGTH], strpos($this->message, ']', $this->pos) - ($this->pos + 2 + $possible[Codes::ATTR_LENGTH]))) === 0;
	}

	protected function handleItemCode()
	{
		$tag = $this->bbc->getItemCode($this->message[$this->pos + 1]);

		// First let's set up the tree: it needs to be in a list, or after an li.
		if ($this->inside_tag === null || ($this->inside_tag[Codes::ATTR_TAG] !== 'list' && $this->inside_tag[Codes::ATTR_TAG] !== 'li'))
		{
			$list_code = $this->bbc->itemCodeList();
			$list_code[Codes::ATTR_DISALLOW_CHILDREN] = isset($this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN]) ? $this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN] : null;

			$this->addOpenTag($list_code);
			$insert = '<ul' . ($tag === '' ? '' : ' style="list-style-type: ' . $tag . '"') . ' class="bbc_list">';
		}
		// We're in a list item already: another itemcode?  Close it first.
		elseif ($this->inside_tag[Codes::ATTR_TAG] === 'li')
		{
			$this->closeOpenedTag();
			$insert = '</li>';
		}
		else
		{
			$insert = '';
		}

		// Now we open a new tag.
		$li_code = $this->bbc->itemCodeListItem();
		$li_code[Codes::ATTR_DISALLOW_CHILDREN] = isset($this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN]) ? $this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN] : null;
		$this->addOpenTag($li_code);

		// First, open the tag...
		$insert .= '<li>';

		$this->addStringAtCurrentPos($insert, 3);
		$this->pos--;

		// Next, find the next break (if any.)  If there's more itemcodes after it, keep it going - otherwise close!
		$next_br = strpos($this->message, '<br />', $this->pos);
		$next_closing_tag = strpos($this->message, '[/', $this->pos);

		$num_open_tags = count($this->open_tags);
		if ($next_br !== false && ($next_closing_tag === false || $next_br <= $next_closing_tag))
		{
			// Can't use offset because of the ^
			preg_match('~^(<br />|&nbsp;|\s|\[)+~', substr($this->message, $next_br + 6), $matches);

			// Keep the list open if the next character after the break is a [. Otherwise, close it.
			$replacement = !empty($matches[0]) && substr_compare($matches[0], '[', -1, 1) === 0 ? '[/li]' : '[/li][/list]';

			$this->message = substr_replace($this->message, $replacement, $next_br, 0);
			$this->open_tags[$num_open_tags - 2][Codes::ATTR_AFTER] = '</ul>';
		}
		// Tell the [list] that it needs to close specially.
		else
		{
			// Move the li over, because we're not sure what we'll hit.
			$this->open_tags[$num_open_tags - 1][Codes::ATTR_AFTER] = '';
			$this->open_tags[$num_open_tags - 2][Codes::ATTR_AFTER] = '</li></ul>';
		}
	}

	/**
	 * Handle codes that are of the parsed context type
	 * @param array $tag
	 *
	 * @return bool
	 */
	protected function handleTypeParsedContent(array $tag)
	{
		// @todo Check for end tag first, so people can say "I like that [i] tag"?
		$this->addOpenTag($tag);
		$this->addStringAtCurrentPos($tag[Codes::ATTR_BEFORE], $this->param_start_pos - $this->pos);
		$this->pos--;

		if (!empty($tag[Codes::ATTR_TRACK_CONTENT]))
		{
			$this->startTrackedContent($tag, $this->pos);
		}

		return false;
	}

	/**
	 * Handle codes that are of the unparsed content type
	 * @param array $tag
	 *
	 * @return bool
	 */
	protected function handleTypeUnparsedContent(array $tag)
	{
		// Find the next closer
		$next_closing_tag = stripos($this->message, '[/' . $tag[Codes::ATTR_TAG] . ']', $this->param_start_pos);

		// No closer
		if ($next_closing_tag === false)
		{
			return true;
		}

		// @todo figure out how to make this move to the validate part
		$data = substr($this->message, $this->param_start_pos, $next_closing_tag - $this->param_start_pos);

		if ($tag[Codes::ATTR_BLOCK_LEVEL] && isset($data[0]) && substr_compare($data, '<br />', 0, 6) === 0)
		{
			$data = substr($data, 6);
		}

		if (isset($tag[Codes::ATTR_VALIDATE]))
		{
			$this->filterData($tag, $data);
		}

		if (!empty($tag[Codes::ATTR_TRACK_CONTENT]))
		{
			$this->startTrackedContent($tag, $this->pos);
		}

		$this->replaceParamVars($tag[Codes::ATTR_CONTENT], array($data));
		$this->addStringAtCurrentPos($tag[Codes::ATTR_CONTENT], $next_closing_tag + 3 + $tag[Codes::ATTR_LENGTH] - $this->pos);
		$this->pos--;

		if (!empty($tag[Codes::ATTR_TRACK_CONTENT]))
		{
			$this->endTrackedContent($tag, $this->pos);
		}

		$this->last_pos = $this->pos + 1;
		return false;
	}

	/**
	 * Handle codes that are of the unparsed equals context type
	 * @param array $tag
	 *
	 * @return bool
	 */
	protected function handleUnparsedEqualsContent(array $tag)
	{
		// The value may be quoted for some tags - check.
		if (isset($tag[Codes::ATTR_QUOTED]))
		{
			$quoted = substr_compare($this->message, '&quot;', $this->param_start_pos, 6) === 0;
			if ($tag[Codes::ATTR_QUOTED] !== Codes::OPTIONAL && !$quoted)
			{
				return true;
			}

			if ($quoted)
			{
				$this->param_start_pos += 6;
			}
		}
		else
		{
			$quoted = false;
		}

		$next_closing_bracket = strpos($this->message, $quoted === false ? ']' : '&quot;]', $this->param_start_pos);
		if ($next_closing_bracket === false)
		{
			return true;
		}

		$next_closing_tag = stripos($this->message, '[/' . $tag[Codes::ATTR_TAG] . ']', $next_closing_bracket);
		if ($next_closing_tag === false)
		{
			return true;
		}

		$data = array(
			substr($this->message, $next_closing_bracket + ($quoted === false ? 1 : 7), $next_closing_tag - ($next_closing_bracket + ($quoted === false ? 1 : 7))),
			substr($this->message, $this->param_start_pos, $next_closing_bracket - $this->param_start_pos)
		);

		if ($tag[Codes::ATTR_BLOCK_LEVEL] && substr_compare($data[0], '<br />', 0, 6) === 0)
		{
			$data[0] = substr($data[0], 6);
		}

		// Validation for my parking, please!
		if (isset($tag[Codes::ATTR_VALIDATE]))
		{
			$this->filterData($tag, $data);
		}

		if (!empty($tag[Codes::ATTR_TRACK_CONTENT]))
		{
			$this->startTrackedContent($tag, $this->pos);
		}

		$this->replaceParamVars($tag[Codes::ATTR_CONTENT], $data, true);
		$this->addStringAtCurrentPos($tag[Codes::ATTR_CONTENT], $next_closing_tag + 3 + $tag[Codes::ATTR_LENGTH] - $this->pos);
		$this->pos--;

		if (!empty($tag[Codes::ATTR_TRACK_CONTENT]))
		{
			$this->endTrackedContent($tag, $this->pos);
		}

		return false;
	}

	/**
	 * Handle codes that are of the closed type
	 * @param array $tag
	 *
	 * @return bool
	 */
	protected function handleTypeClosed(array $tag)
	{
		$next_closing_bracket = strpos($this->message, ']', $this->pos);
		$this->addStringAtCurrentPos($tag[Codes::ATTR_CONTENT], $next_closing_bracket + 1 - $this->pos);
		$this->pos--;

		return false;
	}

	/**
	 * Handle codes that are of the unparsed commas context type
	 * @param array $tag
	 *
	 * @return bool
	 */
	protected function handleUnparsedCommasContent(array $tag)
	{
		$next_closing_bracket = strpos($this->message, ']', $this->param_start_pos);
		if ($next_closing_bracket === false)
		{
			return true;
		}

		$next_closing_tag = stripos($this->message, '[/' . $tag[Codes::ATTR_TAG] . ']', $next_closing_bracket);
		if ($next_closing_tag === false)
		{
			return true;
		}

		// We want $1 to be the content, and the rest to be csv.
		$data = explode(',', ',' . substr($this->message, $this->param_start_pos, $next_closing_bracket - $this->param_start_pos));
		$data[0] = substr($this->message, $next_closing_bracket + 1, $next_closing_tag - $next_closing_bracket - 1);

		if (isset($tag[Codes::ATTR_VALIDATE]))
		{
			$this->filterData($tag, $data);
		}

		if (!empty($tag[Codes::ATTR_TRACK_CONTENT]))
		{
			$this->startTrackedContent($tag, $this->param_start_pos);
		}

		$this->replaceParamVars($tag[Codes::ATTR_CONTENT], $data);
		$this->addStringAtCurrentPos($tag[Codes::ATTR_CONTENT], $next_closing_tag + 3 + $tag[Codes::ATTR_LENGTH] - $this->pos);
		$this->pos--;

		if (!empty($tag[Codes::ATTR_TRACK_CONTENT]))
		{
			$this->endTrackedContent($tag, $this->pos);
		}

		return false;
	}

	/**
	 * Handle codes that are of the unparsed commas type
	 * @param array $tag
	 *
	 * @return bool
	 */
	protected function handleUnparsedCommas(array $tag)
	{
		$next_closing_bracket = strpos($this->message, ']', $this->param_start_pos);
		if ($next_closing_bracket === false)
		{
			return true;
		}

		$data = explode(',', substr($this->message, $this->param_start_pos, $next_closing_bracket - $this->param_start_pos));

		if (isset($tag[Codes::ATTR_VALIDATE]))
		{
			$this->filterData($tag, $data);
		}

		// Replace them out, $1, $2, $3, $4, etc.
		// Fix after, for disabled code mainly.
		$this->replaceParamVars($tag[Codes::ATTR_AFTER], $data);
		$this->replaceParamVars($tag[Codes::ATTR_BEFORE], $data);

		$this->addOpenTag($tag);

		$this->addStringAtCurrentPos($tag[Codes::ATTR_BEFORE], $next_closing_bracket + 1 - $this->pos);
		$this->pos--;

		if (!empty($tag[Codes::ATTR_TRACK_CONTENT]))
		{
			$this->startTrackedContent($tag, $this->pos);
		}

		return false;
	}

	/**
	 * @param string $code
	 * @param array $data
	 * @param bool $no_trim = false
	 */
	protected function replaceParamVars(&$code, array $data, $no_trim = false)
	{
		foreach ($data as $k => $d)
		{
			$code = strtr($code, array('$' . ($k + 1) => $no_trim ? $d : trim($d)));
		}
	}

	/**
	 * Handle codes that are of the equals type
	 * @param array $tag
	 *
	 * @return bool
	 */
	protected function handleEquals(array $tag)
	{
		// The value may be quoted for some tags - check.
		if (isset($tag[Codes::ATTR_QUOTED]))
		{
			$quoted = substr_compare($this->message, '&quot;', $this->param_start_pos, 6) === 0;
			if ($tag[Codes::ATTR_QUOTED] !== Codes::OPTIONAL && !$quoted)
			{
				return true;
			}

			if ($quoted)
			{
				$this->param_start_pos += 6;
			}
		}
		else
		{
			$quoted = false;
		}

		$next_closing_bracket = strpos($this->message, $quoted === false ? ']' : '&quot;]', $this->param_start_pos);
		if ($next_closing_bracket === false)
		{
			return true;
		}

		$data = substr($this->message, $this->param_start_pos, $next_closing_bracket - $this->param_start_pos);

		// Validation for my parking, please!
		if (isset($tag[Codes::ATTR_VALIDATE]))
		{
			$this->filterData($tag, $data);
		}

		// For parsed content, we must recurse to avoid security problems.
		if ($tag[Codes::ATTR_TYPE] === Codes::TYPE_PARSED_EQUALS)
		{
			$this->recursiveParser($data, $tag);
		}

		$this->replaceParamVars($tag[Codes::ATTR_BEFORE], array($data), true);
		$this->replaceParamVars($tag[Codes::ATTR_AFTER], array($data), true);

		$this->addOpenTag($tag);

		$this->addStringAtCurrentPos($tag[Codes::ATTR_BEFORE], $next_closing_bracket + ($quoted === false ? 1 : 7) - $this->pos);
		$this->pos--;

		if (!empty($tag[Codes::ATTR_TRACK_CONTENT]))
		{
			$this->startTrackedContent($tag, $this->pos);
		}

		return false;
	}

	/**
	 * Handles a tag by its type. Offloads the actual handling to handle*() method
	 * @param array $code
	 *
	 * @return bool true if there was something wrong and the parser should advance
	 */
	protected function handleCode(array $code)
	{
		switch ($code[Codes::ATTR_TYPE])
		{
			case Codes::TYPE_PARSED_CONTENT:
				return $this->handleTypeParsedContent($code);

			// Don't parse the content, just skip it.
			case Codes::TYPE_UNPARSED_CONTENT:
				return $this->handleTypeUnparsedContent($code);

			// Don't parse the content, just skip it.
			case Codes::TYPE_UNPARSED_EQUALS_CONTENT:
				return $this->handleUnparsedEqualsContent($code);

			// A closed tag, with no content or value.
			case Codes::TYPE_CLOSED:
				return $this->handleTypeClosed($code);

			// This one is sorta ugly... :/
			case Codes::TYPE_UNPARSED_COMMAS_CONTENT:
				return $this->handleUnparsedCommasContent($code);

			// This has parsed content, and a csv value which is unparsed.
			case Codes::TYPE_UNPARSED_COMMAS:
				return $this->handleUnparsedCommas($code);

			// A tag set to a value, parsed or not.
			case Codes::TYPE_PARSED_EQUALS:
			case Codes::TYPE_UNPARSED_EQUALS:
				return $this->handleEquals($code);
		}

		return false;
	}

	// @todo I don't know what else to call this. It's the area that isn't a tag. Maybe handleContent() would be better?
	// really the only purpose of this is for autolink, html, and changing tabs to &nbsp; in code tags
	protected function betweenTags()
	{
		// Can't have a one letter smiley, URL, or email! (sorry.)
		if ($this->last_pos >= $this->pos - 1)
		{
			return;
		}

		// Make sure the $this->last_pos is not negative.
		$this->last_pos = max($this->last_pos, 0);

		// Pick a block of data to do some raw fixing on.
		// Starts from the last tag. If it had a ATTR_BEFORE it will start from the end of that
		// If the [ wasn't actually a tag, it will include the [ and everything after
		// It ends at the next [
		$data = substr($this->message, $this->last_pos, $this->pos - $this->last_pos);

		// This happens when the pos is > last_pos and there is a trailing \n from one of the tags having "AFTER"
		// In micro-optimization tests, using substr() here doesn't prove to be slower. This is much easier to read so leave it.
		if ($data === $this->smiley_marker)
		{
			return;
		}

		//$o_data = $data;

		// Take care of some HTML!
		if ($this->possible_html && strpos($data, '&lt;') !== false)
		{
			$this->parseHTML($data);
		}

		if ($this->autolink_enabled)
		{
			$this->autoLink($data);
		}

		// This cannot be moved earlier. It breaks tests
		$data = str_replace("\t", '&nbsp;&nbsp;&nbsp;', $data);

		// If it wasn't changed, no copying or other boring stuff has to happen!
		if (substr_compare($this->message, $data, $this->last_pos, $this->pos - $this->last_pos))
		//if ($o_data !== $data)
		{
			$this->message = substr_replace($this->message, $data, $this->last_pos, $this->pos - $this->last_pos);

			// Since we changed it, look again in case we added or removed a tag.  But we don't want to skip any.
			$old_pos = strlen($data) + $this->last_pos;
			$this->pos = strpos($this->message, '[', $this->last_pos);
			$this->pos = $this->pos === false ? $old_pos : min($this->pos, $old_pos);
		}
	}

	protected function handleFootnotes()
	{
		global $fn_num, $fn_content, $fn_count;
		static $fn_total;

		// @todo temporary until we have nesting
		$this->message = str_replace(array('[footnote]', '[/footnote]'), '', $this->message);

		$fn_num = 0;
		$fn_content = array();
		$fn_count = isset($fn_total) ? $fn_total : 0;

		// Replace our footnote text with a [1] link, save the text for use at the end of the message
		$this->message = preg_replace_callback('~(%fn%(.*?)%fn%)~is', array($this, 'footnoteCallback'), $this->message);
		$fn_total += $fn_num;

		// If we have footnotes, add them in at the end of the message
		if (!empty($fn_num))
		{
			$this->message .= '<div class="bbc_footnotes">' . implode('', $fn_content) . '</div>';
		}
	}

	/**
	 * @param array $matches
	 * @return string
	 */
	protected function footnoteCallback(array $matches)
	{
		global $fn_num, $fn_content, $fn_count;

		$fn_num++;
		$fn_content[] = '<div class="target" id="fn' . $fn_num . '_' . $fn_count . '"><sup>' . $fn_num . '&nbsp;</sup>' . $matches[2] . '<a class="footnote_return" href="#ref' . $fn_num . '_' . $fn_count . '">&crarr;</a></div>';

		return '<a class="target" href="#fn' . $fn_num . '_' . $fn_count . '" id="ref' . $fn_num . '_' . $fn_count . '">[' . $fn_num . ']</a>';
	}

	/**
	 * Parse a tag that is disabled
	 * @param array &$tag
	 */
	protected function handleDisabled(array &$tag)
	{
		if (!isset($tag[Codes::ATTR_DISABLED_BEFORE]) && !isset($tag[Codes::ATTR_DISABLED_AFTER]) && !isset($tag[Codes::ATTR_DISABLED_CONTENT]))
		{
			$tag[Codes::ATTR_BEFORE] = $tag[Codes::ATTR_BLOCK_LEVEL] ? '<div>' : '';
			$tag[Codes::ATTR_AFTER] = $tag[Codes::ATTR_BLOCK_LEVEL] ? '</div>' : '';
			$tag[Codes::ATTR_CONTENT] = $tag[Codes::ATTR_TYPE] === Codes::TYPE_CLOSED ? '' : ($tag[Codes::ATTR_BLOCK_LEVEL] ? '<div>$1</div>' : '$1');
		}
		elseif (isset($tag[Codes::ATTR_DISABLED_BEFORE]) || isset($tag[Codes::ATTR_DISABLED_AFTER]))
		{
			$tag[Codes::ATTR_BEFORE] = isset($tag[Codes::ATTR_DISABLED_BEFORE]) ? $tag[Codes::ATTR_DISABLED_BEFORE] : ($tag[Codes::ATTR_BLOCK_LEVEL] ? '<div>' : '');
			$tag[Codes::ATTR_AFTER] = isset($tag[Codes::ATTR_DISABLED_AFTER]) ? $tag[Codes::ATTR_DISABLED_AFTER] : ($tag[Codes::ATTR_BLOCK_LEVEL] ? '</div>' : '');
		}
		else
		{
			$tag[Codes::ATTR_CONTENT] = $tag[Codes::ATTR_DISABLED_CONTENT];
		}
	}

	/**
	 * @param array &$possible
	 * @return array matches
	 */
	protected function matchParameters(array &$possible)
	{
		if (!isset($possible['regex_cache']))
		{
			$possible['regex_cache'] = array();
			foreach ($possible[Codes::ATTR_PARAM] as $p => $info)
			{
				// @todo there are 3 options for PARAM_ATTR_QUOTED: required, optional, and none. This doesn't represent that.
				$quote = empty($info[Codes::PARAM_ATTR_QUOTED]) ? '' : '&quot;';
/*
				// No quotes
				if (empty($info[Codes::PARAM_ATTR_QUOTED]) || $info[Codes::PARAM_ATTR_QUOTED] === Codes::NONE)
				{
					$quote = '';
					$end_quote = '';
				}
				// Quotes are required
				elseif ($info[Codes::PARAM_ATTR_QUOTED] === Codes::REQUIRED)
				{
					$quote = '&quot;';
					$end_quote = '&quot;';
				}
				// Quotes are optional
				elseif ($info[Codes::PARAM_ATTR_QUOTED] === Codes::OPTIONAL)
				{
					// This gets a little tricky. If there was an opening quote, there must be a closing quote.
					// If there was no opening quote, there mustn't be a closing quote.
					// But, quotes are optional
					$quote = '';
					$end_quote = '';
				}
*/
				//$possible['regex_cache'][] = '(\s+' . $p . '=' . $quote . (isset($info[Codes::PARAM_ATTR_MATCH]) ? $info[Codes::PARAM_ATTR_MATCH] : '(.+?)') . $end_quote. ')' . (empty($info[Codes::PARAM_ATTR_OPTIONAL]) ? '' : '?');
				$possible['regex_cache'][] = '(\s+' . $p . '=' . $quote . (isset($info[Codes::PARAM_ATTR_MATCH]) ? $info[Codes::PARAM_ATTR_MATCH] : '(.+?)') . $quote. ')' . (empty($info[Codes::PARAM_ATTR_OPTIONAL]) ? '' : '?');
			}
			$possible['regex_size'] = count($possible['regex_cache']) - 1;
			$possible['regex_keys'] = range(0, $possible['regex_size']);
		}

		// Okay, this may look ugly and it is, but it's not going to happen much and it is the best way
		// of allowing any order of parameters but still parsing them right.
		$message_stub = substr($this->message, $this->param_start_pos - 1);

		// If an addon adds many parameters we can exceed max_execution time, lets prevent that
		// 5040 = 7, 40,320 = 8, (N!) etc
		$max_iterations = self::MAX_PERMUTE_ITERATIONS;

		// Use the same range to start each time. Most BBC is in the order that it should be in when it starts.
		$keys = $possible['regex_keys'];

		// Step, one by one, through all possible permutations of the parameters until we have a match
		do {
			$match_preg = '~^';
			foreach ($keys as $key)
			{
				$match_preg .= $possible['regex_cache'][$key];
			}
			$match_preg .= '\]~i';

			// Check if this combination of parameters matches the user input
			$match = preg_match($match_preg, $message_stub, $matches) !== 0;
		} while (!$match && --$max_iterations && ($keys = pc_next_permutation($keys, $possible['regex_size'])));

		return $match ? $matches : array();
	}

	/**
	 * Recursively call the parser with a new Codes object
	 * This allows to parse BBC in parameters like [quote author="[url]www.quotes.com[/url]"]Something famous.[/quote]
	 *
	 * @param string &$data
	 * @param array $tag
	 */
	protected function recursiveParser(&$data, array $tag)
	{
		// @todo if parsed tags allowed is empty, return?
		$bbc = clone $this->bbc;

		if (!empty($tag[Codes::ATTR_PARSED_TAGS_ALLOWED]))
		{
			$bbc->setParsedTags($tag[Codes::ATTR_PARSED_TAGS_ALLOWED]);
		}

		// Do not use $this->autolinker. For some reason it causes a recursive loop
		// @todo figure out why it causes a recursive loop!
		$autolinker = null;
		$html = null;
		$this->triggerEvent('recursive_bbc', array(&$autolinker, &$html));

		$parser = new \BBC\Parser($bbc, $autolinker, $html);
		$parser->canParseAutolink($this->autolink_enabled);
		$parser->canParseHTML($this->html_enabled);
		$data = $parser->enableSmileys(empty($tag[Codes::ATTR_PARSED_TAGS_ALLOWED]))->parse($data);
	}

	/**
	 * @return array
	 */
	public function getBBC()
	{
		return $this->bbc->getForParsing();
	}

	/**
	 * Enable the parsing of smileys
	 * @param bool|true $enable
	 *
	 * @return $this
	 */
	public function enableSmileys($enable = true)
	{
		$this->do_smileys = (bool) $enable;
		return $this;
	}

	/**
	 * Open a tag
	 * @param array $tag
	 */
	protected function addOpenTag(array &$tag)
	{
		$this->open_tags[] = &$tag;
	}

	/**
	 * @param string|false $tag = false False closes the last open tag. Anything else finds that tag LIFO
	 *
	 * @return mixed
	 */
	protected function closeOpenedTag($tag = false)
	{
		if ($tag === false)
		{
			return array_pop($this->open_tags);
		}
		elseif (isset($this->open_tags[$tag]))
		{
			$return = $this->open_tags[$tag];
			unset($this->open_tags[$tag]);
			return $return;
		}
	}

	/**
	 * Check if there are any tags that are open
	 * @return bool
	 */
	protected function hasOpenTags()
	{
		return !empty($this->open_tags);
	}

	/**
	 * Get the last opened tag
	 * @return array
	 */
	protected function getLastOpenedTag()
	{
		return end($this->open_tags);
	}

	/**
	 * Get the currently opened tags
	 * @param bool|false $tags_only True if you want just the tag or false for the whole code
	 *
	 * @return array
	 */
	protected function getOpenedTags($tags_only = false)
	{
		if (!$tags_only)
		{
			return $this->open_tags;
		}

		$tags = array();
		foreach ($this->open_tags as $tag)
		{
			$tags[] = $tag[Codes::ATTR_TAG];
		}
		return $tags;
	}

	// @todo There's not 1 test that the substr_replace() gets called here.
	/**
	 * @param string &$message
	 * @param null|int $offset = null
	 */
	protected function trimWhiteSpace(&$message, $offset = null)
	{
		if (preg_match('~(<br />|&nbsp;|\s)*~', $this->message, $matches, null, $offset) !== 0 && isset($matches[0]) && $matches[0] !== '')
		{
			$this->message = substr_replace($this->message, '', $this->pos, strlen($matches[0]));
		}
	}

	/**
	 * @param array $code
	 * @param array $matches
	 *
	 * @return array
	 */
	protected function setupTagParameters(array $code, array $params)
	{
		// Put the parameters into the string.
		if (isset($code[Codes::ATTR_BEFORE]))
		{
			$code[Codes::ATTR_BEFORE] = strtr($code[Codes::ATTR_BEFORE], $params);
		}
		if (isset($code[Codes::ATTR_AFTER]))
		{
			$code[Codes::ATTR_AFTER] = strtr($code[Codes::ATTR_AFTER], $params);
		}
		if (isset($code[Codes::ATTR_CONTENT]))
		{
			$code[Codes::ATTR_CONTENT] = strtr($code[Codes::ATTR_CONTENT], $params);
		}

		return $code;
	}

	protected function getParameters(array $code, array $matches)
	{
		$params = array();
		for ($i = 1, $n = count($matches); $i < $n; $i += 2)
		{
			$key = strtok(ltrim($matches[$i]), '=');

			if (isset($code[Codes::ATTR_PARAM][$key][Codes::PARAM_ATTR_VALUE]))
			{
				$params['{' . $key . '}'] = strtr($code[Codes::ATTR_PARAM][$key][Codes::PARAM_ATTR_VALUE], array('$1' => $matches[$i + 1]));
			}
			// @todo it's not validating it. it is filtering it
			elseif (isset($code[Codes::ATTR_PARAM][$key][Codes::ATTR_VALIDATE]))
			{
				$params['{' . $key . '}'] = $code[Codes::ATTR_PARAM][$key][Codes::ATTR_VALIDATE]($matches[$i + 1]);
			}
			else
			{
				$params['{' . $key . '}'] = $matches[$i + 1];
			}

			// Just to make sure: replace any $ or { so they can't interpolate wrongly.
			$params['{' . $key . '}'] = str_replace(array('$', '{'), array('&#036;', '&#123;'), $params['{' . $key . '}']);
		}

		foreach ($code[Codes::ATTR_PARAM] as $p => $info)
		{
			if (!isset($params['{' . $p . '}']))
			{
				$params['{' . $p . '}'] = '';
			}
		}

		return $params;
	}

	/**
	 * Check if a tag (not a code) is open
	 * @param string $tag
	 *
	 * @return bool
	 */
	protected function isTagOpen($tag)
	{
		foreach ($this->open_tags as $open)
		{
			if ($open[Codes::ATTR_TAG] === $tag)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Close any open codes that aren't block level.
	 * Used before opening a code that *is* block level
	 */
	protected function closeNonBlockLevel()
	{
		$n = count($this->open_tags) - 1;
		while ($n >= 0 && !$this->open_tags[$n][Codes::ATTR_BLOCK_LEVEL])
		{
			$n--;
		}

		// Close all the non block level tags so this tag isn't surrounded by them.
		for ($i = count($this->open_tags) - 1; $i > $n; $i--)
		{
			$old_pos = $this->pos;
			$this->addStringAtCurrentPos($this->open_tags[$i][Codes::ATTR_AFTER], 0);
			$this->param_start_pos += $this->pos - $old_pos;

			// Trim or eat trailing stuff... see comment at the end of the big loop.
			if ($this->open_tags[$i][Codes::ATTR_BLOCK_LEVEL] && substr_compare($this->message, '<br />', $this->pos, 6) === 0)
			{
				$this->message = substr_replace($this->message, '', $this->pos, 6);
			}

			if (isset($tag[Codes::ATTR_TRIM]) && $tag[Codes::ATTR_TRIM] !== Codes::TRIM_INSIDE)
			{
				$this->trimWhiteSpace($this->message, $this->pos);
			}

			$this->closeOpenedTag();
		}
	}

	/**
	 * Add markers around a string to denote that smileys should not be parsed
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	protected function noSmileys($string)
	{
		return $this->smiley_marker . $string . $this->smiley_marker;
	}

	public function canCache()
	{
		return $this->can_cache;
	}

	// This is just so I can profile it.
	protected function filterData(array $tag, &$data)
	{
		$tag[Codes::ATTR_VALIDATE]($tag, $data, $this->bbc->getDisabled());
	}

	/**
	 * Add a string at the current position, adding smiley markers and advancing the pointer
	 *
	 * @param string $string The string to be added
	 * @param null $len = null How many bytes to eat
	 * @param bool $no_smileys = true If the smiley markers should be added around the string
	 */
	protected function addStringAtCurrentPos($string, $len = null, $no_smileys = true)
	{
		$tmp = $no_smileys ? $this->noSmileys($string) : $string;
		$this->message = substr_replace($this->message, $tmp, $this->pos, $len);
		$this->pos += strlen($tmp);
	}

	protected function triggerEvent($name, array $params)
	{
		call_integration_hook('integrate_' . $name, $params);
	}
}