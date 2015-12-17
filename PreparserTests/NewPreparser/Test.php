<?php

namespace BBC\PreparserTests\Preparser;

class Test implements \BBC\Tests\BBCTest
{
    protected $disabled = array();

    public function __construct()
    {
        require_once __DIR__ . '/Preparser.php';
    }

    public function getName()
    {
        return 'Preparser';
    }

    public function setup()
    {
        $this->parser = new \PreParser;
    }

    public function beforeMessage()
    {
        $GLOBALS['parse_tag_cache'] = null;
    }

    public function parseMessage($message)
    {
        $this->parser->parse($message);
        return $message;
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