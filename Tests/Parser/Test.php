<?php

namespace BBC\Tests\Parser;

use BBC\Autolink;
use \BBC\Codes;
use BBC\HtmlParser;

class Test implements \BBC\Tests\BBCTest
{
	protected $disabled = array();
	protected $parser;
	protected $smiley_parser;

	public function __construct()
	{
		require_once __DIR__ . '/Parser.php';
		require_once __DIR__ . '/Codes.php';
		require_once __DIR__ . '/DefaultCodes.php';
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
		$bbc = new \BBC\DefaultCodes(array(), $this->disabled);
		$autolink = new Autolink($bbc);
		$html = new HtmlParser;

		$this->parser = new \BBC\Parser($bbc, $autolink, $html);
		$this->parser->canParseHTML(true);

		$this->smiley_parser = new \BBC\SmileyParser;
	}

	public function parseMessage($message)
	{
		$message = $this->parser->parse($message);
		$this->smiley_parser->parse($message);

		return $message;
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