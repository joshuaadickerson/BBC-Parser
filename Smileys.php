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

class Smileys
{
	protected $smileys = array();
	protected $search;
	protected $replace;
	protected $path;


	public function __construct(array $smileys)
	{
		global $modSettings, $user_info;

		// This is the default path. You can change it later
		$this->path = htmlspecialchars($modSettings['smileys_url'] . '/' . $user_info['smiley_set'] . '/');
	}

	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}

	public function getReplace($smiley)
	{
		$code = strtr($specialChars, array(':' => '&#58;', '(' => '&#40;', ')' => '&#41;', '$' => '&#36;', '[' => '&#091;'));
		return '<img src="' . $this->path . $this->smileys[$smiley]['img'] . '" alt="' . $code. '" title="' . strtr(htmlspecialchars($smileysdescs[$i]), array(':' => '&#58;', '(' => '&#40;', ')' => '&#41;', '$' => '&#36;', '[' => '&#091;')) . '" class="smiley" />';
	}

}

// Parse smileys in the passed message.
function parse_smileys(&$message)
{
	global $modSettings, $txt, $user_info, $context, $smcFunc;
	static $smileyarray = array();

	// No smiley set at all?!
	if ($user_info['smiley_set'] == 'none' || trim($message) == '')
		return;

	// If the smiley array hasn't been set, do it now.
	if (empty($smileyarray))
	{
		// Small fix because entities are getting messed up
		$smileyarray = array(
			'&quot;' => '&quot;',
			'&apos;' => '&apos;', // &apos; shouldn't happen, but just in case
			'&#039;' => '&#039;',
			'&lt;' => '&lt;',
			'&gt;' => '&gt;',
			'&amp;' => '&amp;',
		);

		// Use the default smileys if it is disabled. (better for "portability" of smileys.)
		if (empty($modSettings['smiley_enable']))
		{
			$smileysfrom = array('>:D', ':D', '::)', '>:(', ':))', ':)', ';)', ';D', ':(', ':o', '8)', ':P', '???', ':-[', ':-X', ':-*', ':\'(', ':-\\', '^-^', 'O0', 'C:-)', '0:)');
			$smileysto = array('evil.gif', 'cheesy.gif', 'rolleyes.gif', 'angry.gif', 'laugh.gif', 'smiley.gif', 'wink.gif', 'grin.gif', 'sad.gif', 'shocked.gif', 'cool.gif', 'tongue.gif', 'huh.gif', 'embarrassed.gif', 'lipsrsealed.gif', 'kiss.gif', 'cry.gif', 'undecided.gif', 'azn.gif', 'afro.gif', 'police.gif', 'angel.gif');
			$smileysdescs = array('', $txt['icon_cheesy'], $txt['icon_rolleyes'], $txt['icon_angry'], '', $txt['icon_smiley'], $txt['icon_wink'], $txt['icon_grin'], $txt['icon_sad'], $txt['icon_shocked'], $txt['icon_cool'], $txt['icon_tongue'], $txt['icon_huh'], $txt['icon_embarrassed'], $txt['icon_lips'], $txt['icon_kiss'], $txt['icon_cry'], $txt['icon_undecided'], '', '', '', '');
		}
		else
		{
			// Load the smileys in reverse order by length so they don't get parsed wrong.
			if (($temp = cache_get_data('parsing_smileys', 480)) == null)
			{
				$result = $smcFunc['db_query']('', '
					SELECT code, filename, description
					FROM {db_prefix}smileys',
					array(
					)
				);
				$smileysfrom = array();
				$smileysto = array();
				$smileysdescs = array();
				while ($row = $smcFunc['db_fetch_assoc']($result))
				{
					$smileysfrom[] = $row['code'];
					$smileysto[] = $row['filename'];
					$smileysdescs[] = $row['description'];
				}
				$smcFunc['db_free_result']($result);

				cache_put_data('parsing_smileys', array($smileysfrom, $smileysto, $smileysdescs), 480);
			}
			else
				list ($smileysfrom, $smileysto, $smileysdescs) = $temp;
		}

		foreach ($smileysfrom as $i => $from)
		{
			$smileyCode = '<img src="' . $modSettings['smileys_url'] . '/' . $user_info['smiley_set'] . '/' . $smileysto[$i] . '" alt="' . htmlentities($from) . '" title="' . htmlentities($smileysdescs[$i]) . '" class="smiley" />';

			$smileyarray[$from] = $smileyCode;
			if ($from != ($specialChars = htmlspecialchars($from, ENT_QUOTES)))
				$smileyarray[$specialChars] = $smileyCode;
		}
	}

	// Replace away!
	$message = strtr($message, $smileyarray);
}