<?php

namespace BBC;

class SmileyParser
{
	protected $has_smileys = true;
	protected $smileys;
	protected $search;
	protected $replace;

	public function __construct(array $smileys = null)
	{
		$this->has_smileys = $GLOBALS['user_info']['smiley_set'] !== 'none';

		if ($this->has_smileys)
		{
			$this->smileys = $smileys === null ? $this->getDefault() : $smileys;
		}
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
	function parse(&$message)
	{
		// No smiley set at all?!
		if (!$this->has_smileys && trim($message) == '')
		{
			return;
		}

		$replace = $this->replace;
		// Replace away!
		$message = preg_replace_callback($this->search, function ($matches) use ($replace) {
			return $replace[$matches[0]];
		}, $message);
	}

	protected function getDefault()
	{
		global $modSettings, $txt;

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
			{
				list ($smileysfrom, $smileysto, $smileysdescs) = $temp;
			}
		}

		// The non-breaking-space is a complex thing...
		$non_breaking_space = '\x{A0}';

		// This smiley regex makes sure it doesn't parse smileys within code tags (so [url=mailto:David@bla.com] doesn't parse the :D smiley)
		//$smileyPregReplacements = array();
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

		$this->search = '~(?<=[>:\?\.\s' . $non_breaking_space . '[\]()*\\\;]|^)(' . implode('|', $searchParts) . ')(?=[^[:alpha:]0-9]|$)~';
		//$smileyPregSearch = '~\n(?<=[>:\?\.\s' . $non_breaking_space . '[\]()*\\\;]|^)(' . implode('|', $searchParts) . ')(?=[^[:alpha:]0-9]|$)\n~';
	}
}