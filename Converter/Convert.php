<?php

use BBC\Codes;

// Takes your old BBC array and makes it work with the new format.
class Convert
{
    protected $attributes = array(
        'tag'                 => Codes::ATTR_TAG,
        'type'                => Codes::ATTR_TYPE,
        'parameters'          => Codes::ATTR_PARAM,
        'test'                => Codes::ATTR_TEST,
        'content'             => Codes::ATTR_CONTENT,
        'before'              => Codes::ATTR_BEFORE,
        'after'               => Codes::ATTR_AFTER,
        'disabled_content'    => Codes::ATTR_DISABLED_CONTENT,
        'disabled_before'     => Codes::ATTR_DISABLED_BEFORE,
        'disabled_after'      => Codes::ATTR_DISABLED_AFTER,
        'block_level'         => Codes::ATTR_BLOCK_LEVEL,
        'trim'                => Codes::ATTR_TRIM,
        'validate'            => Codes::ATTR_VALIDATE,
        'quoted'              => Codes::ATTR_QUOTED,
        'require_parents'     => Codes::ATTR_REQUIRE_PARENTS,
        'require_children'    => Codes::ATTR_REQUIRE_CHILDREN,
        'disallow_parents'    => Codes::ATTR_DISALLOW_PARENTS,
        'disallow_children'   => Codes::ATTR_DISALLOW_CHILDREN,
        'disallow_before'     => Codes::ATTR_DISALLOW_BEFORE,
        'disallow_after'      => Codes::ATTR_DISALLOW_AFTER,
        'parsed_tags_allowed' => Codes::ATTR_PARSED_TAGS_ALLOWED,
    );


    protected $types = array(
        ''                        => Codes::TYPE_PARSED_CONTENT,
        'unparsed_equals'         => Codes::TYPE_UNPARSED_EQUALS,
        'parsed_equals'           => Codes::TYPE_PARSED_EQUALS,
        'unparsed_content'        => Codes::TYPE_UNPARSED_CONTENT,
        'closed'                  => Codes::TYPE_CLOSED,
        'unparsed_commas'         => Codes::TYPE_UNPARSED_COMMAS,
        'unparsed_commas_content' => Codes::TYPE_UNPARSED_COMMAS_CONTENT,
        'unparsed_equals_content' => Codes::TYPE_UNPARSED_EQUALS_CONTENT,
    );


    protected $param_attributes = array(
        'match'    => Codes::PARAM_ATTR_MATCH,
        'quoted'   => Codes::PARAM_ATTR_QUOTED,
        'validate' => Codes::PARAM_ATTR_VALIDATE,
        'value'    => Codes::PARAM_ATTR_VALUE,
        'optional' => Codes::PARAM_ATTR_OPTIONAL,
    );

    protected $code = array();
    protected $issues = array();

    public function convert(array $code)
    {
        $this->setType($code);
        $this->setAttributes($code);
        $this->setLength($code);
        $this->setBlockLevel($code);
        $this->setAutolink($code);
    }

    protected function setType(array $code)
    {
        if (isset($code['type']) && isset($this->types[$code['type']]))
        {
            $this->code[Codes::ATTR_TYPE] = $this->types[$code['type']];
        }
        else
        {
            $this->code[Codes::ATTR_TYPE] = Codes::TYPE_PARSED_CONTENT;
        }
    }

    protected function setAttributes(array $code)
    {
        foreach ($code as $attr => $val)
        {
            if (isset($this->attributes[$attr]))
            {
                $this->code[$this->attributes[$attr]] = $val;
            }
            else
            {
                $this->addIssue('Attribute was not found: ' . $attr, false);
            }
        }
    }
}