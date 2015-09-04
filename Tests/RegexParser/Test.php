<?php

namespace BBC\Tests\RegexParser;

use \BBC\Codes;

class Test implements \BBC\Tests\BBCTest
{
	protected $disabled;

	/** @var  \BBC\RegexParser */
	protected $parser;

	public function __construct()
	{
		require_once __DIR__ . '/RegexParser.php';
		require_once __DIR__ . '/RegexCodes.php';
	}

	public function getName()
	{
		return 'Regex Parser';
	}

	public function setup()
	{
		$this->parser = new \BBC\RegexParser(new \BBC\RegexCodes);
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