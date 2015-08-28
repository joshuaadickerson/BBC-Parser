<?php


/**
 * Takes a message and parses it, returning the prepared message as a reference.
 *
 * - Cleans up links (javascript, etc.) and code/quote sections.
 * - Won't convert \n's and a few other things if previewing is true.
 *
 * @package Posts
 * @param string $message
 * @param boolean $previewing
 */
function preparsecode(&$message, $previewing = false)
{
    global $user_info;
    // This line makes all languages *theoretically* work even with the wrong charset ;).
    $message = preg_replace('~&amp;#(\d{4,5}|[2-9]\d{2,4}|1[2-9]\d);~', '&#$1;', $message);
    // Clean up after nobbc ;).
    $message = preg_replace_callback('~\[nobbc\](.+?)\[/nobbc\]~i', 'preparsecode_nobbc_callback', $message);
    // Remove \r's... they're evil!
    $message = strtr($message, array("\r" => ''));
    // You won't believe this - but too many periods upsets apache it seems!
    $message = preg_replace('~\.{100,}~', '...', $message);
    // Trim off trailing quotes - these often happen by accident.
    while (substr($message, -7) == '[quote]')
    {
        $message = trim(substr($message, 0, -7));
    }
    while (substr($message, 0, 8) == '[/quote]')
    {
        $message = trim(substr($message, 8));
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
    // The regular expression non breaking space.
    $non_breaking_space = '\x{A0}';
    // Only mess with stuff outside [code] tags.
    for ($i = 0, $n = count($parts); $i < $n; $i++)
    {
        // It goes 0 = outside, 1 = begin tag, 2 = inside, 3 = close tag, repeat.
        if ($i % 4 == 0)
        {
            fixTags($parts[$i]);
            // Replace /me.+?\n with [me=name]dsf[/me]\n.
            if (preg_match('~[\[\]\\"]~', $user_info['name']) !== false)
            {
                $parts[$i] = preg_replace('~(\A|\n)/me(?: |&nbsp;)([^\n]*)(?:\z)?~i', '$1[me=&quot;' . $user_info['name'] . '&quot;]$2[/me]', $parts[$i]);
                $parts[$i] = preg_replace('~(\[footnote\])/me(?: |&nbsp;)([^\n]*?)(\[\/footnote\])~i', '$1[me=&quot;' . $user_info['name'] . '&quot;]$2[/me]$3', $parts[$i]);
            }
            else
            {
                $parts[$i] = preg_replace('~(\A|\n)/me(?: |&nbsp;)([^\n]*)(?:\z)?~i', '$1[me=' . $user_info['name'] .  ']$2[/me]', $parts[$i]);
                $parts[$i] = preg_replace('~(\[footnote\])/me(?: |&nbsp;)([^\n]*?)(\[\/footnote\])~i', '$1[me=' . $user_info['name'] . ']$2[/me]$3', $parts[$i]);
            }

            // Make sure all tags are lowercase.
            $parts[$i] = preg_replace_callback('~\[([/]?)(list|li|table|tr|td|th)((\s[^\]]+)*)\]~i', 'preparsecode_lowertags_callback', $parts[$i]);
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
                '~\[table\](?![\s' . $non_breaking_space . ']*\[tr\])~su' => '[table][tr]',
                // Find [tr]s not followed by [td] or [th]
                '~\[tr\](?![\s' . $non_breaking_space . ']*\[t[dh]\])~su' => '[tr][td]',
                // Find [/td] and [/th]s not followed by something valid.
                '~\[/t([dh])\](?![\s' . $non_breaking_space . ']*(?:\[t[dh]\]|\[/tr\]|\[/table\]))~su' => '[/t$1][/tr]',
                // Find [/tr]s not followed by something valid.
                '~\[/tr\](?![\s' . $non_breaking_space . ']*(?:\[tr\]|\[/table\]))~su' => '[/tr][/table]',
                // Find [/td] [/th]s incorrectly followed by [/table].
                '~\[/t([dh])\][\s' . $non_breaking_space . ']*\[/table\]~su' => '[/t$1][/tr][/table]',
                // Find [table]s, [tr]s, and [/td]s (possibly correctly) followed by [td].
                '~\[(table|tr|/td)\]([\s' . $non_breaking_space . ']*)\[td\]~su' => '[$1]$2[_td_]',
                // Now, any [td]s left should have a [tr] before them.
                '~\[td\]~s' => '[tr][td]',
                // Look for [tr]s which are correctly placed.
                '~\[(table|/tr)\]([\s' . $non_breaking_space . ']*)\[tr\]~su' => '[$1]$2[_tr_]',
                // Any remaining [tr]s should have a [table] before them.
                '~\[tr\]~s' => '[table][tr]',
                // Look for [/td]s or [/th]s followed by [/tr].
                '~\[/t([dh])\]([\s' . $non_breaking_space . ']*)\[/tr\]~su' => '[/t$1]$2[_/tr_]',
                // Any remaining [/tr]s should have a [/td].
                '~\[/tr\]~s' => '[/td][/tr]',
                // Look for properly opened [li]s which aren't closed.
                '~\[li\]([^\[\]]+?)\[li\]~s' => '[li]$1[_/li_][_li_]',
                '~\[li\]([^\[\]]+?)\[/list\]~s' => '[_li_]$1[_/li_][/list]',
                '~\[li\]([^\[\]]+?)$~s' => '[li]$1[/li]',
                // Lists - find correctly closed items/lists.
                '~\[/li\]([\s' . $non_breaking_space . ']*)\[/list\]~su' => '[_/li_]$1[/list]',
                // Find list items closed and then opened.
                '~\[/li\]([\s' . $non_breaking_space . ']*)\[li\]~su' => '[_/li_]$1[_li_]',
                // Now, find any [list]s or [/li]s followed by [li].
                '~\[(list(?: [^\]]*?)?|/li)\]([\s' . $non_breaking_space . ']*)\[li\]~su' => '[$1]$2[_li_]',
                // Allow for sub lists.
                '~\[/li\]([\s' . $non_breaking_space . ']*)\[list\]~u' => '[_/li_]$1[list]',
                '~\[/list\]([\s' . $non_breaking_space . ']*)\[li\]~u' => '[/list]$1[_li_]',
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
            // Fix up some use of tables without [tr]s, etc. (it has to be done more than once to catch it all.)
            for ($j = 0; $j < 3; $j++)
            {
                $parts[$i] = preg_replace(array_keys($mistake_fixes), $mistake_fixes, $parts[$i]);
            }
            // Remove empty bbc from the sections outside the code tags
            $parts[$i] = preg_replace('~\[[bisu]\]\s*\[/[bisu]\]~', '', $parts[$i]);
            $parts[$i] = preg_replace('~\[quote\]\s*\[/quote\]~', '', $parts[$i]);
            // Fix color tags of many forms so they parse properly
            $parts[$i] = preg_replace('~\[color=(?:#[\da-fA-F]{3}|#[\da-fA-F]{6}|[A-Za-z]{1,20}|rgb\(\d{1,3}, ?\d{1,3}, ?\d{1,3}\))\]\s*\[/color\]~', '', $parts[$i]);
        }
        call_integration_hook('integrate_preparse_code', array(&$parts[$i], $i, $previewing));
    }
    // Put it back together!
    if (!$previewing)
    {
        $message = strtr(implode('', $parts), array('  ' => '&nbsp; ', "\n" => '<br />', "\xC2\xA0" => '&nbsp;'));
    }
    else
    {
        $message = strtr(implode('', $parts), array('  ' => '&nbsp; ', "\xC2\xA0" => '&nbsp;'));
    }
    // Now we're going to do full scale table checking...
    $message = preparsetable($message);
    // Now let's quickly clean up things that will slow our parser (which are common in posted code.)
    $message = strtr($message, array('[]' => '&#91;]', '[&#039;' => '&#91;&#039;'));
}

/**
 * Ensure tags inside of nobbc do not get parsed by converting the markers to html entities
 *
 * @package Posts
 * @param string[] $matches
 */
function preparsecode_nobbc_callback($matches)
{
    return '[nobbc]' . strtr($matches[1], array('[' => '&#91;', ']' => '&#93;', ':' => '&#58;', '@' => '&#64;')) . '[/nobbc]';
}

/**
 * Takes a tag and lowercases it
 *
 * @package Posts
 * @param string[] $matches
 */
function preparsecode_lowertags_callback($matches)
{
    return '[' . $matches[1] . strtolower($matches[2]) . $matches[3] . ']';
}

/**
 * This is very simple, and just removes things done by preparsecode.
 *
 * @package Posts
 * @param string $message
 */
function un_preparsecode($message)
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

/**
 * Fix any URLs posted - ie. remove 'javascript:'.
 *
 * - Used by preparsecode, fixes links in message and returns nothing.
 *
 * @package Posts
 * @param string $message
 */
function fixTags(&$message)
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

    call_integration_hook('integrate_fixtags', array(&$fixArray, &$message));

    // Fix each type of tag.
    foreach ($fixArray as $param)
    {
        fixTag($message, $param['tag'], $param['protocols'], $param['embeddedUrl'], $param['hasEqualSign'], !empty($param['hasExtra']));
    }

    // Now fix possible security problems with images loading links automatically...
    $message = preg_replace_callback('~(\[img.*?\])(.+?)\[/img\]~is', 'fixTags_img_callback', $message);

    // Limit the size of images posted?
    if (!empty($modSettings['max_image_width']) || !empty($modSettings['max_image_height']))
    {
        resizeBBCImages($message);
    }
}
/**
 * Ensure image tags do not load anything by themselfs (security)
 *
 * @package Posts
 * @param string[] $matches
 */
function fixTags_img_callback($matches)
{
    return $matches[1] . preg_replace('~action(=|%3d)(?!dlattach)~i', 'action-', $matches[2]) . '[/img]';
}
/**
 * Fix a specific class of tag - ie. url with =.
 *
 * - Used by fixTags, fixes a specific tag's links.
 *
 * @package Posts
 * @param string $message
 * @param string $myTag - the tag
 * @param string $protocols - http or ftp
 * @param bool $embeddedUrl = false - whether it *can* be set to something
 * @param bool $hasEqualSign = false, whether it *is* set to something
 * @param bool $hasExtra = false - whether it can have extra cruft after the begin tag.
 */
function fixTag(&$message, $myTag, $protocols, $embeddedUrl = false, $hasEqualSign = false, $hasExtra = false)
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
            if (substr($replace, 0, 1) === '/')
            {
                $replace = $domain_url . $replace;
            }
            elseif (substr($replace, 0, 1) == '?')
            {
                $replace = $scripturl . $replace;
            }
            elseif (substr($replace, 0, 1) == '#' && $embeddedUrl)
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
        elseif (!$found && $protocols[0] == 'ftp')
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
 *
 * @param string $message
 */
function preparsetable($message)
{
    $table_check = $message;
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
            $message = substr($message, 0, $table_offset + $offset) . substr($message, $table_offset + strlen($matches[0]) + $offset);
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
        $message .= '[/' . $tag . ']';
    }

    return $message;
}