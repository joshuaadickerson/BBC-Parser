<?php

namespace BBC\PreparserTests\OldPreParser;

class Test implements \BBC\Tests\BBCTest
{
    protected $disabled = array();

    public function __construct()
    {
        require_once __DIR__ . '/OldPreParser.php';
    }

    public function getName()
    {
        return 'Old preparser';
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
        preparsecode($message);
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