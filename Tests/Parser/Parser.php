<?php

// @todo change to \StringParser\BBC
namespace BBC;

use \BBC\Codes;

//define('BR', '<br />');
//define('BR_LEN', strlen(BR));

// Anywhere you see - 1 + 2 it's because you get rid of the ] and add 2 \n

class Parser
{
	const MAX_PERMUTE_ITERATIONS = 5040;

	protected $message;
	protected $bbc;
	protected $bbc_codes;
	protected $item_codes;
	protected $tags;
	protected $pos;
	protected $pos1;
	protected $pos2;
	protected $pos3;
	protected $last_pos;
	protected $do_smileys = true;
	// This is just the name of the tags that are open, by key
	protected $open_tags = array();
	// This is the actual tag that's open
	// @todo implement as SplStack
	protected $open_bbc = array();
	protected $do_autolink = true;
	protected $inside_tag;
	protected $autolink_search;
	protected $autolink_replace;

	private $original_msg;

	public function __construct(Codes $bbc)
	{
		$this->bbc = $bbc;

		$this->bbc_codes = $this->bbc->getForParsing();
		$this->item_codes = $this->bbc->getItemCodes();
		$this->loadAutolink();
	}

	public function resetParser()
	{
		$this->pos = null;
		$this->pos1 = null;
		$this->pos2 = null;
		$this->last_pos = null;
		$this->open_tags = array();
		$this->do_autolink = true;
		$this->inside_tag = null;
		$this->lastAutoPos = 0;
	}

	public function parse($message)
	{
		$this->message = $message;

		// Don't waste cycles
		if ($this->message === '')
		{
			return '';
		}

		// Clean up any cut/paste issues we may have
		$this->message = sanitizeMSCutPaste($this->message);

		// Unfortunately, this has to be done here because smileys are parsed as blocks between BBC
		// @todo remove from here and make the caller figure it out
		if (!$this->parsingEnabled())
		{
			if ($this->do_smileys)
			{
				parsesmileys($this->message);
			}

			return $this->message;
		}

		$this->resetParser();

		// @todo change this to <br> (it will break tests)
		$this->message = str_replace("\n", '<br />', $this->message);

		$this->pos = -1;
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
				break;
			}

			$tags = strtolower($this->message[$this->pos + 1]);

			// Possibly a closer?
			if ($tags === '/')
			{
				if($this->hasOpenTags())
				{
					// Next closing bracket after the first character
					$this->pos2 = strpos($this->message, ']', $this->pos + 1);

					// Playing games? string = [/]
					if ($this->pos2 === $this->pos + 2)
					{
						continue;
					}

					// Get everything between [/ and ]
					$look_for = strtolower(substr($this->message, $this->pos + 2, $this->pos2 - $this->pos - 2));
					$to_close = array();
					$block_level = null;

					do
					{
						// Get the last opened tag
						$tag = $this->closeOpenedTag(false);

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
						continue;
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

							continue;
						}
					}

					foreach ($to_close as $tag)
					{
						$tmp = $this->noSmileys($tag[Codes::ATTR_AFTER]);
						$this->message = substr_replace($this->message, $tmp, $this->pos, $this->pos2 + 1 - $this->pos);
						$this->pos += strlen($tmp);
						$this->pos2 = $this->pos - 1;

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

				// We don't allow / to be used for anything but the closing character, so this can't be a tag
				continue;
			}

			// No tags for this character, so just keep going (fastest possible course.)
			if (!isset($this->bbc_codes[$tags]))
			{
				continue;
			}

			$this->inside_tag = !$this->hasOpenTags() ? null : $this->getLastOpenedTag();

			if ($this->isItemCode($tags) && isset($this->message[$this->pos + 2]) && $this->message[$this->pos + 2] === ']' && !$this->bbc->isDisabled('list') && !$this->bbc->isDisabled('li'))
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
				$tag = $this->findTag($this->bbc_codes[$tags]);
			}

			// Implicitly close lists and tables if something other than what's required is in them. This is needed for itemcode.
			if ($tag === null && $this->inside_tag !== null && !empty($this->inside_tag[Codes::ATTR_REQUIRE_CHILDREN]))
			{
				$this->closeOpenedTag();
				$tmp = $this->noSmileys($this->inside_tag[Codes::ATTR_AFTER]);
				$this->message = substr_replace($this->message, $tmp, $this->pos, 0);
				$this->pos += strlen($tmp) - 1;
			}

			// No tag?  Keep looking, then.  Silly people using brackets without actual tags.
			if ($tag === null)
			{
				continue;
			}

			// Propagate the list to the child (so wrapping the disallowed tag won't work either.)
			if (isset($this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN]))
			{
				$tag[Codes::ATTR_DISALLOW_CHILDREN] = isset($tag[Codes::ATTR_DISALLOW_CHILDREN]) ? $tag[Codes::ATTR_DISALLOW_CHILDREN] + $this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN] : $this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN];
			}

			// Is this tag disabled?
			if ($this->bbc->isDisabled($tag[Codes::ATTR_TAG]))
			{
				$this->handleDisabled($tag);
			}

			// The only special case is 'html', which doesn't need to close things.
			if ($tag[Codes::ATTR_BLOCK_LEVEL] && $tag[Codes::ATTR_TAG] !== 'html' && !$this->inside_tag[Codes::ATTR_BLOCK_LEVEL])
			{
				$this->closeNonBlockLevel();
			}

			// This is the part where we actually handle the tags. I know, crazy how long it took.
			if($this->handleTag($tag))
			{
				continue;
			}

			// If this is block level, eat any breaks after it.
			if ($tag[Codes::ATTR_BLOCK_LEVEL] && isset($this->message[$this->pos + 1]) && substr_compare($this->message, '<br />', $this->pos + 1, 6) === 0)
				//if (!empty($tag[Codes::ATTR_BLOCK_LEVEL]) && substr($this->message, $this->pos + 1, 6) === '<br />')
			{
				$this->message = substr_replace($this->message, '', $this->pos + 1, 6);
				//$this->message = substr($this->message, 0, $this->pos + 1) . substr($this->message, $this->pos + 7);
			}

			// Are we trimming outside this tag?
			if (!empty($tag[Codes::ATTR_TRIM]) && $tag[Codes::ATTR_TRIM] !== Codes::TRIM_OUTSIDE)
			{
				$this->trimWhiteSpace($this->message, $this->pos + 1);
			}
		}

		// Close any remaining tags.
		while ($tag = $this->closeOpenedTag())
		{
			//$this->message .= "\n" . $tag[Codes::ATTR_AFTER] . "\n";
			$this->message .= $this->noSmileys($tag[Codes::ATTR_AFTER]);
		}

		$this->parseSmileys();

		if (isset($this->message[0]) && $this->message[0] === ' ')
		{
			$this->message = '&nbsp;' . substr($this->message, 1);
		}

		// Cleanup whitespace.
		// @todo remove \n because it should never happen after the explode/str_replace. Replace with str_replace
		$this->message = strtr($this->message, array('  ' => '&nbsp; ', "\r" => '', "\n" => '<br />', '<br /> ' => '<br />&nbsp;', '&#13;' => "\n"));

		// Finish footnotes if we have any.
		if (strpos($this->message, '<sup class="bbc_footnotes">') !== false)
		{
			$this->handleFootnotes();
		}

		// Allow addons access to what the parser created
		$message = $this->message;
		call_integration_hook('integrate_post_parsebbc', array(&$message));
		$this->message = $message;

		return $this->message;
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

	public function parsingEnabled()
	{
		return !empty($GLOBALS['modSettings']['enableBBC']);
	}

	protected function parseHTML(&$data)
	{
		global $modSettings;

		$data = preg_replace('~&lt;a\s+href=((?:&quot;)?)((?:https?://|mailto:)\S+?)\\1&gt;~i', '[url=$2]', $data);
		$data = preg_replace('~&lt;/a&gt;~i', '[/url]', $data);

		// <br /> should be empty.
		$empty_tags = array('br', 'hr');
		foreach ($empty_tags as $tag)
		{
			$data = str_replace(array('&lt;' . $tag . '&gt;', '&lt;' . $tag . '/&gt;', '&lt;' . $tag . ' /&gt;'), '[' . $tag . ' /]', $data);
		}

		// b, u, i, s, pre... basic tags.
		$closable_tags = array('b', 'u', 'i', 's', 'em', 'ins', 'del', 'pre', 'blockquote');
		foreach ($closable_tags as $tag)
		{
			$diff = substr_count($data, '&lt;' . $tag . '&gt;') - substr_count($data, '&lt;/' . $tag . '&gt;');
			$data = strtr($data, array('&lt;' . $tag . '&gt;' => '<' . $tag . '>', '&lt;/' . $tag . '&gt;' => '</' . $tag . '>'));

			if ($diff > 0)
			{
				$data = substr($data, 0, -1) . str_repeat('</' . $tag . '>', $diff) . substr($data, -1);
			}
		}

		// Do <img ... /> - with security... action= -> action-.
		preg_match_all('~&lt;img\s+src=((?:&quot;)?)((?:https?://)\S+?)\\1(?:\s+alt=(&quot;.*?&quot;|\S*?))?(?:\s?/)?&gt;~i', $data, $matches, PREG_PATTERN_ORDER);
		if (!empty($matches[0]))
		{
			$replaces = array();
			foreach ($matches[2] as $match => $imgtag)
			{
				$alt = empty($matches[3][$match]) ? '' : ' alt=' . preg_replace('~^&quot;|&quot;$~', '', $matches[3][$match]);

				// Remove action= from the URL - no funny business, now.
				if (preg_match('~action(=|%3d)(?!dlattach)~i', $imgtag) !== 0)
				{
					$imgtag = preg_replace('~action(?:=|%3d)(?!dlattach)~i', 'action-', $imgtag);
				}

				// Check if the image is larger than allowed.
				// @todo - We should seriously look at deprecating some of $this in favour of CSS resizing.
				if (!empty($modSettings['max_image_width']) && !empty($modSettings['max_image_height']))
				{
					// For images, we'll want $this.
					require_once(SUBSDIR . '/Attachments.subs.php');
					list ($width, $height) = url_image_size($imgtag);

					if (!empty($modSettings['max_image_width']) && $width > $modSettings['max_image_width'])
					{
						$height = (int) (($modSettings['max_image_width'] * $height) / $width);
						$width = $modSettings['max_image_width'];
					}

					if (!empty($modSettings['max_image_height']) && $height > $modSettings['max_image_height'])
					{
						$width = (int) (($modSettings['max_image_height'] * $width) / $height);
						$height = $modSettings['max_image_height'];
					}

					// Set the new image tag.
					$replaces[$matches[0][$match]] = '[img width=' . $width . ' height=' . $height . $alt . ']' . $imgtag . '[/img]';
				}
				else
					$replaces[$matches[0][$match]] = '[img' . $alt . ']' . $imgtag . '[/img]';
			}

			$data = strtr($data, $replaces);
		}
	}

	protected function autoLink(&$data)
	{
		if ($data === '' || $data === "\n")
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

		// Parse any URLs.... have to get rid of the @ problems some things cause... stupid email addresses.
		if (!$this->bbc->isDisabled('url') && (strpos($data, '://') !== false || strpos($data, 'www.') !== false))
		{
			// Switch out quotes really quick because they can cause problems.
			$data = str_replace(array('&#039;', '&nbsp;', '&quot;', '"', '&lt;'), array('\'', "\xC2\xA0", '>">', '<"<', '<lt<'), $data);

			$result = preg_replace($this->autolink_search, $this->autolink_replace, $data);

			// Only do this if the preg survives.
			if (is_string($result))
			{
				$data = $result;
			}

			// Switch those quotes back
			$data = str_replace(array('\'', "\xC2\xA0", '>">', '<"<', '<lt<'), array('&#039;', '&nbsp;', '&quot;', '"', '&lt;'), $data);
		}

		// Next, emails...
		if (!$this->bbc->isDisabled('email') && strpos($data, '@') !== false)
		{
			$data = preg_replace('~(?<=[\?\s\x{A0}\[\]()*\\\;>]|^)([\w\-\.]{1,80}@[\w\-]+\.[\w\-\.]+[\w\-])(?=[?,\s\x{A0}\[\]()*\\\]|$|<br />|&nbsp;|&gt;|&lt;|&quot;|&#039;|\.(?:\.|;|&nbsp;|\s|$|<br />))~u', '[email]$1[/email]', $data);
			$data = preg_replace('~(?<=<br />)([\w\-\.]{1,80}@[\w\-]+\.[\w\-\.]+[\w\-])(?=[?\.,;\s\x{A0}\[\]()*\\\]|$|<br />|&nbsp;|&gt;|&lt;|&quot;|&#039;)~u', '[email]$1[/email]', $data);
		}
	}

	protected function loadAutolink()
	{
		// @todo get rid of the FTP, nobody uses it
		$search = array(
			'~(?<=[\s>\.(;\'"]|^)((?:http|https)://[\w\-_%@:|]+(?:\.[\w\-_%]+)*(?::\d+)?(?:/[\p{L}\p{N}\-_\~%\.@!,\?&;=#(){}+:\'\\\\]*)*[/\p{L}\p{N}\-_\~%@\?;=#}\\\\])~ui',
			'~(?<=[\s>(\'<]|^)(www(?:\.[\w\-_]+)+(?::\d+)?(?:/[\p{L}\p{N}\-_\~%\.@!,\?&;=#(){}+:\'\\\\]*)*[/\p{L}\p{N}\-_\~%@\?;=#}\\\\])~ui'
		);
		$replace = array(
			'[url]$1[/url]',
			'[url=http://$1]$1[/url]'
		);

		call_integration_hook('integrate_autolink', array(&$search, &$replace, $this->bbc));

		$this->autolink_search = $search;
		$this->autolink_replace = $replace;
	}

	protected function findTag(array $possible_codes)
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

			// Not a match?
			if (substr_compare($this->message, $possible[Codes::ATTR_TAG], $this->pos + 1, $possible[Codes::ATTR_LENGTH], true) !== 0)
			{
				$last_check = $possible[Codes::ATTR_TAG];

				continue;
			}

			// The character after the possible tag or nothing
			$next_c = isset($this->message[$this->pos + 1 + $possible[Codes::ATTR_LENGTH]]) ? $this->message[$this->pos + 1 + $possible[Codes::ATTR_LENGTH]] : '';

			// This only happens if the tag is the last character of the string
			if ($next_c === '')
			{
				$last_check = $possible[Codes::ATTR_TAG];
				continue;
			}

			// A test validation?
			// @todo figure out if the regex need can use offset
			// this creates a copy of the entire message starting from this point!
			// @todo where do we know if the next char is ]?

			// Do we want parameters?
			if (!empty($possible[Codes::ATTR_PARAM]))
			{
				if ($next_c !== ' ')
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
			else
			{
				// Do we need an equal sign?
				if ($next_c !== '=' && in_array($possible[Codes::ATTR_TYPE], array(Codes::TYPE_UNPARSED_EQUALS, Codes::TYPE_UNPARSED_COMMAS, Codes::TYPE_UNPARSED_COMMAS_CONTENT, Codes::TYPE_UNPARSED_EQUALS_CONTENT, Codes::TYPE_PARSED_EQUALS)))
				{
					continue;
				}

				if ($next_c !== ']')
				{
					// An immediate ]?
					if ($possible[Codes::ATTR_TYPE] === Codes::TYPE_UNPARSED_CONTENT)
					{
						continue;
					}
					// Maybe we just want a /...
					elseif ($possible[Codes::ATTR_TYPE] === Codes::TYPE_CLOSED && substr_compare($this->message, '/]', $this->pos + 1 + $possible[Codes::ATTR_LENGTH], 2) !== 0 && substr_compare($this->message, ' /]', $this->pos + 1 + $possible[Codes::ATTR_LENGTH], 3) !== 0)
					{
						continue;
					}
				}
			}

			if (isset($possible[Codes::ATTR_TEST]) && preg_match('~^' . $possible[Codes::ATTR_TEST] . '~', substr($this->message, $this->pos + 2 + $possible[Codes::ATTR_LENGTH], strpos($this->message, ']', $this->pos) - ($this->pos + 2 + $possible[Codes::ATTR_LENGTH]))) === 0)
			{
				continue;
			}

			// Check allowed tree?
			if (isset($possible[Codes::ATTR_REQUIRE_PARENTS]) && ($this->inside_tag === null || !isset($possible[Codes::ATTR_REQUIRE_PARENTS][$this->inside_tag[Codes::ATTR_TAG]])))
			{
				continue;
			}

			if (isset($this->inside_tag[Codes::ATTR_REQUIRE_CHILDREN]) && !isset($this->inside_tag[Codes::ATTR_REQUIRE_CHILDREN][$possible[Codes::ATTR_TAG]]))
			{
				continue;
			}
			// If this is in the list of disallowed child tags, don't parse it.
			if (isset($this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN]) && isset($this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN][$possible[Codes::ATTR_TAG]]))
			{
				continue;
			}

			// Not allowed in this parent, replace the tags or show it like regular text
			if (isset($possible[Codes::ATTR_DISALLOW_PARENTS]) && ($this->inside_tag !== null && isset($possible[Codes::ATTR_DISALLOW_PARENTS][$this->inside_tag[Codes::ATTR_TAG]])))
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
				$match = $this->matchParameters($possible, $matches);

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

// @todo remove this. This is only for testing
//$GLOBALS['codes_used'][$GLOBALS['current_message']][] = $tag;
//$GLOBALS['codes_used_count'][$GLOBALS['current_message']][serialize($tag)] = isset($GLOBALS['codes_used_count'][$GLOBALS['current_message']][serialize($tag)]) ? $GLOBALS['codes_used_count'][$GLOBALS['current_message']][serialize($tag)] + 1 : 1;

		return $tag;
	}

	protected function handleItemCode()
	{
		$tag = $this->item_codes[$this->message[$this->pos + 1]];

		// First let's set up the tree: it needs to be in a list, or after an li.
		if ($this->inside_tag === null || ($this->inside_tag[Codes::ATTR_TAG] !== 'list' && $this->inside_tag[Codes::ATTR_TAG] !== 'li'))
		{
			$this->addOpenTag(array(
				Codes::ATTR_TAG => 'list',
				Codes::ATTR_TYPE => Codes::TYPE_PARSED_CONTENT,
				Codes::ATTR_AFTER => '</ul>',
				Codes::ATTR_BLOCK_LEVEL => true,
				Codes::ATTR_REQUIRE_CHILDREN => array('li'),
				Codes::ATTR_DISALLOW_CHILDREN => isset($this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN]) ? $this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN] : null,
				Codes::ATTR_LENGTH => 4,
				Codes::ATTR_AUTOLINK => true,
			));
			$code = '<ul' . ($tag === '' ? '' : ' style="list-style-type: ' . $tag . '"') . ' class="bbc_list">';
		}
		// We're in a list item already: another itemcode?  Close it first.
		elseif ($this->inside_tag[Codes::ATTR_TAG] === 'li')
		{
			$this->closeOpenedTag();
			$code = '</li>';
		}
		else
		{
			$code = '';
		}

		// Now we open a new tag.
		$this->addOpenTag(array(
			Codes::ATTR_TAG => 'li',
			Codes::ATTR_TYPE => Codes::TYPE_PARSED_CONTENT,
			Codes::ATTR_AFTER => '</li>',
			Codes::ATTR_TRIM => Codes::TRIM_OUTSIDE,
			Codes::ATTR_BLOCK_LEVEL => true,
			Codes::ATTR_DISALLOW_CHILDREN => isset($this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN]) ? $this->inside_tag[Codes::ATTR_DISALLOW_CHILDREN] : null,
			Codes::ATTR_AUTOLINK => true,
			Codes::ATTR_LENGTH => 2,
		));

		// First, open the tag...
		$code .= '<li>';

		$tmp = $this->noSmileys($code);
		$this->message = substr_replace($this->message, $tmp, $this->pos, 3);
		$this->pos += strlen($tmp) - 1;

		// Next, find the next break (if any.)  If there's more itemcode after it, keep it going - otherwise close!
		$this->pos2 = strpos($this->message, '<br />', $this->pos);
		$this->pos3 = strpos($this->message, '[/', $this->pos);

		$num_open_tags = count($this->open_tags);
		if ($this->pos2 !== false && ($this->pos3 === false || $this->pos2 <= $this->pos3))
		{
			// Can't use offset because of the ^
			preg_match('~^(<br />|&nbsp;|\s|\[)+~', substr($this->message, $this->pos2 + 6), $matches);

			// Keep the list open if the next character after the break is a [. Otherwise, close it.
			$replacement = (!empty($matches[0]) && substr_compare($matches[0], '[', -1, 1) === 0 ? '[/li]' : '[/li][/list]');
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

	protected function handleTypeParsedContext($tag)
	{
		// @todo Check for end tag first, so people can say "I like that [i] tag"?
		$this->addOpenTag($tag);
		$tmp = $this->noSmileys($tag[Codes::ATTR_BEFORE]);
		$this->message = substr_replace($this->message, $tmp, $this->pos, $this->pos1 - $this->pos);
		$this->pos += strlen($tmp) - 1;

		return false;
	}

	protected function handleTypeUnparsedContext($tag)
	{
		// Find the next closer
		$this->pos2 = stripos($this->message, '[/' . $tag[Codes::ATTR_TAG] . ']', $this->pos1);

		// No closer
		if ($this->pos2 === false)
		{
			return true;
		}

		// @todo figure out how to make this move to the validate part
		$data = substr($this->message, $this->pos1, $this->pos2 - $this->pos1);

		if (!empty($tag[Codes::ATTR_BLOCK_LEVEL]) && isset($data[0]) && substr_compare($data, '<br />', 0, 6) === 0)
		{
			$data = substr($data, 6);
		}

		if (isset($tag[Codes::ATTR_VALIDATE]))
		{
			$tag[Codes::ATTR_VALIDATE]($tag, $data, $this->bbc->getDisabled());
		}

		$code = strtr($tag[Codes::ATTR_CONTENT], array('$1' => $data));
		$tmp = $this->noSmileys($code);
		$this->message = substr_replace($this->message, $tmp, $this->pos, $this->pos2 + 3 + $tag[Codes::ATTR_LENGTH] - $this->pos);
		$this->pos += strlen($tmp) - 1;
		$this->last_pos = $this->pos + 1;

		return false;
	}

	protected function handleUnparsedEqualsContext($tag)
	{
		// The value may be quoted for some tags - check.
		if (isset($tag[Codes::ATTR_QUOTED]))
		{
			$quoted = substr_compare($this->message, '&quot;', $this->pos1, 6) === 0;
			if ($tag[Codes::ATTR_QUOTED] !== Codes::OPTIONAL && !$quoted)
			{
				return true;
			}

			if ($quoted)
			{
				$this->pos1 += 6;
			}
		}
		else
		{
			$quoted = false;
		}

		$this->pos2 = strpos($this->message, $quoted === false ? ']' : '&quot;]', $this->pos1);
		if ($this->pos2 === false)
		{
			return true;
		}

		$this->pos3 = stripos($this->message, '[/' . $tag[Codes::ATTR_TAG] . ']', $this->pos2);
		if ($this->pos3 === false)
		{
			return true;
		}

		$data = array(
			substr($this->message, $this->pos2 + ($quoted === false ? 1 : 7), $this->pos3 - ($this->pos2 + ($quoted === false ? 1 : 7))),
			substr($this->message, $this->pos1, $this->pos2 - $this->pos1)
		);

		if (!empty($tag[Codes::ATTR_BLOCK_LEVEL]) && substr_compare($data[0], '<br />', 0, 6) === 0)
		{
			$data[0] = substr($data[0], 6);
		}

		// Validation for my parking, please!
		if (isset($tag[Codes::ATTR_VALIDATE]))
		{
			$tag[Codes::ATTR_VALIDATE]($tag, $data, $this->bbc->getDisabled());
		}

		$code = strtr($tag[Codes::ATTR_CONTENT], array('$1' => $data[0], '$2' => $data[1]));
		$tmp = $this->noSmileys($code);
		$this->message = substr_replace($this->message, $tmp, $this->pos, $this->pos3 + 3 + $tag[Codes::ATTR_LENGTH] - $this->pos);
		$this->pos += strlen($tmp) - 1;

		return false;
	}

	protected function handleTypeClosed($tag)
	{
		$this->pos2 = strpos($this->message, ']', $this->pos);
		$tmp = $this->noSmileys($tag[Codes::ATTR_CONTENT]);
		$this->message = substr_replace($this->message, $tmp, $this->pos, $this->pos2 + 1 - $this->pos);
		$this->pos += strlen($tmp) - 1;

		return false;
	}

	protected function handleUnparsedCommasContext($tag)
	{
		$this->pos2 = strpos($this->message, ']', $this->pos1);
		if ($this->pos2 === false)
		{
			return true;
		}

		$this->pos3 = stripos($this->message, '[/' . $tag[Codes::ATTR_TAG] . ']', $this->pos2);
		if ($this->pos3 === false)
		{
			return true;
		}

		// We want $1 to be the content, and the rest to be csv.
		$data = explode(',', ',' . substr($this->message, $this->pos1, $this->pos2 - $this->pos1));
		$data[0] = substr($this->message, $this->pos2 + 1, $this->pos3 - $this->pos2 - 1);

		if (isset($tag[Codes::ATTR_VALIDATE]))
		{
			$tag[Codes::ATTR_VALIDATE]($tag, $data, $this->bbc->getDisabled());
		}

		$code = $tag[Codes::ATTR_CONTENT];
		foreach ($data as $k => $d)
		{
			$code = strtr($code, array('$' . ($k + 1) => trim($d)));
		}

		$tmp = $this->noSmileys($code);
		$this->message = substr_replace($this->message, $tmp, $this->pos, $this->pos3 + 3 + $tag[Codes::ATTR_LENGTH] - $this->pos);
		$this->pos += strlen($tmp) - 1;

		return false;
	}

	protected function handleUnparsedCommas($tag)
	{
		$this->pos2 = strpos($this->message, ']', $this->pos1);
		if ($this->pos2 === false)
		{
			return true;
		}

		$data = explode(',', substr($this->message, $this->pos1, $this->pos2 - $this->pos1));

		if (isset($tag[Codes::ATTR_VALIDATE]))
		{
			$tag[Codes::ATTR_VALIDATE]($tag, $data, $this->bbc->getDisabled());
		}

		// Fix after, for disabled code mainly.
		foreach ($data as $k => $d)
		{
			$tag[Codes::ATTR_AFTER] = strtr($tag[Codes::ATTR_AFTER], array('$' . ($k + 1) => trim($d)));
		}

		$this->addOpenTag($tag);

		// Replace them out, $1, $2, $3, $4, etc.
		$code = $tag[Codes::ATTR_BEFORE];
		foreach ($data as $k => $d)
		{
			$code = strtr($code, array('$' . ($k + 1) => trim($d)));
		}

		$tmp = $this->noSmileys($code);
		$this->message = substr_replace($this->message, $tmp, $this->pos, $this->pos2 + 1 - $this->pos);
		$this->pos += strlen($tmp) - 1;

		return false;
	}

	protected function handleEquals($tag)
	{
		// The value may be quoted for some tags - check.
		if (isset($tag[Codes::ATTR_QUOTED]))
		{
			$quoted = substr_compare($this->message, '&quot;', $this->pos1, 6) === 0;
			if ($tag[Codes::ATTR_QUOTED] !== Codes::OPTIONAL && !$quoted)
			{
				return true;
			}

			if ($quoted)
			{
				$this->pos1 += 6;
			}
		}
		else
		{
			$quoted = false;
		}

		$this->pos2 = strpos($this->message, $quoted === false ? ']' : '&quot;]', $this->pos1);
		if ($this->pos2 === false)
		{
			return true;
		}

		$data = substr($this->message, $this->pos1, $this->pos2 - $this->pos1);

		// Validation for my parking, please!
		if (isset($tag[Codes::ATTR_VALIDATE]))
		{
			$tag[Codes::ATTR_VALIDATE]($tag, $data, $this->bbc->getDisabled());
		}

		// For parsed content, we must recurse to avoid security problems.
		if ($tag[Codes::ATTR_TYPE] === Codes::TYPE_PARSED_EQUALS)
		{
			$this->recursiveParser($data, $tag);
		}

		$tag[Codes::ATTR_AFTER] = strtr($tag[Codes::ATTR_AFTER], array('$1' => $data));

		$this->addOpenTag($tag);

		$code = strtr($tag[Codes::ATTR_BEFORE], array('$1' => $data));
		$tmp = $this->noSmileys($code);
		$this->message = substr_replace($this->message, $tmp, $this->pos, $this->pos2 + ($quoted === false ? 1 : 7) - $this->pos);
		$this->pos += strlen($tmp) - 1;

		return false;
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

	// @todo I don't know what else to call this. It's the area that isn't a tag.
	protected function betweenTags()
	{
		// Make sure the $this->last_pos is not negative.
		$this->last_pos = max($this->last_pos, 0);

		// Pick a block of data to do some raw fixing on.
		$data = substr($this->message, $this->last_pos, $this->pos - $this->last_pos);

		// @todo $data seems to be \n a lot. Why? It got called 62 times in a test
		if ($data === "\n")
		{
			return;
		}

		// Take care of some HTML!
		if (!empty($GLOBALS['modSettings']['enablePostHTML']) && strpos($data, '&lt;') !== false)
		{
			// @todo new \Parser\BBC\HTML;
			$this->parseHTML($data);
		}

		// @todo is this sending tags like [/b] here?
		if (!empty($GLOBALS['modSettings']['autoLinkUrls']))
		{
			$this->autoLink($data);
		}

		// @todo can this be moved much earlier?
		// This cannot be moved earlier. It breaks tests
		$data = str_replace("\t", '&nbsp;&nbsp;&nbsp;', $data);

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
		$this->message = preg_replace_callback('~(%fn%(.*?)%fn%)~is', 'footnote_callback', $this->message);
		$fn_total += $fn_num;

		// If we have footnotes, add them in at the end of the message
		if (!empty($fn_num))
		{
			$this->message .= '<div class="bbc_footnotes">' . implode('', $fn_content) . '</div>';
		}
	}

	protected function handleDisabled(&$tag)
	{
		if (!isset($tag[Codes::ATTR_DISABLED_BEFORE]) && !isset($tag[Codes::ATTR_DISABLED_AFTER]) && !isset($tag[Codes::ATTR_DISABLED_CONTENT]))
		{
			$tag[Codes::ATTR_BEFORE] = !empty($tag[Codes::ATTR_BLOCK_LEVEL]) ? '<div>' : '';
			$tag[Codes::ATTR_AFTER] = !empty($tag[Codes::ATTR_BLOCK_LEVEL]) ? '</div>' : '';
			$tag[Codes::ATTR_CONTENT] = $tag[Codes::ATTR_TYPE] === Codes::TYPE_CLOSED ? '' : (!empty($tag[Codes::ATTR_BLOCK_LEVEL]) ? '<div>$1</div>' : '$1');
		}
		elseif (isset($tag[Codes::ATTR_DISABLED_BEFORE]) || isset($tag[Codes::ATTR_DISABLED_AFTER]))
		{
			$tag[Codes::ATTR_BEFORE] = isset($tag[Codes::ATTR_DISABLED_BEFORE]) ? $tag[Codes::ATTR_DISABLED_BEFORE] : (!empty($tag[Codes::ATTR_BLOCK_LEVEL]) ? '<div>' : '');
			$tag[Codes::ATTR_AFTER] = isset($tag[Codes::ATTR_DISABLED_AFTER]) ? $tag[Codes::ATTR_DISABLED_AFTER] : (!empty($tag[Codes::ATTR_BLOCK_LEVEL]) ? '</div>' : '');
		}
		else
		{
			$tag[Codes::ATTR_CONTENT] = $tag[Codes::ATTR_DISABLED_CONTENT];
		}
	}

	// @todo change to returning matches. If array() continue
	protected function matchParameters(array &$possible, &$matches)
	{
		if (!isset($possible['regex_cache']))
		{
			$possible['regex_cache'] = array();
			foreach ($possible[Codes::ATTR_PARAM] as $p => $info)
			{
				$quote = empty($info[Codes::PARAM_ATTR_QUOTED]) ? '' : '&quot;';
				$possible['regex_cache'][] = '(\s+' . $p . '=' . $quote . (isset($info[Codes::PARAM_ATTR_MATCH]) ? $info[Codes::PARAM_ATTR_MATCH] : '(.+?)') . $quote. ')' . (empty($info[Codes::PARAM_ATTR_OPTIONAL]) ? '' : '?');
			}
			$possible['regex_size'] = count($possible['regex_cache']) - 1;
			$possible['regex_keys'] = range(0, $possible['regex_size']);
		}

		// Okay, this may look ugly and it is, but it's not going to happen much and it is the best way
		// of allowing any order of parameters but still parsing them right.
		$message_stub = substr($this->message, $this->pos1 - 1);

		// If an addon adds many parameters we can exceed max_execution time, lets prevent that
		// 5040 = 7, 40,320 = 8, (N!) etc
		$max_iterations = self::MAX_PERMUTE_ITERATIONS;

		// Step, one by one, through all possible permutations of the parameters until we have a match
		do {
			$match_preg = '~^';
			foreach ($possible['regex_keys'] as $key)
			{
				$match_preg .= $possible['regex_cache'][$key];
			}
			$match_preg .= '\]~i';

			// Check if this combination of parameters matches the user input
			$match = preg_match($match_preg, $message_stub, $matches) !== 0;
		} while (!$match && --$max_iterations && ($possible['regex_keys'] = pc_next_permutation($possible['regex_keys'], $possible['regex_size'])));

		return $match;
	}

	// This allows to parse BBC in parameters like [quote author="[url]www.quotes.com[/quote]"]Something famous.[/quote]
	protected function recursiveParser(&$data, $tag)
	{
		// @todo if parsed tags allowed is empty, return?
		$bbc = clone $this->bbc;

		if (!empty($tag[Codes::ATTR_PARSED_TAGS_ALLOWED]))
		{
			$bbc->setParsedTags($tag[Codes::ATTR_PARSED_TAGS_ALLOWED]);
		}

		$parser = new \BBC\Parser($bbc);
		$data = $parser->enableSmileys(empty($tag[Codes::ATTR_PARSED_TAGS_ALLOWED]))->parse($data);
	}

	public function getBBC()
	{
		return $this->bbc_codes;
	}

	public function enableSmileys($enable = true)
	{
		$this->do_smileys = (bool) $enable;
		return $this;
	}

	protected function addOpenTag($tag)
	{
		$this->open_tags[] = $tag;
	}

	// if false, close the last one
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

	protected function hasOpenTags()
	{
		return !empty($this->open_tags);
	}

	protected function getLastOpenedTag()
	{
		return end($this->open_tags);
	}

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

	// There's not 1 test that the substr_replace() gets called here.
	protected function trimWhiteSpace(&$message, $offset = null)
	{
		if (preg_match('~(<br />|&nbsp;|\s)*~', $this->message, $matches, null, $offset) !== 0 && isset($matches[0]) && $matches[0] !== '')
		{
			$this->message = substr_replace($this->message, '', $this->pos, strlen($matches[0]));
		}
	}

	protected function insertAtCursor($string, $offset)
	{
		$this->message = substr_replace($this->message, $string, $offset, 0);
	}

	protected function removeChars($offset, $length)
	{
		$this->message = substr_replace($this->message, '', $offset, $length);
	}

	protected function setupTagParameters($possible, $matches)
	{
		$params = array();
		for ($i = 1, $n = count($matches); $i < $n; $i += 2)
		{
			$key = strtok(ltrim($matches[$i]), '=');

			if (isset($possible[Codes::ATTR_PARAM][$key][Codes::PARAM_ATTR_VALUE]))
			{
				$params['{' . $key . '}'] = strtr($possible[Codes::ATTR_PARAM][$key][Codes::PARAM_ATTR_VALUE], array('$1' => $matches[$i + 1]));
			}
			// @todo it's not validating it. it is filtering it
			elseif (isset($possible[Codes::ATTR_PARAM][$key][Codes::ATTR_VALIDATE]))
			{
				$params['{' . $key . '}'] = $possible[Codes::ATTR_PARAM][$key][Codes::ATTR_VALIDATE]($matches[$i + 1]);
			}
			else
			{
				$params['{' . $key . '}'] = $matches[$i + 1];
			}

			// Just to make sure: replace any $ or { so they can't interpolate wrongly.
			$params['{' . $key . '}'] = str_replace(array('$', '{'), array('&#036;', '&#123;'), $params['{' . $key . '}']);
		}

		foreach ($possible[Codes::ATTR_PARAM] as $p => $info)
		{
			if (!isset($params['{' . $p . '}']))
			{
				$params['{' . $p . '}'] = '';
			}
		}

		// We found our tag
		$tag = $possible;

		// Put the parameters into the string.
		if (isset($tag[Codes::ATTR_BEFORE]))
		{
			$tag[Codes::ATTR_BEFORE] = strtr($tag[Codes::ATTR_BEFORE], $params);
		}
		if (isset($tag[Codes::ATTR_AFTER]))
		{
			$tag[Codes::ATTR_AFTER] = strtr($tag[Codes::ATTR_AFTER], $params);
		}
		if (isset($tag[Codes::ATTR_CONTENT]))
		{
			$tag[Codes::ATTR_CONTENT] = strtr($tag[Codes::ATTR_CONTENT], $params);
		}

		$this->pos1 += strlen($matches[0]) - 1;

		return $tag;
	}

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

	protected function isItemCode($char)
	{
		return isset($this->item_codes[$char]);
	}

	protected function closeNonBlockLevel()
	{
		$n = count($this->open_tags) - 1;
		while (empty($this->open_tags[$n][Codes::ATTR_BLOCK_LEVEL]) && $n >= 0)
		{
			$n--;
		}

		// Close all the non block level tags so this tag isn't surrounded by them.
		for ($i = count($this->open_tags) - 1; $i > $n; $i--)
		{
			$tmp = $this->noSmileys($this->open_tags[$i][Codes::ATTR_AFTER]);
			$this->message = substr_replace($this->message, $tmp, $this->pos, 0);
			$ot_strlen = strlen($tmp);
			$this->pos += $ot_strlen;
			$this->pos1 += $ot_strlen;

			// Trim or eat trailing stuff... see comment at the end of the big loop.
			if (!empty($this->open_tags[$i][Codes::ATTR_BLOCK_LEVEL]) && substr_compare($this->message, '<br />', $this->pos, 6) === 0)
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

	protected function noSmileys($string)
	{
		return "\n" . $string . "\n";
	}

	protected function parseSmileys()
	{
		// Parse the smileys within the parts where it can be done safely.
		if ($this->do_smileys === true)
		{
			$message_parts = explode("\n", $this->message);

			for ($i = 0, $n = count($message_parts); $i < $n; $i += 2)
			{
				parsesmileys($message_parts[$i]);
				//parsesmileys($this->message);
			}

			$this->message = implode('', $message_parts);
		}
		// No smileys, just get rid of the markers.
		else
		{
			$this->message = str_replace("\n", '', $this->message);
		}
	}

	// Rearranges all parameters to be in the right order.  Returns TRUE if no parameters are leftover.
	function fix_param_order($message, &$parameters, &$replace_str, &$tpos)
	{
		$pos = 0;
		$test = substr($message, 0, $tpos = strpos($message, ']'));
		while (substr_count($test, '"') % 2 !== 0)
		{
			$tpos += ($pos1 = strpos(substr($message, $tpos), '"'));
			if ($pos1 === false)
				break;
			$test = substr($message, 0, ($pos += strpos(substr($message, $tpos), ']')));
		}
		$params = explode(' ', $test);
		unset($params[0]);
		$order = array();
		$replace_str = $old = '';
		foreach ($params as $param)
		{
			if (strpos($param, '=') === false)
				$order[$old] .= ' ' . $param;
			else
				$order[$old = substr($param, 0, strpos($param, '='))] = substr($param, strpos($param, '=') + 1);
		}
		foreach ($parameters as $key => $ignore)
		{
			$replace_str .= (isset($order[$key]) ? ' ' . $key . '=' . $order[$key] : '');
			unset($order[$key]);
		}
		return count($order) == 0;
	}

	function dougiefresh()
	{
		// Reorganize the parameter list, then compare the result.  Continue if found:
		if (!fix_param_order(($test = substr($this->message, $this->pos1 - 1)), $possible['parameters'], $replace_str, $tpos))
			return true;
		$preg = '';
		foreach ($possible['parameters'] as $p => $info)
			$preg .= '(\s+' . $p . '=' . (empty($info['quoted']) ? '' : '&quot;') . (isset($info['match']) ? $info['match'] : '(.+?)') . (empty($info['quoted']) ? '' : '&quot;') . ')' . (empty($info['optional']) ? '' : '?');
		if (!preg_match('~^' . $preg . '\]~i', ($replace_str .= substr($test, $tpos)), $matches))
			return true;
		$message = substr($message, 0, $pos1 - 1) . $replace_str;
	}
}