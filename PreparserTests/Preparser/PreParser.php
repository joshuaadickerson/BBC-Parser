<?php

// @todo change the methods called on each part so that it creates an array of search/replace and do a single preg_replace(). Obviously callbacks are different
class PreParser
{
	protected $message;
	protected $fix_search = array();
	protected $fix_replace = array();

	public function __construct()
	{
		$this->setFixes();
	}

	/**
	 * Takes a message and parses it, returning the prepared message as a reference.
	 *
	 * - Cleans up links (javascript, etc.) and code/quote sections.
	 * - Won't convert \n's and a few other things if previewing is true.
	 *
	 * @param string $message
	 * @param boolean $previewing = false
	 */
	public function parse(&$message, $previewing = false)
	{
		$this->message = $message;

		// Remove \r's... they're evil!
		$this->message = str_replace(array("\r"), array(''), $this->message);

		// @todo sanitizeMSCutPaste()?

		$this->fixWrongCharset();

		// You won't believe this - but too many periods upsets apache it seems!
		$this->message = preg_replace('~\.{100,}~', '...', $this->message);

		// Hoping this makes parsing faster
		$has = array(
			'nobbc' => strpos($this->message, '[nobbc') !== false,
			'quote' => strpos($this->message, '[quote') !== false,
			'code' => strpos($this->message, '[code') !== false,
			'html' => strpos($this->message, '[html') !== false,
			'list' => strpos($this->message, '[list') !== false,
			'table' => strpos($this->message, '[table') !== false,
			'color' => strpos($this->message, '[color') !== false,
			'me' => strpos($this->message, '/me') !== false,
		);

		call_integration_hook('integrate_preparse_code_has', array(&$has, $this->message, $previewing));

		// Clean up after nobbc ;).
		if ($has['nobbc'])
		{
			$this->fixNoBBC();
		}

		if ($has['quote'])
		{
			$this->fixTrailingQuotes();
		}

		if ($has['code'])
		{
			$this->fixCode();
		}

		// @todo only split if we have code tag?
		// Now that we've fixed all the code tags, let's fix the img and url tags...
		$parts = preg_split('~(\[/code\]|\[code(?:=[^\]]+)?\])~i', $this->message, -1, PREG_SPLIT_DELIM_CAPTURE);

		// Only mess with stuff outside [code] tags.
		for ($i = 0, $n = count($parts); $i < $n; $i++)
		{
			// It goes 0 = outside, 1 = begin tag, 2 = inside, 3 = close tag, repeat.
			if ($i % 4 == 0)
			{
				fixTags($parts[$i]);

				// Replace /me.+?\n with [me=name]dsf[/me]\n.
				if ($has['me'])
				{
					$this->doMe($parts[$i]);
				}

				// @todo this doesn't really make a difference with the parser since it lowercases the tags anyway. Maybe get rid of it?
				// Make sure all tags are lowercase.
				$this->lowercaseTags($parts[$i]);

				if ($has['list'])
				{
					$this->fixLists($parts[$i]);
				}

				// @todo why 3?
				// Fix up some use of tables without [tr]s, etc. (it has to be done more than once to catch it all.)
				for ($j = 0; $j < 3; $j++)
				{
					$parts[$i] = preg_replace($this->fix_search, $this->fix_replace, $parts[$i]);
				}

				// Remove empty bbc from the sections outside the code tags
				$this->removeEmpty($parts[$i]);

				// Fix color tags of many forms so they parse properly
				if ($has['color'])
				{
					$this->fixColors($parts[$i]);
				}
			}

			// @todo I feel like cases where this call is in a loop we could save an isset() if we just got the function calls
			call_integration_hook('integrate_preparse_code', array(&$parts[$i], $i, $previewing));
		}

		// Put it back together!
		if (!$previewing)
		{
			$this->message = strtr(implode('', $parts), array('  ' => '&nbsp; ', "\n" => '<br />', "\xC2\xA0" => '&nbsp;'));
		}
		else
		{
			$this->message = strtr(implode('', $parts), array('  ' => '&nbsp; ', "\xC2\xA0" => '&nbsp;'));
		}

		// Now we're going to do full scale table checking...
		if ($has['table'])
		{
			$this->table();
		}

		// @todo can this be moved earlier? Like to the first str_replace()?
		// Now let's quickly clean up things that will slow our parser (which are common in posted code.)
		$this->message = strtr($this->message, array('[]' => '&#91;]', '[&#039;' => '&#91;&#039;'));

		$message = $this->message;
	}

	public function unparse($message)
	{
		$parts = preg_split('~(\[/code\]|\[code(?:=[^\]]+)?\])~i', $message, -1, PREG_SPLIT_DELIM_CAPTURE);

		// We're going to unparse only the stuff outside [code]...
		for ($i = 0, $n = count($parts); $i < $n; $i++)
		{
			call_integration_hook('integrate_unpreparse_code', array(&$message, &$parts, &$i));
		}

		// Change breaks back to \n's and &nsbp; back to spaces.
		return preg_replace('~<br( /)?' . '>~', "\n", str_replace('&nbsp;', ' ', implode('', $parts)));
	}

	protected function setFixes()
	{
		$mistake_fixes = array(
			// Find [table]s not followed by [tr].
			'~\[table\](?![\s\x{A0}]*\[tr\])~su' => '[table][tr]',
			// Find [tr]s not followed by [td] or [th]
			'~\[tr\](?![\s\x{A0}]*\[t[dh]\])~su' => '[tr][td]',
			// Find [/td] and [/th]s not followed by something valid.
			'~\[/t([dh])\](?![\s\x{A0}]*(?:\[t[dh]\]|\[/tr\]|\[/table\]))~su' => '[/t$1][/tr]',
			// Find [/tr]s not followed by something valid.
			'~\[/tr\](?![\s\x{A0}]*(?:\[tr\]|\[/table\]))~su' => '[/tr][/table]',
			// Find [/td] [/th]s incorrectly followed by [/table].
			'~\[/t([dh])\][\s\x{A0}]*\[/table\]~su' => '[/t$1][/tr][/table]',
			// Find [table]s, [tr]s, and [/td]s (possibly correctly) followed by [td].
			'~\[(table|tr|/td)\]([\s\x{A0}]*)\[td\]~su' => '[$1]$2[_td_]',
			// Now, any [td]s left should have a [tr] before them.
			'~\[td\]~s' => '[tr][td]',
			// Look for [tr]s which are correctly placed.
			'~\[(table|/tr)\]([\s\x{A0}]*)\[tr\]~su' => '[$1]$2[_tr_]',
			// Any remaining [tr]s should have a [table] before them.
			'~\[tr\]~s' => '[table][tr]',
			// Look for [/td]s or [/th]s followed by [/tr].
			'~\[/t([dh])\]([\s\x{A0}]*)\[/tr\]~su' => '[/t$1]$2[_/tr_]',
			// Any remaining [/tr]s should have a [/td].
			'~\[/tr\]~s' => '[/td][/tr]',

			// @todo https://github.com/SimpleMachines/SMF2.1/issues/3106
			// Look for properly opened [li]s which aren't closed.
			'~\[li\]([^\[\]]+?)\[li\]~s' => '[li]$1[_/li_][_li_]',
			'~\[li\]([^\[\]]+?)\[/list\]~s' => '[_li_]$1[_/li_][/list]',
			'~\[li\]([^\[\]]+?)$~s' => '[li]$1[/li]',
			// Lists - find correctly closed items/lists.
			'~\[/li\]([\s\x{A0}]*)\[/list\]~su' => '[_/li_]$1[/list]',
			// Find list items closed and then opened.
			'~\[/li\]([\s\x{A0}]*)\[li\]~su' => '[_/li_]$1[_li_]',
			// Now, find any [list]s or [/li]s followed by [li].
			'~\[(list(?: [^\]]*?)?|/li)\]([\s\x{A0}]*)\[li\]~su' => '[$1]$2[_li_]',
			// Allow for sub lists.
			'~\[/li\]([\s\x{A0}]*)\[list\]~u' => '[_/li_]$1[list]',
			'~\[/list\]([\s\x{A0}]*)\[li\]~u' => '[/list]$1[_li_]',
			// Any remaining [li]s weren't inside a [list].
			'~\[li\]~' => '[list][li]',
			// Any remaining [/li]s weren't before a [/list].
			'~\[/li\]~' => '[/li][/list]',
			// Put the correct ones back how we found them.
			'~\[_(li|/li|td|tr|/tr)_\]~' => '[$1]',

			// Images with no real url.
			'~\[img\]https?://.{0,7}\[/img\]~' => '',

			// Font tags with multiple fonts (copy&paste in the WYSIWYG by some browsers).
			'~\[font=\\\'?(.*?)\\\'?(?=\,[ \'\"A-Za-z]*\]).*?\](.*?(?:\[/font\]))~s'  => '[font=$1]$2'
		);

		call_integration_hook('integrate_preparse_fixes', array(&$mistake_fixes));

		$this->fix_search = array_keys($mistake_fixes);
		$this->fix_replace = array_values($mistake_fixes);
	}

	/**
	 * Fix color tags of many forms so they parse properly
	 * @param $part
	 */
	protected function fixColors(&$part)
	{
		$part = preg_replace('~\[color=(?:#[\da-fA-F]{3}|#[\da-fA-F]{6}|[A-Za-z]{1,20}|rgb\(\d{1,3}, ?\d{1,3}, ?\d{1,3}\))\]\s*\[/color\]~', '', $part);
	}

	/**
	 * Close any open lists
	 * @param $part
	 */
	protected function fixLists(&$part)
	{
		$list_open = substr_count($part, '[list]') + substr_count($part, '[list ');
		$list_close = substr_count($part, '[/list]');

		if ($list_close - $list_open > 0)
		{
			$part = str_repeat('[list]', $list_close - $list_open) . $part;
		}

		if ($list_open - $list_close > 0)
		{
			$part = $part . str_repeat('[/list]', $list_open - $list_close);
		}
	}

	/**
	 * This makes all languages *theoretically* work even with the wrong charset ;)
	 */
	protected function fixWrongCharset()
	{
		$this->message = preg_replace('~&amp;#(\d{4,5}|[2-9]\d{2,4}|1[2-9]\d);~', '&#$1;', $this->message);
	}

	protected function fixNoBBC()
	{
		$this->message = preg_replace_callback('~\[nobbc\](.+?)\[/nobbc\]~is', array($this, 'nobbc_callback'), $this->message);
	}

	/**
	 * Remove any trailing quote tags
	 */
	protected function fixTrailingQuotes()
	{
		// Trim off trailing quotes - these often happen by accident.
		while (substr($this->message, -7) === '[quote]')
		{
			$this->message = substr($this->message, 0, -7);
		}

		while (substr($this->message, 0, 8) === '[/quote]')
		{
			$this->message = substr($this->message, 8);
		}
	}

	/**
	 * Changes /me to [me]
	 * @param string &$part
	 */
	protected function doMe(&$part)
	{
		global $user_info;

		// Replace /me.+?\n with [me=name]dsf[/me]\n.
		if (preg_match('~[\[\]\\"]~', $user_info['name']) !== false)
		{
			$part = preg_replace('~(\A|\n)/me(?: |&nbsp;)([^\n]*)(?:\z)?~i', '$1[me=&quot;' . $user_info['name'] . '&quot;]$2[/me]', $part);
			$part = preg_replace('~(\[footnote\])/me(?: |&nbsp;)([^\n]*?)(\[\/footnote\])~i', '$1[me=&quot;' . $user_info['name'] . '&quot;]$2[/me]$3', $part);
		}
		else
		{
			$part = preg_replace('~(\A|\n)/me(?: |&nbsp;)([^\n]*)(?:\z)?~i', '$1[me=' . $user_info['name'] .  ']$2[/me]', $part);
			$part = preg_replace('~(\[footnote\])/me(?: |&nbsp;)([^\n]*?)(\[\/footnote\])~i', '$1[me=' . $user_info['name'] . ']$2[/me]$3', $part);
		}
	}

	// @todo move the search array to the constructor and add a hook to add more
	protected function removeEmpty(&$part)
	{
		// Remove empty bbc from the sections outside the code tags
		$part = preg_replace(
			array(
				'~\[[bisu]\]\s*\[/[bisu]\]~',
				'~\[quote\]\s*\[/quote\]~',
			),
			'',
			$part
		);
	}

	protected function fixCode()
	{
		// Find all code blocks, work out whether we'd be parsing them, then ensure they are all closed.
		$in_tag = false;
		$had_tag = false;
		$codeopen = 0;
		if (preg_match_all('~(\[(/)*code(?:=[^\]]+)?\])~is', $this->message, $matches))
		{
			foreach ($matches[0] as $index => $dummy)
			{
				// Closing?
				if (!empty($matches[2][$index]))
				{
					// If it's closing and we're not in a tag we need to open it...
					if (!$in_tag)
					{
						$codeopen = true;
					}
					// Either way we ain't in one any more.
					$in_tag = false;
				}
				// Opening tag...
				else
				{
					$had_tag = true;
					// If we're in a tag don't do nought!
					if (!$in_tag)
					{
						$in_tag = true;
					}
				}
			}
		}
		// If we have an open tag, close it.
		if ($in_tag)
		{
			$this->message .= '[/code]';
		}
		// Open any ones that need to be open, only if we've never had a tag.
		if ($codeopen && !$had_tag)
		{
			$this->message = '[code]' . $this->message;
		}
	}

	/**
	 * Ensure tags inside of nobbc do not get parsed by converting the markers to html entities
	 *
	 * @param string[] $matches
	 * @return string
	 */
	protected function nobbc_callback(array $matches)
	{
		// @todo str_replace
		return '[nobbc]' . strtr($matches[1], array('[' => '&#91;', ']' => '&#93;', ':' => '&#58;', '@' => '&#64;')) . '[/nobbc]';
	}

	/**
	 * Takes a tag and lowercases it
	 *
	 * @param string[] $matches
	 * @return string
	 */
	protected function lowertags_callback(array $matches)
	{
		return '[' . $matches[1] . strtolower($matches[2]) . $matches[3] . ']';
	}

	/**
	 * Fix any URLs posted - ie. remove 'javascript:'.
	 *
	 * - Used by preparsecode, fixes links in message and returns nothing.
	 */
	protected function fixTags()
	{
		global $modSettings;

		// WARNING: Editing the below can cause large security holes in your forum.
		// Edit only if you are sure you know what you are doing.
		$fixArray = array(
			// [img]http://...[/img] or [img width=1]http://...[/img]
			array(
				'tag' => 'img',
				'protocols' => array('http', 'https'),
				'embeddedUrl' => false,
				'hasEqualSign' => false,
				'hasExtra' => true,
			),
			// [url]http://...[/url]
			array(
				'tag' => 'url',
				'protocols' => array('http', 'https'),
				'embeddedUrl' => true,
				'hasEqualSign' => false,
			),
			// [url=http://...]name[/url]
			array(
				'tag' => 'url',
				'protocols' => array('http', 'https'),
				'embeddedUrl' => true,
				'hasEqualSign' => true,
			),
			// [iurl]http://...[/iurl]
			array(
				'tag' => 'iurl',
				'protocols' => array('http', 'https'),
				'embeddedUrl' => true,
				'hasEqualSign' => false,
			),
			// [iurl=http://...]name[/iurl]
			array(
				'tag' => 'iurl',
				'protocols' => array('http', 'https'),
				'embeddedUrl' => true,
				'hasEqualSign' => true,
			),
		);

		$message = $this->message;
		call_integration_hook('integrate_fixtags', array(&$fixArray, &$message));
		$this->message = $message;

		// Fix each type of tag.
		foreach ($fixArray as $param)
		{
			$this->fixTag($this->message, $param['tag'], $param['protocols'], $param['embeddedUrl'], $param['hasEqualSign'], !empty($param['hasExtra']));
		}

		// Now fix possible security problems with images loading links automatically...
		$this->message = preg_replace_callback('~(\[img.*?\])(.+?)\[/img\]~is', array($this, 'fixTags_img_callback'), $this->message);

		// Limit the size of images posted?
		if (!empty($modSettings['max_image_width']) || !empty($modSettings['max_image_height']))
		{
			resizeBBCImages($this->message);
		}
	}

	/**
	 * Ensure image tags do not load anything by themselfs (security)
	 *
	 * @param string[] $matches
	 * @return string
	 */
	protected function fixTags_img_callback($matches)
	{
		return $matches[1] . preg_replace('~action(=|%3d)(?!dlattach)~i', 'action-', $matches[2]) . '[/img]';
	}

	/**
	 * Fix a specific class of tag - ie. url with =.
	 *
	 * - Used by fixTags, fixes a specific tag's links.
	 *
	 * @param string $message
	 * @param string $myTag - the tag
	 * @param array $protocols - http or ftp
	 * @param bool $embeddedUrl = false - whether it *can* be set to something
	 * @param bool $hasEqualSign = false, whether it *is* set to something
	 * @param bool $hasExtra = false - whether it can have extra cruft after the begin tag.
	 */
	protected function fixTag(&$message, $myTag, array $protocols, $embeddedUrl = false, $hasEqualSign = false, $hasExtra = false)
	{
		global $boardurl, $scripturl;

		if (preg_match('~^([^:]+://[^/]+)~', $boardurl, $match) != 0)
		{
			$domain_url = $match[1];
		}
		else
		{
			$domain_url = $boardurl . '/';
		}

		$replaces = array();

		if ($hasEqualSign)
		{
			preg_match_all('~\[(' . $myTag . ')=([^\]]*?)\](?:(.+?)\[/(' . $myTag . ')\])?~is', $message, $matches);
		}
		else
		{
			preg_match_all('~\[(' . $myTag . ($hasExtra ? '(?:[^\]]*?)' : '') . ')\](.+?)\[/(' . $myTag . ')\]~is', $message, $matches);
		}

		foreach ($matches[0] as $k => $dummy)
		{
			// Remove all leading and trailing whitespace.
			$replace = trim($matches[2][$k]);
			$this_tag = $matches[1][$k];
			$this_close = $hasEqualSign ? (empty($matches[4][$k]) ? '' : $matches[4][$k]) : $matches[3][$k];
			$found = false;

			foreach ($protocols as $protocol)
			{
				$found = strncasecmp($replace, $protocol . '://', strlen($protocol) + 3) === 0;
				if ($found)
				{
					break;
				}
			}

			if (!$found && $protocols[0] === 'http')
			{
				if ($replace[0] === '/')
				{
					$replace = $domain_url . $replace;
				}
				elseif ($replace[0] == '?')
				{
					$replace = $scripturl . $replace;
				}
				elseif ($replace[0] === '#' && $embeddedUrl)
				{
					$replace = '#' . preg_replace('~[^A-Za-z0-9_\-#]~', '', substr($replace, 1));
					$this_tag = 'iurl';
					$this_close = 'iurl';
				}
				else
				{
					$replace = $protocols[0] . '://' . $replace;
				}
			}
			// @todo remove ftp
			elseif (!$found && $protocols[0] === 'ftp')
			{
				$replace = $protocols[0] . '://' . preg_replace('~^(?!ftps?)[^:]+://~', '', $replace);
			}
			elseif (!$found)
			{
				$replace = $protocols[0] . '://' . $replace;
			}

			if ($hasEqualSign && $embeddedUrl)
			{
				$replaces[$matches[0][$k]] = '[' . $this_tag . '=' . $replace . ']' . (empty($matches[4][$k]) ? '' : $matches[3][$k] . '[/' . $this_close . ']');
			}
			elseif ($hasEqualSign)
			{
				$replaces['[' . $matches[1][$k] . '=' . $matches[2][$k] . ']'] = '[' . $this_tag . '=' . $replace . ']';
			}
			elseif ($embeddedUrl)
			{
				$replaces['[' . $matches[1][$k] . ']' . $matches[2][$k] . '[/' . $matches[3][$k] . ']'] = '[' . $this_tag . '=' . $replace . ']' . $matches[2][$k] . '[/' . $this_close . ']';
			}
			else
			{
				$replaces['[' . $matches[1][$k] . ']' . $matches[2][$k] . '[/' . $matches[3][$k] . ']'] = '[' . $this_tag . ']' . $replace . '[/' . $this_close . ']';
			}
		}

		foreach ($replaces as $k => $v)
		{
			if ($k == $v)
			{
				unset($replaces[$k]);
			}
		}

		if (!empty($replaces))
		{
			$message = strtr($message, $replaces);
		}
	}

	/**
	 * Validates and corrects table structure
	 *
	 * What it does
	 * - Checks tables for correct tag order / nesting
	 * - Adds in missing closing tags, removes excess closing tags
	 * - Although it prevents markup error, it can mess-up the intended (abiet wrong) layout
	 * driving the post author in to a furious rage
	 */
	protected function table()
	{
		$table_check = $this->message;
		$table_offset = 0;
		$table_array = array();

		// Define the allowable tags after a give tag
		$table_order = array(
			'table' => 'td',
			'table' => 'th',
			'tr' => 'table',
			'td' => 'tr',
			'th' => 'tr',
		);

		// Find all closing tags (/table /tr /td etc)
		while (preg_match('~\[(/)*(table|tr|td|th)\]~', $table_check, $matches) != false)
		{
			// Keep track of where this is.
			$offset = strpos($table_check, $matches[0]);
			$remove_tag = false;
			// Is it opening?
			if ($matches[1] != '/')
			{
				// If the previous table tag isn't correct simply remove it.
				if ((!empty($table_array) && $table_array[0] !== $table_order[$matches[2]]) || (empty($table_array) && $matches[2] !== 'table'))
				{
					$remove_tag = true;
				}
				// Record this was the last tag.
				else
				{
					array_unshift($table_array, $matches[2]);
				}
			}
			// Otherwise is closed!
			else
			{
				// Only keep the tag if it's closing the right thing.
				if (empty($table_array) || ($table_array[0] != $matches[2]))
				{
					$remove_tag = true;
				}
				else
				{
					array_shift($table_array);
				}
			}

			// Removing?
			if ($remove_tag)
			{
				// @todo substr_replace
				$this->message = substr($this->message, 0, $table_offset + $offset) . substr($this->message, $table_offset + strlen($matches[0]) + $offset);
				// We've lost some data.
				$table_offset -= strlen($matches[0]);
			}

			// Remove everything up to here.
			$table_offset += $offset + strlen($matches[0]);
			$table_check = substr($table_check, $offset + strlen($matches[0]));
		}

		// Close any remaining table tags.
		foreach ($table_array as $tag)
		{
			$this->message .= '[/' . $tag . ']';
		}
	}

	protected function lowercaseTags(&$part)
	{
		// Make sure all tags are lowercase.
		$part = preg_replace_callback('~\[([/]?)(list|li|table|tr|td|th)((\s[^\]]+)*)\]~i', array($this, 'lowertags_callback'), $part);
	}

	protected function fixMistakes(&$part)
	{
		// Fix up some use of tables without [tr]s, etc. (it has to be done more than once to catch it all.)
		for ($j = 0; $j < 3; $j++)
		{
			$part = preg_replace(array_keys($this->fix_search), $this->fix_replace, $part);
		}
	}
}