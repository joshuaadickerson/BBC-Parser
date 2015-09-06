<?php

use BBC\Codes;

// Takes your old BBC array and makes it work with the new format.
class Export
{
    protected $attributes = array(
        Codes::ATTR_TAG                 => 'Codes::ATTR_TAG',
        Codes::ATTR_TYPE                => 'Codes::ATTR_TYPE',
        Codes::ATTR_PARAM               => 'Codes::ATTR_PARAM',
        Codes::ATTR_TEST                => 'Codes::ATTR_TEST',
        Codes::ATTR_CONTENT             => 'Codes::ATTR_CONTENT',
        Codes::ATTR_BEFORE              => 'Codes::ATTR_BEFORE',
        Codes::ATTR_AFTER               => 'Codes::ATTR_AFTER',
        Codes::ATTR_DISABLED_CONTENT    => 'Codes::ATTR_DISABLED_CONTENT',
        Codes::ATTR_DISABLED_BEFORE     => 'Codes::ATTR_DISABLED_BEFORE',
        Codes::ATTR_DISABLED_AFTER      => 'Codes::ATTR_DISABLED_AFTER',
        Codes::ATTR_BLOCK_LEVEL         => 'Codes::ATTR_BLOCK_LEVEL',
        Codes::ATTR_TRIM                => 'Codes::ATTR_TRIM',
        Codes::ATTR_VALIDATE            => 'Codes::ATTR_VALIDATE',
        Codes::ATTR_QUOTED              => 'Codes::ATTR_QUOTED',
        Codes::ATTR_REQUIRE_PARENTS     => 'Codes::ATTR_REQUIRE_PARENTS',
        Codes::ATTR_REQUIRE_CHILDREN    => 'Codes::ATTR_REQUIRE_CHILDREN',
        Codes::ATTR_DISALLOW_CHILDREN   => 'Codes::ATTR_DISALLOW_CHILDREN',
        Codes::ATTR_DISALLOW_PARENTS    => 'Codes::ATTR_DISALLOW_PARENTS',
        Codes::ATTR_DISALLOW_BEFORE     => 'Codes::ATTR_DISALLOW_BEFORE',
        Codes::ATTR_DISALLOW_AFTER      => 'Codes::ATTR_DISALLOW_AFTER',
        Codes::ATTR_PARSED_TAGS_ALLOWED => 'Codes::ATTR_PARSED_TAGS_ALLOWED',
        Codes::ATTR_AUTOLINK            => 'Codes::ATTR_AUTOLINK',
        Codes::ATTR_LENGTH              => 'Codes::ATTR_LENGTH',
        Codes::ATTR_DISABLED            => 'Codes::ATTR_DISABLED',
        Codes::ATTR_NO_CACHE            => 'Codes::ATTR_NO_CACHE',
    );


    protected $types = array(
        Codes::TYPE_PARSED_CONTENT          => 'Codes::TYPE_PARSED_CONTENT',
        Codes::TYPE_UNPARSED_EQUALS         => 'Codes::TYPE_UNPARSED_EQUALS',
        Codes::TYPE_PARSED_EQUALS           => 'Codes::TYPE_PARSED_EQUALS',
        Codes::TYPE_UNPARSED_CONTENT        => 'Codes::TYPE_UNPARSED_CONTENT',
        Codes::TYPE_CLOSED                  => 'Codes::TYPE_CLOSED',
        Codes::TYPE_UNPARSED_COMMAS         => 'Codes::TYPE_UNPARSED_COMMAS',
        Codes::TYPE_UNPARSED_COMMAS_CONTENT => 'Codes::TYPE_UNPARSED_COMMAS_CONTENT',
        Codes::TYPE_UNPARSED_EQUALS_CONTENT => 'Codes::TYPE_UNPARSED_EQUALS_CONTENT',
    );


    protected $param_attributes = array(

    );

    public function __construct()
    {

    }

    public function export($code)
    {
        $this->check($code);
        if ($this->getIssues() !== array())
        {
            throw new \Exception('Cannot export BBC with issues.');
        }

        $new_code = $this->convert($code);

        $export = '
	array(
		Codes::ATTR_TAG => '  . $new_code[Codes::ATTR_TAG] . ',
		Codes::ATTR_TYPE => ' . $new_code[Codes::ATTR_TYPE];

        foreach ($attributes as $k => $v)
        {
            // if param foreach params
        }

        $export .= '
	);';

        return $export;
    }

    public function addIssue($issue, $fatal)
    {
        $this->issues[] = array($issue, (bool) $fatal);
    }

    public function getIssues()
    {
        return $this->issues;
    }

    public function hasFatalIssues()
    {
        foreach ($this->issues as $issue)
        {
            if ($issue[1])
            {
                return true;
            }
        }

        return false;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getTypes()
    {
        return $this->types;
    }

    public function getParameterAttributes()
    {
        return $this->param_attributes;
    }
}