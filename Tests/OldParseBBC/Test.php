<?php

namespace BBC\Tests\OldParseBBC;

class Test implements \BBC\Tests\BBCTest
{
	protected $disabled = array();

	public function __construct()
	{
		require_once __DIR__ . '/ParseBBC.php';
	}

	public function getName()
	{
		return 'Old parse_bbc';
	}

	public function setup()
	{
		global $bbc_codes, $itemcodes, $no_autolink_tags;
		global $disabled, $default_disabled, $parse_tag_cache;

		$bbc_codes = array();
		$itemcodes = array();
		$no_autolink_tags = array();
		$disabled = array_flip($this->disabled);
		$default_disabled = null;
		$parse_tag_cache = null;
	}

	public function beforeMessage()
	{
		$GLOBALS['parse_tag_cache'] = null;
	}

	public function parseMessage($message)
	{
		return parse_bbc($message);
	}

	public function codes()
	{
		return parse_bbc(false);
	}

	public function setDisabled(array $disabled)
	{
		$this->disabled = $disabled;
	}
}