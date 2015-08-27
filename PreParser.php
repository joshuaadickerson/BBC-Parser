<?php

class PreParser
{
	protected $message;
	protected $fix_search = array();
	protected $fix_replace = array();
	protected $nbsp;

	public function __construct()
	{
		global $context;

		$this->nbsp = $context['utf8'] ? '\x{A0}' : '\xA0';
	}

	public function parse($message)
	{
		$this->message = $message;

		// Remove \r's... they're evil!
		$this->message = str_replace(array("\r"), array(''), $this->message);

		// @todo sanitizeMSCutPaste()?

		$this->fixWrongCharset();

		$has = array(
			'nobbc' => strpos($message, '[nobbc') !== false,
			'quote' => strpos($message, '[quote') !== false,
			'code' => strpos($message, '[code') !== false,
			'html' => strpos($message, '[html') !== false,
		);

		$message = $this->message;
	}

	public function unparse()
	{

	}

	public function resetParser()
	{

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
		$this->message = preg_replace_callback('~\[nobbc\](.+?)\[/nobbc\]~is', function ($a)
		{
			// @todo change to str_replace
			return '[nobbc]' . strtr($a[1], array('[' => '&#91;', ']' => '&#93;', ':' => '&#58;', '@' => '&#64;')) . '[/nobbc]';
		}, $this->message);
	}

	// @todo change to substr_compare()
	protected function fixTrailingQuotes()
	{
		// Trim off trailing quotes - these often happen by accident.
		while (substr($this->message, -7) == '[quote]')
		{
			$this->message = substr($this->message, 0, -7);
		}

		while (substr($this->message, 0, 8) == '[/quote]')
		{
			$this->message = substr($this->message, 8);
		}
	}
}

/**
 * Takes a message and parses it, returning nothing.
 * Cleans up links (javascript, etc.) and code/quote sections.
 * Won't convert \n's and a few other things if previewing is true.
 *
 * @param string $message The mesasge
 * @param bool $previewing Whether we're previewing
 */
function preparsecode(&$message, $previewing = false)
{
	global $user_info, $modSettings, $context;
	// This line makes all languages *theoretically* work even with the wrong charset ;).
	$message = preg_replace('~&amp;#(\d{4,5}|[2-9]\d{2,4}|1[2-9]\d);~', '&#$1;', $message);
	// Clean up after nobbc ;).
	$message = preg_replace_callback('~\[nobbc\](.+?)\[/nobbc\]~is', function ($a)
	{
		return '[nobbc]' . strtr($a[1], array('[' => '&#91;', ']' => '&#93;', ':' => '&#58;', '@' => '&#64;')) . '[/nobbc]';
	}, $message);
	// Remove \r's... they're evil!
	$message = strtr($message, array("\r" => ''));
	// You won't believe this - but too many periods upsets apache it seems!
	$message = preg_replace('~\.{100,}~', '...', $message);
	// Trim off trailing quotes - these often happen by accident.
	while (substr($message, -7) == '[quote]')
	{
		$message = substr($message, 0, -7);
	}
	while (substr($message, 0, 8) == '[/quote]')
	{
		$message = substr($message, 8);
	}
	// Find all code blocks, work out whether we'd be parsing them, then ensure they are all closed.
	$in_tag = false;
	$had_tag = false;
	$codeopen = 0;
	if (preg_match_all('~(\[(/)*code(?:=[^\]]+)?\])~is', $message, $matches))
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
		$message .= '[/code]';
	}
	// Open any ones that need to be open, only if we've never had a tag.
	if ($codeopen && !$had_tag)
	{
		$message = '[code]' . $message;
	}
	// Now that we've fixed all the code tags, let's fix the img and url tags...
	$parts = preg_split('~(\[/code\]|\[code(?:=[^\]]+)?\])~i', $message, -1, PREG_SPLIT_DELIM_CAPTURE);
	// The regular expression non breaking space has many versions.
	$non_breaking_space = $context['utf8'] ? '\x{A0}' : '\xA0';
	// Only mess with stuff outside [code] tags.
	for ($i = 0, $n = count($parts); $i < $n; $i++)
	{
		// It goes 0 = outside, 1 = begin tag, 2 = inside, 3 = close tag, repeat.
		if ($i % 4 == 0)
		{
			fixTags($parts[$i]);
			// Replace /me.+?\n with [me=name]dsf[/me]\n.
			if (strpos($user_info['name'], '[') !== false || strpos($user_info['name'], ']') !== false || strpos($user_info['name'], '\'') !== false || strpos($user_info['name'], '"') !== false)
			{
				$parts[$i] = preg_replace('~(\A|\n)/me(?: |&nbsp;)([^\n]*)(?:\z)?~i', '$1[me=&quot;' . $user_info['name'] . '&quot;]$2[/me]', $parts[$i]);
			}
			else
			{
				$parts[$i] = preg_replace('~(\A|\n)/me(?: |&nbsp;)([^\n]*)(?:\z)?~i', '$1[me=' . $user_info['name'] . ']$2[/me]', $parts[$i]);
			}
			if (!$previewing && strpos($parts[$i], '[html]') !== false)
			{
				if (allowedTo('admin_forum'))
				{
					$parts[$i] = preg_replace('~\[html\](.+?)\[/html\]~ise', '\'[html]\' . strtr(un_htmlspecialchars(\'$1\'), array("\n" => \'&#13;\', \'  \' => \' &#32;\', \'[\' => \'&#91;\', \']\' => \'&#93;\')) . \'[/html]\'', $parts[$i]);
				}
				// We should edit them out, or else if an admin edits the message they will get shown...
				else
				{
					while (strpos($parts[$i], '[html]') !== false)
					{
						$parts[$i] = preg_replace('~\[[/]?html\]~i', '', $parts[$i]);
					}
				}
			}
			// Let's look at the time tags...
			$parts[$i] = preg_replace_callback('~\[time(?:=(absolute))*\](.+?)\[/time\]~i', function ($m) use ($modSettings, $user_info)
			{
				return "[time]" . (is_numeric("$m[2]") || @strtotime("$m[2]") == 0 ? "$m[2]" : strtotime("$m[2]") - ("$m[1]" == "absolute" ? 0 : (($modSettings["time_offset"] + $user_info["time_offset"]) * 3600))) . "[/time]";
			}, $parts[$i]);
			// Change the color specific tags to [color=the color].
			$parts[$i] = preg_replace('~\[(black|blue|green|red|white)\]~', '[color=$1]', $parts[$i]);  // First do the opening tags.
			$parts[$i] = preg_replace('~\[/(black|blue|green|red|white)\]~', '[/color]', $parts[$i]);   // And now do the closing tags
			// Make sure all tags are lowercase.
			$parts[$i] = preg_replace_callback('~\[([/]?)(list|li|table|tr|td)((\s[^\]]+)*)\]~i', function ($m)
			{
				return "[$m[1]" . strtolower("$m[2]") . "$m[3]]";
			}, $parts[$i]);
			$list_open = substr_count($parts[$i], '[list]') + substr_count($parts[$i], '[list ');
			$list_close = substr_count($parts[$i], '[/list]');
			if ($list_close - $list_open > 0)
			{
				$parts[$i] = str_repeat('[list]', $list_close - $list_open) . $parts[$i];
			}
			if ($list_open - $list_close > 0)
			{
				$parts[$i] = $parts[$i] . str_repeat('[/list]', $list_open - $list_close);
			}
			$mistake_fixes = array(
				// Find [table]s not followed by [tr].
				'~\[table\](?![\s' . $non_breaking_space . ']*\[tr\])~s' . ($context['utf8'] ? 'u' : '') => '[table][tr]',
				// Find [tr]s not followed by [td].
				'~\[tr\](?![\s' . $non_breaking_space . ']*\[td\])~s' . ($context['utf8'] ? 'u' : '') => '[tr][td]',
				// Find [/td]s not followed by something valid.
				'~\[/td\](?![\s' . $non_breaking_space . ']*(?:\[td\]|\[/tr\]|\[/table\]))~s' . ($context['utf8'] ? 'u' : '') => '[/td][/tr]',
				// Find [/tr]s not followed by something valid.
				'~\[/tr\](?![\s' . $non_breaking_space . ']*(?:\[tr\]|\[/table\]))~s' . ($context['utf8'] ? 'u' : '') => '[/tr][/table]',
				// Find [/td]s incorrectly followed by [/table].
				'~\[/td\][\s' . $non_breaking_space . ']*\[/table\]~s' . ($context['utf8'] ? 'u' : '') => '[/td][/tr][/table]',
				// Find [table]s, [tr]s, and [/td]s (possibly correctly) followed by [td].
				'~\[(table|tr|/td)\]([\s' . $non_breaking_space . ']*)\[td\]~s' . ($context['utf8'] ? 'u' : '') => '[$1]$2[_td_]',
				// Now, any [td]s left should have a [tr] before them.
				'~\[td\]~s' => '[tr][td]',
				// Look for [tr]s which are correctly placed.
				'~\[(table|/tr)\]([\s' . $non_breaking_space . ']*)\[tr\]~s' . ($context['utf8'] ? 'u' : '') => '[$1]$2[_tr_]',
				// Any remaining [tr]s should have a [table] before them.
				'~\[tr\]~s' => '[table][tr]',
				// Look for [/td]s followed by [/tr].
				'~\[/td\]([\s' . $non_breaking_space . ']*)\[/tr\]~s' . ($context['utf8'] ? 'u' : '') => '[/td]$1[_/tr_]',
				// Any remaining [/tr]s should have a [/td].
				'~\[/tr\]~s' => '[/td][/tr]',
				// Look for properly opened [li]s which aren't closed.
				'~\[li\]([^\[\]]+?)\[li\]~s' => '[li]$1[_/li_][_li_]',
				'~\[li\]([^\[\]]+?)\[/list\]~s' => '[_li_]$1[_/li_][/list]',
				'~\[li\]([^\[\]]+?)$~s' => '[li]$1[/li]',
				// Lists - find correctly closed items/lists.
				'~\[/li\]([\s' . $non_breaking_space . ']*)\[/list\]~s' . ($context['utf8'] ? 'u' : '') => '[_/li_]$1[/list]',
				// Find list items closed and then opened.
				'~\[/li\]([\s' . $non_breaking_space . ']*)\[li\]~s' . ($context['utf8'] ? 'u' : '') => '[_/li_]$1[_li_]',
				// Now, find any [list]s or [/li]s followed by [li].
				'~\[(list(?: [^\]]*?)?|/li)\]([\s' . $non_breaking_space . ']*)\[li\]~s' . ($context['utf8'] ? 'u' : '') => '[$1]$2[_li_]',
				// Allow for sub lists.
				'~\[/li\]([\s' . $non_breaking_space . ']*)\[list\]~' . ($context['utf8'] ? 'u' : '') => '[_/li_]$1[list]',
				'~\[/list\]([\s' . $non_breaking_space . ']*)\[li\]~' . ($context['utf8'] ? 'u' : '') => '[/list]$1[_li_]',
				// Any remaining [li]s weren't inside a [list].
				'~\[li\]~' => '[list][li]',
				// Any remaining [/li]s weren't before a [/list].
				'~\[/li\]~' => '[/li][/list]',
				// Put the correct ones back how we found them.
				'~\[_(li|/li|td|tr|/tr)_\]~' => '[$1]',
				// Images with no real url.
				'~\[img\]https?://.{0,7}\[/img\]~' => '',
			);
			// Fix up some use of tables without [tr]s, etc. (it has to be done more than once to catch it all.)
			for ($j = 0; $j < 3; $j++)
			{
				$parts[$i] = preg_replace(array_keys($mistake_fixes), $mistake_fixes, $parts[$i]);
			}
			// Remove empty bbc from the sections outside the code tags
			$parts[$i] = preg_replace('~\[[bisu]\]\s*\[/[bisu]\]~', '', $parts[$i]);
			$parts[$i] = preg_replace('~\[quote\]\s*\[/quote\]~', '', $parts[$i]);
			$parts[$i] = preg_replace('~\[color=(?:#[\da-fA-F]{3}|#[\da-fA-F]{6}|[A-Za-z]{1,20}|rgb\(\d{1,3}, ?\d{1,3}, ?\d{1,3}\))\]\s*\[/color\]~', '', $parts[$i]);
		}
	}
	// Put it back together!
	if (!$previewing)
	{
		$message = strtr(implode('', $parts), array('  ' => '&nbsp; ', "\n" => '<br>', $context['utf8'] ? "\xC2\xA0" : "\xA0" => '&nbsp;'));
	}
	else
	{
		$message = strtr(implode('', $parts), array('  ' => '&nbsp; ', $context['utf8'] ? "\xC2\xA0" : "\xA0" => '&nbsp;'));
	}
	// Now let's quickly clean up things that will slow our parser (which are common in posted code.)
	$message = strtr($message, array('[]' => '&#91;]', '[&#039;' => '&#91;&#039;'));
}