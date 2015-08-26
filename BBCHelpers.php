<?php

/**
 * Microsoft uses their own character set Code Page 1252 (CP1252), which is a
 * superset of ISO 8859-1, defining several characters between DEC 128 and 159
 * that are not normally displayable.  This converts the popular ones that
 * appear from a cut and paste from windows.
 *
 * @param string|false $string
 * @return string $string
 */
function sanitizeMSCutPaste($string)
{
	if (empty($string))
		return $string;

	// UTF-8 occurrences of MS special characters
	$findchars_utf8 = array(
		"\xe2\x80\x9a", // single low-9 quotation mark
		"\xe2\x80\x9e", // double low-9 quotation mark
		"\xe2\x80\xa6", // horizontal ellipsis
		"\xe2\x80\x98", // left single curly quote
		"\xe2\x80\x99", // right single curly quote
		"\xe2\x80\x9c", // left double curly quote
		"\xe2\x80\x9d", // right double curly quote
		"\xe2\x80\x93", // en dash
		"\xe2\x80\x94", // em dash
	);

	// safe replacements
	$replacechars = array(
		',',   // &sbquo;
		',,',  // &bdquo;
		'...', // &hellip;
		"'",   // &lsquo;
		"'",   // &rsquo;
		'"',   // &ldquo;
		'"',   // &rdquo;
		'-',   // &ndash;
		'--',  // &mdash;
	);

	$string = str_replace($findchars_utf8, $replacechars, $string);

	return $string;
}

/**
 * Parse smileys in the passed message.
 *
 * What it does:
 * - The smiley parsing function which makes pretty faces appear :).
 * - If custom smiley sets are turned off by smiley_enable, the default set of smileys will be used.
 * - These are specifically not parsed in code tags [url=mailto:Dad@blah.com]
 * - Caches the smileys from the database or array in memory.
 * - Doesn't return anything, but rather modifies message directly.
 *
 * @param string $message
 */
function parsesmileys(&$message)
{
	global $modSettings, $txt, $user_info;
	static $smileyPregSearch = null, $smileyPregReplacements = array();

	// No smiley set at all?!
	if ($user_info['smiley_set'] == 'none' || trim($message) == '')
		return;

	// If smileyPregSearch hasn't been set, do it now.
	if (empty($smileyPregSearch))
	{
		// Use the default smileys if it is disabled. (better for "portability" of smileys.)
		if (empty($modSettings['smiley_enable']))
		{
			$smileysfrom = array('>:D', ':D', '::)', '>:(', ':))', ':)', ';)', ';D', ':(', ':o', '8)', ':P', '???', ':-[', ':-X', ':-*', ':\'(', ':-\\', '^-^', 'O0', 'C:-)', 'O:)');
			$smileysto = array('evil.gif', 'cheesy.gif', 'rolleyes.gif', 'angry.gif', 'laugh.gif', 'smiley.gif', 'wink.gif', 'grin.gif', 'sad.gif', 'shocked.gif', 'cool.gif', 'tongue.gif', 'huh.gif', 'embarrassed.gif', 'lipsrsealed.gif', 'kiss.gif', 'cry.gif', 'undecided.gif', 'azn.gif', 'afro.gif', 'police.gif', 'angel.gif');
			$smileysdescs = array('', $txt['icon_cheesy'], $txt['icon_rolleyes'], $txt['icon_angry'], $txt['icon_laugh'], $txt['icon_smiley'], $txt['icon_wink'], $txt['icon_grin'], $txt['icon_sad'], $txt['icon_shocked'], $txt['icon_cool'], $txt['icon_tongue'], $txt['icon_huh'], $txt['icon_embarrassed'], $txt['icon_lips'], $txt['icon_kiss'], $txt['icon_cry'], $txt['icon_undecided'], '', '', '', $txt['icon_angel']);
		}
		else
		{
			// Load the smileys in reverse order by length so they don't get parsed wrong.
			if (($temp = cache_get_data('parsing_smileys', 480)) == null)
			{
				$smileysfrom = array();
				$smileysto = array();
				$smileysdescs = array();

				// @todo there is no reason $db should be used before this
				$db = database();

				$db->fetchQueryCallback('
					SELECT code, filename, description
					FROM {db_prefix}smileys
					ORDER BY LENGTH(code) DESC',
					array(
					),
					function($row) use (&$smileysfrom, &$smileysto, &$smileysdescs)
					{
						$smileysfrom[] = $row['code'];
						$smileysto[] = htmlspecialchars($row['filename']);
						$smileysdescs[] = $row['description'];
					}
				);

				cache_put_data('parsing_smileys', array($smileysfrom, $smileysto, $smileysdescs), 480);
			}
			else
				list ($smileysfrom, $smileysto, $smileysdescs) = $temp;
		}

		// The non-breaking-space is a complex thing...
		$non_breaking_space = '\x{A0}';

		// This smiley regex makes sure it doesn't parse smileys within code tags (so [url=mailto:David@bla.com] doesn't parse the :D smiley)
		$smileyPregReplacements = array();
		$searchParts = array();
		$smileys_path = htmlspecialchars($modSettings['smileys_url'] . '/' . $user_info['smiley_set'] . '/');

		for ($i = 0, $n = count($smileysfrom); $i < $n; $i++)
		{
			$specialChars = htmlspecialchars($smileysfrom[$i], ENT_QUOTES);
			$smileyCode = '<img src="' . $smileys_path . $smileysto[$i] . '" alt="' . strtr($specialChars, array(':' => '&#58;', '(' => '&#40;', ')' => '&#41;', '$' => '&#36;', '[' => '&#091;')). '" title="' . strtr(htmlspecialchars($smileysdescs[$i]), array(':' => '&#58;', '(' => '&#40;', ')' => '&#41;', '$' => '&#36;', '[' => '&#091;')) . '" class="smiley" />';

			$smileyPregReplacements[$smileysfrom[$i]] = $smileyCode;

			$searchParts[] = preg_quote($smileysfrom[$i], '~');
			if ($smileysfrom[$i] != $specialChars)
			{
				$smileyPregReplacements[$specialChars] = $smileyCode;
				$searchParts[] = preg_quote($specialChars, '~');
			}
		}

		$smileyPregSearch = '~(?<=[>:\?\.\s' . $non_breaking_space . '[\]()*\\\;]|^)(' . implode('|', $searchParts) . ')(?=[^[:alpha:]0-9]|$)~';
		//$smileyPregSearch = '~\n(?<=[>:\?\.\s' . $non_breaking_space . '[\]()*\\\;]|^)(' . implode('|', $searchParts) . ')(?=[^[:alpha:]0-9]|$)\n~';
	}

	// Replace away!
	$message = preg_replace_callback($smileyPregSearch, function ($matches) use ($smileyPregReplacements)
	{
		return $smileyPregReplacements[$matches[0]];
	}, $message);
}

/**
 * Calculates all the possible permutations (orders) of an array.
 *
 * What it does:
 * - should not be called on arrays bigger than 10 elements as this function is memory hungry
 * - returns an array containing each permutation.
 * - e.g. (1,2,3) returns (1,2,3), (1,3,2), (2,1,3), (2,3,1), (3,1,2), and (3,2,1)
 * - really a combinations without repetition N! function so 3! = 6 and 10! = 4098 combinations
 * - Used by parse_bbc to allow bbc tag parameters to be in any order and still be
 * parsed properly
 *
 * @param mixed[] $array index array of values
 * @return mixed[] array representing all permutations of the supplied array
 */
function permute($array)
{
	$orders = array($array);

	$n = count($array);
	$p = range(0, $n);
	for ($i = 1; $i < $n; null)
	{
		$p[$i]--;
		$j = $i % 2 != 0 ? $p[$i] : 0;

		$temp = $array[$i];
		$array[$i] = $array[$j];
		$array[$j] = $temp;

		for ($i = 1; $p[$i] == 0; $i++)
			$p[$i] = 1;

		$orders[] = $array;
	}

	return $orders;
}

function pc_next_permutation($p, $size)
{
	// If there is only 1, then there can only be 1 permutation... duh.
	if ($size < 1)
	{
		return false;
	}

	// slide down the array looking for where we're smaller than the next guy
	for ($i = $size - 1; isset($p[$i]) && $p[$i] >= $p[$i + 1]; --$i);

	// if this doesn't occur, we've finished our permutations
	// the array is reversed: (1, 2, 3, 4) => (4, 3, 2, 1)
	if ($i < 0)
	{
		return false;
	}

	// slide down the array looking for a bigger number than what we found before
	for ($j = $size; $p[$j] <= $p[$i]; --$j);

	// swap them
	$tmp = $p[$i];
	$p[$i] = $p[$j];
	$p[$j] = $tmp;

	// now reverse the elements in between by swapping the ends
	for (++$i, $j = $size; $i < $j; ++$i, --$j)
	{
		$tmp = $p[$i];
		$p[$i] = $p[$j];
		$p[$j] = $tmp;
	}

	return $p;
}


function footnote_callback($matches)
{
	global $fn_num, $fn_content, $fn_count;

	$fn_num++;
	$fn_content[] = '<div class="target" id="fn' . $fn_num . '_' . $fn_count . '"><sup>' . $fn_num . '&nbsp;</sup>' . $matches[2] . '<a class="footnote_return" href="#ref' . $fn_num . '_' . $fn_count . '">&crarr;</a></div>';

	return '<a class="target" href="#fn' . $fn_num . '_' . $fn_count . '" id="ref' . $fn_num . '_' . $fn_count . '">[' . $fn_num . ']</a>';
}

/**
 * Cut down version just so we can run test cases
 */
function htmlTime($timestamp)
{
	if (empty($timestamp))
		return '';

	$timestamp = forum_time(true, $timestamp);
	$time = date('Y-m-d H:i', $timestamp);
	$stdtime = standardTime($timestamp, true, true);

	// @todo maybe htmlspecialchars on the title attribute?
	return '<time title="' . $stdtime . '" datetime="' . $time . '" data-timestamp="' . $timestamp . '">' . $stdtime . '</time>';
}

/**
 * Cut down version just so we can run test cases
 */
function forum_time($use_user_offset = true, $timestamp = null)
{
	if ($timestamp === null)
		$timestamp = time();
	elseif ($timestamp == 0)
		return 0;

	return $timestamp;
}

/**
 * Cut down version just so we can run test cases
 */
function standardTime($log_time, $show_today = true, $offset_type = false)
{
	$time = $log_time;

	// Format any other characters..
	return strftime('%B %d, %Y, %I:%M:%S %p', $time);
}

// This is just a mock so we don't break anything
function call_integration_hook($hook, $parameters = array())
{
	return;
}

function cache_put_data($key, $value, $ttl = 120)
{
	return;
}

function cache_get_data($key, $ttl = 120)
{
	return;
}

// because shuffle doesn't have a shuffle_assoc()
function shuffle_assoc(&$array)
{
	$keys = array_keys($array);

	shuffle($keys);

	foreach($keys as $key)
	{
		$new[$key] = $array[$key];
	}

	$array = $new;

	return true;
}

function tabToHtmlTab($string)
{
	return str_replace("\t", "<span class=\"tab\">\t</span>", $string);
}

function removeBr($string)
{
	return str_replace('<br />', '', $string);
}

function addProtocol($string, $protocol = 'http')
{
	if (substr_compare($string, 'http://', 0) !== 0
		&& substr_compare($string, 'https://', 0) !== 0
		&& substr_compare($string, 'ftp://', 0) !== 0
		&& substr_compare($string, 'mailto://', 0) !== 0
		&& substr_compare($string, $protocol, 0) !== 0)
	{
		return $protocol . $string;
	}
}