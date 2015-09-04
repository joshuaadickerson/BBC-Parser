<?php

namespace BBC\Tests\Parser;

use BBC\Autolink;
use \BBC\Codes;
use BBC\HtmlParser;

class Test implements \BBC\Tests\BBCTest
{
	protected $disabled = array();

	public function __construct()
	{
		require_once __DIR__ . '/Parser.php';
		require_once __DIR__ . '/Codes.php';
		require_once __DIR__ . '/SmileyParser.php';
		require_once __DIR__ . '/Autolink.php';
		require_once __DIR__ . '/HtmlParser.php';
	}

	public function getName()
	{
		return 'Parser';
	}

	public function setup()
	{
		$bbc = new Codes(array(), $this->disabled);
		$autolink = new Autolink($bbc);
		$html = new HtmlParser;

		$this->parser = new \BBC\Parser($bbc, $autolink, $html);
	}

	public function parseMessage($message)
	{
		return $this->parser->parse($message);
	}

	public function codes()
	{
		return new Codes;
	}

	public function setDisabled(array $disabled)
	{
		$this->disabled = $disabled;
	}
}