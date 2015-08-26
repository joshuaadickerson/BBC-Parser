<?php

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
		'<img src="' . $this->path . $this->smileys[$smiley]['img'] . '" alt="' . $code. '" title="' . strtr(htmlspecialchars($smileysdescs[$i]), array(':' => '&#58;', '(' => '&#40;', ')' => '&#41;', '$' => '&#36;', '[' => '&#091;')) . '" class="smiley" />';
	}
}