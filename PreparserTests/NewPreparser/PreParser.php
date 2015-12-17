<?php


use \BBC\PreparseCodes as Codes;
use \BBC\Autolink;

//define('BR', '<br />');
//define('BR_LEN', strlen(BR));

// Anywhere you see - 1 + 2 it's because you get rid of the ] and add 2 \n

/**
 * Some rules of the preparser:
 * * it must be return the same result no matter how many times you run it
 * * it shouldn't try to guess the future. Codes change.
 * * don't try to dictate how many parameters or what those parameters should be in a code
 * * however, parameters shouldn't be different per code. So a single filter for a parameter
 * * try to be generic with rules. Don't try to use types since types will eventually change
 * * you can do preparsing to make the parser faster, but don't break the parser doing so
 * * if a code for the regular parser is of an unparsed type, it should also be in the preparser as unparsed.
 *   otherwise certain actions taken in the pre-parser might show up in the output of the parser. eg, autolinked urls
 */

class PreParser
{
	const MAX_PERMUTE_ITERATIONS = 5040;

	protected $message;
	protected $codes;
	protected $item_codes;
	// @todo are all of these pos properties necessary? Seems like most of them are purely local and the rest can probably be shared
	protected $pos;
	protected $param_start_pos;
	protected $pos2;
	protected $pos3;
	protected $last_pos;
	protected $open_tags = array();
	// This is the actual tag that's open
	protected $open_code;

	protected $autolinker;
	protected $possible_html;
	protected $html_parser;

	protected $parse_tree = array();

	/** @var bool Whether tags are case sensitive */
	protected $case_sensitive_tags = false;

	/**
	 * @param \BBC\Codes $bbc
	 */
	public function __construct(Codes $codes, Autolink $autolinker = null, HtmlParser $html_parser = null)
	{
		$this->codes = $codes;

		$this->item_codes = $this->codes->getItemCodes();

		$this->autolinker = $autolinker;
		$this->loadAutolink();

		$this->html_parser = $html_parser;
	}

	/**
	 * Sets if the tags must be lowercased
	 * @param bool $toggle
	 */
	public function setTagsCaseSensitive($toggle)
	{
		$this->case_sensitive_tags = (bool) $toggle;
	}

	/**
	 * Reset the parser's properties for a new message
	 */
	public function resetParser()
	{
		$this->pos = -1;
		$this->param_start_pos = null;
		$this->pos2 = null;
		$this->last_pos = null;
		$this->open_tags = array();
		$this->open_code = null;
		$this->lastAutoPos = 0;
		$this->previewing = false;
	}

	/**
	 * Set if the message is a preview
	 * @param bool $toggle
	 * @return $this
	 */
	public function setPreviewing($toggle)
	{
		$this->previewing = (bool) $toggle;
		return $this;
	}

	protected function cleanMessage()
	{
		$this->message = Util::htmlspecialchars($this->message, ENT_QUOTES, 'UTF-8', true);

		$this->message = strtr($this->message, array(
				"\r" => '',
				'[]' => '&#91;]',
				'[&#039;' => '&#91;&#039;'
		));

		// Clean up any cut/paste issues we may have
		$this->message = sanitizeMSCutPaste($this->message);
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
		call_integration_hook('integrate_preparsebbc_before', array(&$message, $this->codes));

		$this->message = $message;

		// Don't waste cycles
		if ($this->message === '')
		{
			return '';
		}

		$this->cleanMessage();

		$this->resetParser();

		// Check if the message might have a link or email to save a bunch of parsing in autolink()
		$this->autolinker->setPossibleAutolink($this->message);

		$this->possible_html = !empty($GLOBALS['modSettings']['enablePostHTML']) && strpos($message, '&lt;') !== false;

		// Don't load the HTML Parser unless we have to
		if ($this->possible_html && $this->html_parser === null)
		{
			$this->loadHtmlParser();
		}

		// This handles pretty much all of the parsing. It is a separate method so it is easier to override and profile.
		$this->parse_loop();

		// Close any remaining tags.
		while ($code = $this->closeOpenedTag())
		{
			$this->message .= $code[Codes::ATTR_AFTER];
		}

		// Allow addons access to what the parser created
		$message = $this->message;
		call_integration_hook('integrate_preparsebbc_after', array(&$message));
		$this->message = $message;

		return $this->message;
	}

	protected function parse_loop()
	{
		while ($this->pos !== false)
		{
			$this->last_pos = isset($this->last_pos) ? max($this->pos, $this->last_pos) : $this->pos;
			$this->pos = strpos($this->message, '[', $this->pos + 1);

			// Failsafe.
			if ($this->pos === false || $this->last_pos > $this->pos)
			{
				$this->pos = strlen($this->message) + 1;
			}

			// Can't have a one letter smiley, URL, or email! (sorry.)
			if ($this->last_pos < $this->pos - 1)
			{
				$this->betweenTags();
			}

			// Are we there yet?  Are we there yet?
			if ($this->pos >= strlen($this->message) - 1)
			{
				return;
			}

			$next_char = strtolower($this->message[$this->pos + 1]);

			// Possibly a closer?
			if ($next_char === '/')
			{
				if($this->hasOpenTags())
				{
					// $content = [start: (last ]), end: $this->pos - 1]
					// If $content === '' ? removeLastTag()

					// Close tags that are out of order too
					$this->handleOpenTags();
				}

				// We don't allow / to be used for anything but the closing character, so this can't be a tag
				continue;
			}

			// No tags for this character, so just keep going (fastest possible course.)
			if (!$this->codes->hasChar($next_char))
			{
				continue;
			}

			$this->open_code = !$this->hasOpenTags() ? null : $this->getLastOpenedTag();

			if ($this->isItemCode($next_char))
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
				$code = $this->findCode($this->codes->getByChar($next_char));
			}

			// Implicitly close lists and tables if something other than what's required is in them. This is needed for itemcode.
			if ($code === null && $this->open_code !== null)
			{
				$this->closeOpenedTag();
				$tmp = $this->open_code[Codes::ATTR_AFTER];
				$this->message = substr_replace($this->message, $tmp, $this->pos, 0);
				$this->pos += strlen($tmp) - 1;
			}

			// There must be a next tag
			if (isset($this->open_code[Codes::NEXT_TAG_MUST_BE]) && !in_array($code, $this->open_code[Codes::NEXT_TAG_MUST_BE]))
			{
				// this isn't what the next tag must be
				// add the first tag from the NEXT_TAG_MUST_BE array
				$next_tag_must_be = reset($this->open_code[Codes::NEXT_TAG_MUST_BE]);

				// Open the tag
				//$this->addOpenCode(isset($this->codes[$next_tag_must_be]) ? $this->codes[$next_tag_must_be] : $this->codes->getDummyTag($next_tag_must_be));
				// Add it at the current position
				$insert = '[' . $next_tag_must_be . ']';
				$this->addAtPos($insert, $this->pos);

				// Set position so it opens this tag
				// Go backwards so we capture this new code
				$this->pos -= strlen($insert) - 1;

				// Continue so we go back to the previous position
				continue;
			}

			// No tag?  Keep looking, then.  Silly people using brackets without actual tags.
			if ($code === null)
			{
				continue;
			}

			// If we require tags to be lowercased (we do) but this tag wasn't, then lowercase it
			if (!$this->case_sensitive_tags && !substr_compare($this->message, $code[Codes::ATTR_TAG], $this->pos + 1, $code[Codes::ATTR_LENGTH], true))
			{
				$this->message = substr_replace($this->message, $code[self::ATTR_TAG], $this->pos + 1, $code[Codes::ATTR_LENGTH]);
			}

			// The only special case is 'html', which doesn't need to close things.
			if ($code[Codes::BLOCK_LEVEL] && $code[Codes::ATTR_TAG] !== 'html' && !$this->open_code[Codes::BLOCK_LEVEL])
			{
				$this->closeNonBlockLevel();
			}

			// Set the offset of this so we know where it started from (namely in case we have to remove it)
			$code['start_pos'] = $this->pos;

			if (isset($this->message[$this->pos + 1 + $code[Codes::ATTR_LENGTH]]))
			{
				$this->handleParameters($code);
				$this->handleEquals($code);
			}
			// If $code[self::NO_PARSE], $closing_tag_pos = strpos($this->message, '[/' . $code[self::ATTR_TAG] . ']', $this->pos)
			// If $code[self::FILTER_CONTENT] $code[self::FILTER_CONTENT]($content);
			// If $content === '' && $code[self::REMOVE_EMPTY], remove the tag
			// Else, $this->pos = $closing_tag_pos

			// This is the part where we actually handle the tags. I know, crazy how long it took.
			if($this->handleOpenCode($code))
			{
				continue;
			}
		}
	}

	protected function handleEquals(array $code)
	{
		// Check if there can be an equals parameter
		if ('=' !== $this->pos + 1 + $code[Codes::ATTR_LENGTH])
		{
			// No parameters, no changes
			return false;
		}

		$code['equals_start_offset'] = $this->pos + 1 + $code[Codes::ATTR_LENGTH];

		$this->getEquals($code['equals_start_offset']);
		$this->filterEquals($code, $value);

		return true;
	}

	protected function getEquals($pos)
	{
		$value = preg_match();
		$quoted = false;
	}

	protected function handleParameters(array &$code)
	{
		// Check if there can be parameters
		if (' ' !== $this->pos + 1 + $code[Codes::ATTR_LENGTH])
		{
			// No parameters, no changes
			return false;
		}

		$code['param_start_offset'] = $this->pos + 1 + $code[Codes::ATTR_LENGTH];

		// Get the parameters.
		$code[Codes::FOUND_PARAMS] = $this->getParameters($code['param_start_offset']);

		// All the preparse code is declaring is if the parameters need to be filtered, not if they have any
		// So there is a strong possibility that there are parameters, but we still need to know where they are.
		// Even if there are no filters, it is best to declare that there are parameters so they can be ordered properly

		$code['param_end_offset'] = $code['param_start_offset'];

		foreach ($code[Codes::FOUND_PARAMS] as &$param)
		{
			if (!empty($code[Codes::PARAMS][$param['key']][Codes::FILTER_PARAM]))
			{
				$this->filterParameter($code, $param);
			}

			// The ending offset is a little tougher to get.
			$code['param_end_offset'] = max($code['param_end_offset'],
				$param['offset'] + strlen($param['string']) + 1
			);
		}

		$code[Codes::FOUND_PARAMS] = $this->sortParameters($code[Codes::FOUND_PARAMS], $code[Codes::PARAMS]);

		$insert = $this->assembleParameters($code);
		$this->addAtPos($insert, $code['param_start_offset'], $code['param_end_offset'] - $code['param_start_offset']);

		// Parameters = changes
		return true;
	}

	protected function filterParameter(array $code, array &$param)
	{
		// Run the filter
		if ($code[Codes::PARAMS][$param['key']][Codes::PARAM_IS_URL])
		{
			$this->filterUrl($param['value']);
		}
		else
		{
			$code[Codes::PARAMS][$param['key']][Codes::FILTER_PARAM]($param);
		}
	}

	protected function assembleParameters(array $code)
	{
		$insert = '';

		foreach ($code[Codes::FOUND_PARAMS] as $param)
		{
			if (isset($code[Codes::PARAMS][$param['key']]))
			{
				continue;
			}
		}

		return $insert;
	}

	protected function sortParameters(array $parameters, array $known_order)
	{
		$sorted = array();

		// Do them in the order that they appear here
		foreach ($parameters as $param)
		{

		}

		// Then do the rest
		foreach ($known_order as $param)
		{
			if (isset($parameters[$param['key']]))
			{
				continue;
			}
		}

		return $sorted;
	}

	protected function assembleParameter($key, $value, $quoted)
	{
		return ' ' . $key . '=' . ($quoted ? '&quot;' : '') . $value . ($quoted ? '&quot;' : '');
	}

	protected function getParametersAtPos($pos)
	{
		preg_match_all('/\s*([^=]+)=(\S+)\s*/', $pos, $matches, PREG_OFFSET_CAPTURE);

		// Set $next_closing_bracket to where the offset of the closing bracket is so handleParameters() can write over
		// if it needs to make changes

		$parameters = array();
		foreach ($matches as $match)
		{

		}
		/**
		 * @returns array [
		 *   [
		 *     key: (string) $key
		 *     value: (string) $value
		 *     quoted: (bool) true/false
		 *     offset: (int) 0
		 *   ]
		 * ]
		 */

		return $parameters;
	}

	protected function filterUrl($url)
	{

	}

	protected function isItemCode($next_char)
	{
		return $this->codes->isItemCode($next_char)
			&& isset($this->message[$this->pos + 2])
			&& $this->message[$this->pos + 2] === ']';
	}

	protected function handleOpenCodes()
	{
		// Next closing bracket after the first character
		$this->pos2 = strpos($this->message, ']', $this->pos + 1);

		// Playing games? string = [/]
		if ($this->pos2 === $this->pos + 2)
		{
			return;
		}

		// Get everything between [/ and ]
		$look_for = strtolower(substr($this->message, $this->pos + 2, $this->pos2 - $this->pos - 2));
		$to_close = array();
		$block_level = null;

		do
		{
			// Get the last opened tag
			$code = $this->closeOpenedTag();

			// No open tags
			if (!$code)
			{
				break;
			}

			if ($code[Codes::BLOCK_LEVEL])
			{
				// Only find out if we need to.
				if ($block_level === false)
				{
					$this->addOpenCode($code);
					break;
				}

				// The idea is, if we are LOOKING for a block level tag, we can close them on the way.
				if (isset($look_for[1]) && $this->codes->hasChar($look_for[0]))
				{
					foreach ($this->codes->getByChar($look_for[0]) as $temp)
					{
						if ($temp[Codes::ATTR_TAG] === $look_for)
						{
							$block_level = $temp[Codes::BLOCK_LEVEL];
							break;
						}
					}
				}

				if ($block_level !== true)
				{
					$block_level = false;
					$this->addOpenCode($code);
					break;
				}
			}

			$to_close[] = $code;
		} while ($code[Codes::ATTR_TAG] !== $look_for);

		// Did we just eat through everything and not find it?
		if (!$this->hasOpenTags() && (empty($code) || $code[Codes::ATTR_TAG] !== $look_for))
		{
			$this->open_tags = $to_close;
			return;
		}
		elseif (!empty($to_close) && $code[Codes::ATTR_TAG] !== $look_for)
		{
			if ($block_level === null && isset($look_for[0]) && $this->codes->hasChar($look_for[0]))
			{
				foreach ($this->codes->getByChar($look_for[0]) as $temp)
				{
					if ($temp[Codes::ATTR_TAG] === $look_for)
					{
						$block_level = !empty($temp[Codes::BLOCK_LEVEL]);
						break;
					}
				}
			}

			// We're not looking for a block level tag (or maybe even a tag that exists...)
			if (!$block_level)
			{
				foreach ($to_close as $code)
				{
					$this->addOpenCode($code);
				}

				return;
			}
		}

		foreach ($to_close as $code)
		{
			$tmp = $this->noSmileys($code[Codes::ATTR_AFTER]);
			$this->message = substr_replace($this->message, $tmp, $this->pos, $this->pos2 + 1 - $this->pos);
			$this->pos += strlen($tmp);
			$this->pos2 = $this->pos - 1;

			// See the comment at the end of the big loop - just eating whitespace ;).
			if ($code[Codes::BLOCK_LEVEL] && isset($this->message[$this->pos]) && substr_compare($this->message, '<br />', $this->pos, 6) === 0)
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

	public function loadHtmlParser()
	{
		$parser = new HtmlParser;
		call_integration_hook('integrate_bbc_load_html_parser', array(&$parser));
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
		if ($data === '' || $data === $this->smiley_marker  || !$this->autolinker->hasPossible())
		{
			return;
		}

		// Are we inside tags that should be auto linked?
		if ($this->hasOpenTags())
		{
			foreach ($this->getOpenedTags() as $open_tag)
			{
				if (!$open_tag[Codes::ATTR_AUTOLINK])
				{
					return;
				}
			}
		}

		$this->autolinker->parse($data);
	}

	/**
	 * Load the autolink regular expression to be used in autoLink()
	 */
	protected function loadAutolink()
	{
		if ($this->autolinker === null)
		{
			$this->autolinker = new Autolink($this->codes);
		}
	}

	/**
	 * Find if the current character is the start of a tag and get it
	 *
	 * @param array $possible_codes
	 *
	 * @return null|array the tag that was found or null if no tag found
	 */
	protected function findCode(array $possible_codes)
	{
		$code = null;
		$last_check = null;

		foreach ($possible_codes as $possible)
		{
			// Skip tags that didn't match the next X characters
			if ($possible[Codes::ATTR_TAG] === $last_check)
			{
				continue;
			}

			// The character after the possible tag or nothing
			$next_c = isset($this->message[$this->pos + 1 + $possible[Codes::ATTR_LENGTH]]) ? $this->message[$this->pos + 1 + $possible[Codes::ATTR_LENGTH]] : '';

			// This only happens if the tag is the last character of the message
			if ($next_c === '')
			{
				break;
			}

			// The next character must be one of these or it's not a tag
			if ($next_c !== ' ' && $next_c !== ']' && $next_c !== '=' && $next_c !== '/')
			{
				$last_check = $possible[Codes::ATTR_TAG];
				continue;
			}

			// Not a match?
			if (substr_compare($this->message, $possible[Codes::ATTR_TAG], $this->pos + 1, $possible[Codes::ATTR_LENGTH], !$this->case_sensitive_tags) !== 0)
			{
				$last_check = $possible[Codes::ATTR_TAG];
				continue;
			}

			break;
		}

		return $code;
	}


	// Just get a key/value list of the parameters in any order for filtering
	protected function getParameters($pos)
	{

	}

	protected function handleItemCode()
	{
		$code = $this->message[$this->pos + 1];
		$addText = '';

		// First let's set up the tree: it needs to be in a list, or after an li.
		if ($this->open_code === null || ($this->open_code[Codes::ATTR_TAG] !== 'list' && $this->open_code[Codes::ATTR_TAG] !== 'li'))
		{
			$addText .= '[list itemcode=1]';

			$this->addOpenCode(array(
					Codes::ATTR_TAG => 'list',
					Codes::ATTR_TYPE => Codes::TYPE_PARSED_CONTENT,
					Codes::BLOCK_LEVEL => true,
					Codes::ATTR_REQUIRE_CHILDREN => array('li' => 'li'),
					Codes::ATTR_DISALLOW_CHILDREN => isset($this->open_code[Codes::ATTR_DISALLOW_CHILDREN]) ? $this->open_code[Codes::ATTR_DISALLOW_CHILDREN] : null,
					Codes::ATTR_LENGTH => 4,
					Codes::ATTR_AUTOLINK => true,
			));
		}
		// We're in a list item already: another itemcode?  Close it first.
		elseif ($this->open_code[Codes::ATTR_TAG] === 'li')
		{
			$this->closeOpenedTag();

			$addText .= '[/li]';
		}
		else
		{
			$code = '';
		}

		$addText .= '[li itemcode="' . $code .'"]';
		$addTextLen = strlen($addText);

		$this->addAtPos($addText, $this->pos + $addTextLen);

		// Now we open a new tag.
		$this->addOpenCode(array(
			Codes::ATTR_TAG => 'li',
			Codes::ATTR_TYPE => Codes::TYPE_PARSED_CONTENT,
			Codes::ATTR_AFTER => '</li>',
			Codes::ATTR_TRIM => Codes::TRIM_OUTSIDE,
			Codes::BLOCK_LEVEL => true,
			Codes::ATTR_DISALLOW_CHILDREN => isset($this->open_code[Codes::ATTR_DISALLOW_CHILDREN]) ? $this->open_code[Codes::ATTR_DISALLOW_CHILDREN] : null,
			Codes::ATTR_AUTOLINK => true,
			Codes::ATTR_LENGTH => 2,
		));

		// First, open the tag...
		$code .= '<li>';

		$tmp = $this->noSmileys($code);
		$this->message = substr_replace($this->message, $tmp, $this->pos, 3);
		$this->pos += strlen($tmp) - 1;

		// Next, find the next break (if any.)  If there's more itemcode after it, keep it going - otherwise close!
		$this->pos2 = strpos($this->message, "\n", $this->pos);
		$this->pos3 = strpos($this->message, '[/', $this->pos);

		$num_open_tags = count($this->open_tags);
		if ($this->pos2 !== false && ($this->pos3 === false || $this->pos2 <= $this->pos3))
		{
			// Can't use offset because of the ^
			preg_match('~^(<br />|&nbsp;|\s|\[)+~', substr($this->message, $this->pos2 + 6), $matches);

			// Keep the list open if the next character after the break is a [. Otherwise, close it.
			$replacement = !empty($matches[0]) && substr_compare($matches[0], '[', -1, 1) === 0 ? '[/li]' : '[/li][/list]';

			$this->message = substr_replace($this->message, $replacement, $this->pos2, 0);
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

	// @todo I don't know what else to call this. It's the area that isn't a tag.
	protected function betweenTags()
	{
		// Make sure the $this->last_pos is not negative.
		$this->last_pos = max($this->last_pos, 0);

		// Pick a block of data to do some raw fixing on.
		$data = substr($this->message, $this->last_pos, $this->pos - $this->last_pos);

		// This happens when the pos is > last_pos and there is a trailing \n from one of the tags having "AFTER"
		// In micro-optimization tests, using substr() here doesn't prove to be slower. This is much easier to read so leave it.
		if ($data === $this->smiley_marker)
		{
			return;
		}

		// Take care of some HTML!
		if ($this->possible_html && strpos($data, '&lt;') !== false)
		{
			// @todo new \Parser\BBC\HTML;
			$this->parseHTML($data);
		}

		// The global doesn't matter. You can always change that later.
		$this->autoLink($data);

		// If it wasn't changed, no copying or other boring stuff has to happen!
		if (substr_compare($this->message, $data, $this->last_pos, $this->pos - $this->last_pos))
		{
			$this->message = substr_replace($this->message, $data, $this->last_pos, $this->pos - $this->last_pos);

			// Since we changed it, look again in case we added or removed a tag.  But we don't want to skip any.
			$old_pos = strlen($data) + $this->last_pos;
			$this->pos = strpos($this->message, '[', $this->last_pos);
			$this->pos = $this->pos === false ? $old_pos : min($this->pos, $old_pos);
		}
	}

	// @todo change to returning matches. If array() continue
	/**
	 * @param array &$possible
	 * @return bool
	 */
	protected function matchParameters(array &$possible)
	{
		$matches = array();
		
		if (!isset($possible['regex_cache']))
		{
			$possible['regex_cache'] = array();
			foreach ($possible[Codes::ATTR_PARAM] as $p => $info) {
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

		return $matches;
	}

	/**
	 * Open a tag
	 * @param array $code
	 */
	protected function addOpenCode(array $code)
	{
		$this->open_tags[] = $code;
	}

	/**
	 * @param string|false $code = false False closes the last open tag. Anything else finds that tag LIFO
	 *
	 * @return mixed
	 */
	protected function closeOpenedTag($code = false)
	{
		if ($code === false)
		{
			return array_pop($this->open_tags);
		}
		elseif (isset($this->open_tags[$code]))
		{
			$return = $this->open_tags[$code];
			unset($this->open_tags[$code]);
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
	 * @param bool|false $codes_only True if you want just the tag or false for the whole code
	 *
	 * @return array
	 */
	protected function getOpenedTags($codes_only = false)
	{
		if (!$codes_only)
		{
			return $this->open_tags;
		}

		$codes = array();
		foreach ($this->open_tags as $code)
		{
			$codes[] = $code[Codes::ATTR_TAG];
		}
		return $codes;
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
	 * Check if a tag (not a code) is open
	 * @param string $tag
	 *
	 * @return bool
	 */
	protected function isOpen($tag)
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
		while (empty($this->open_tags[$n][Codes::BLOCK_LEVEL]) && $n >= 0)
		{
			$n--;
		}

		// Close all the non block level tags so this tag isn't surrounded by them.
		for ($i = count($this->open_tags) - 1; $i > $n; $i--)
		{
			$add_text = '[/' . $this->open_tags[$i][Codes::ATTR_TAG] . ']';
			$this->message = substr_replace($this->message, $tmp, $this->pos, 0);
			$ot_strlen = strlen($add_text);
			$this->pos += $ot_strlen;
			$this->param_start_pos += $ot_strlen;

			// Trim or eat trailing stuff... see comment at the end of the big loop.
			if (!empty($this->open_tags[$i][Codes::BLOCK_LEVEL]) && substr_compare($this->message, '<br />', $this->pos, 6) === 0)
			{
				$this->message = substr_replace($this->message, '', $this->pos, 6);
			}

			if (isset($code[Codes::ATTR_TRIM]) && $code[Codes::ATTR_TRIM] !== Codes::TRIM_INSIDE)
			{
				$this->trimWhiteSpace($this->message, $this->pos);
			}

			$this->closeOpenedTag();
		}
	}

	// This is just so I can profile it.
	protected function filterData(array $code, &$data)
	{
		$code[Codes::ATTR_VALIDATE]($code, $data, $this->codes->getDisabled());
	}

	protected function checkNextTag($next_tag, $only_whitespace = true)
	{
		if ($only_whitespace)
		{
			preg_match();
		}
		else
		{

		}


	}

	protected function addAtPos($string, $pos, $length = null)
	{
		$this->message = substr_replace($this->message, $string, $pos, $length);
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

		do
		{
			// Get the last opened tag
			$tag = $this->closeOpenedTag();

			// No open tags
			if (!$tag)
			{
				break;
			}

			if ($tag[Codes::ATTR_BLOCK_LEVEL])
			{
				// Only find out if we need to.
				if ($block_level === false)
				{
					$this->addOpenTag($tag);
					break;
				}

				// The idea is, if we are LOOKING for a block level tag, we can close them on the way.
				if (isset($look_for[1]) && isset($this->bbc_codes[$look_for[0]]))
				{
					foreach ($this->bbc_codes[$look_for[0]] as $temp)
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
					$this->addOpenTag($tag);
					break;
				}
			}

			$to_close[] = $tag;
		} while ($tag[Codes::ATTR_TAG] !== $look_for);

		// Did we just eat through everything and not find it?
		if (!$this->hasOpenTags() && (empty($tag) || $tag[Codes::ATTR_TAG] !== $look_for))
		{
			$this->open_tags = $to_close;
			return;
		}
		elseif (!empty($to_close) && $tag[Codes::ATTR_TAG] !== $look_for)
		{
			if ($block_level === null && isset($look_for[0], $this->bbc_codes[$look_for[0]]))
			{
				foreach ($this->bbc_codes[$look_for[0]] as $temp)
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
				foreach ($to_close as $tag)
				{
					$this->addOpenTag($tag);
				}

				return;
			}
		}

		foreach ($to_close as $tag)
		{
			$tmp = $this->noSmileys($tag[Codes::ATTR_AFTER]);
			$this->message = substr_replace($this->message, $tmp, $this->pos, $closing_bracket_pos + 1 - $this->pos);
			$this->pos += strlen($tmp);
			$closing_bracket_pos = $this->pos - 1;

			// See the comment at the end of the big loop - just eating whitespace ;).
			if ($tag[Codes::ATTR_BLOCK_LEVEL] && isset($this->message[$this->pos]) && substr_compare($this->message, '<br />', $this->pos, 6) === 0)
			{
				$this->message = substr_replace($this->message, '', $this->pos, 6);
			}

			// Trim inside whitespace
			if (!empty($tag[Codes::ATTR_TRIM]) && $tag[Codes::ATTR_TRIM] !== Codes::TRIM_INSIDE)
			{
				$this->trimWhiteSpace($this->message, $this->pos + 1);
			}
		}

		if (!empty($to_close))
		{
			$this->pos--;
		}
	}

	// Node types: content, tag, param, closing tag
	protected function addNode($type, $string, $children = array())
	{

	}

	protected function getNode($type)
	{
		return array(
			'type' => $type,
			'children' => array(),
		);
	}
}