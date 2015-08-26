<?php

namespace BBC\Tests\Parser;

use \BBC\Codes;

class Test implements \BBC\Tests\BBCTest
{
	public function __construct()
	{
		require_once __DIR__ . '/Parser.php';
		require_once __DIR__ . '/Codes.php';
	}

	public function getName()
	{
		return 'Parser';
	}

	public function setup()
	{
		$this->parser = new \BBC\Parser(new Codes);
	}

	public function parseMessage($message)
	{
		return $this->parser->parse($message);
	}

	public function codes()
	{
		return new Codes;
	}
}