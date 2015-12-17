<?php

namespace BBC;

class PreparseCodes
{
    /** The next tag after this one must be one of the tags from this array */
    const NEXT_TAG_MUST_BE = 5;
    /** This code only contains other tags, no content */
    const TAGS_ONLY_CONTENT = 6;
    /** Removes tags with no content */
    const REMOVE_EMPTY = 7;
    /** If true, run checks to validate the parameter */
    const PARAM_IS_URL = 8;
    /** If true, run checks to validate the parameter */
    const EQUALS_IS_URL = 9;
    /** If true, run checks to validate the content as a url */
    const CONTENT_IS_URL = 17;
    /** Whether to parse the content or not */
    const NO_PARSE = 10;
    const FILTER_CONTENT = 11;
    const FILTER_EQUALS = 12;
    const FILTER_PARAM = 13;
    const PARAMS = 18;
    const BLOCK_LEVEL = 14;
    /** Add these parent tags (in this order) if they are missing */
    const ADD_PARENT_IF_MISSING = 15;
    const REMOVE_EXTRA_CLOSING = 16;
    /** When reversing the preparsing, it will remove the tags (open/close), leaving only the content. Used for autolink */
    const UNPARSE_REMOVE_TAG = 19;
    /** When reversing the preparsing, it will remove the parameter */
    const UNPARSE_REMOVE_PARAM = 20;

    protected $codes = array();

    public function __construct(array $codes = array())
    {
        if (empty($codes))
        {
            $this->codes = $this->getDefault();
        }
    }

    public function getCodes()
    {
        $codes = array();

        foreach ($this->getCodes() as $chars)
        {
            foreach ($chars as $code)
            {
                $codes[] = $code;
            }
        }

        return $codes;
    }

    public function hasChar($char)
    {
        return isset($this->codes[$char]) || isset($this->item_codes[$char]);
    }

    public function getByChar($char)
    {
        if ($this->hasChar($char))
        {
            return $this->codes[$char];
        }

        return array();
    }

    protected function getDefault()
    {
        return array(
            'a' => array(
                array(
                    self::ATTR_TAG => 'auto_email',
                    self::FILTER_EQUALS => self::EQUALS_IS_URL,
                    self::NO_PARSE => true,
                    self::UNPARSE_REMOVE_TAG,
                    self::ATTR_LENGTH => 10,
                ),
                array(
                    self::ATTR_TAG => 'auto_url',
                    self::FILTER_EQUALS => self::EQUALS_IS_URL,
                    self::NO_PARSE => true,
                    self::UNPARSE_REMOVE_TAG,
                    self::ATTR_LENGTH => 8,
                ),
            ),
            'b' => array(
                array(
                    self::ATTR_TAG => 'b',
                    self::REMOVE_EMPTY => true,
                    self::ATTR_LENGTH => 1,
                ),
            ),
            'c' => array(
                array(
                    self::ATTR_TAG => 'code',
                    self::NO_PARSE => true,
                    self::BLOCK_LEVEL => true,
                    self::ATTR_LENGTH => 4,
                ),
                array(
                    self::ATTR_TAG => 'color',
                    self::FILTER_EQUALS => function(&$equals) {
                        $equals = preg_replace(
                            // @todo fix this regex to only get the part between the = and ]
                            '~(?:#[\da-fA-F]{3}|#[\da-fA-F]{6}|[A-Za-z]{1,20}|rgb\(\d{1,3}, ?\d{1,3}, ?\d{1,3}\))~',
                            //'~\[color=(?:#[\da-fA-F]{3}|#[\da-fA-F]{6}|[A-Za-z]{1,20}|rgb\(\d{1,3}, ?\d{1,3}, ?\d{1,3}\))\]\s*\[/color\]~',
                            '',
                            $equals
                        );
                    },
                    self::ATTR_LENGTH => 5,
                ),
            ),
            'l' => array(
                array(
                    self::ATTR_TAG => 'li',
                    self::ADD_PARENT_IF_MISSING => array('list'),
                    self::ATTR_LENGTH => 2,
                ),
                array(
                    self::ATTR_TAG => 'list',
                    self::BLOCK_LEVEL => true,
                    self::TAGS_ONLY_CONTENT => true,
                    self::NEXT_TAG_MUST_BE => array('li'),
                    self::ATTR_LENGTH => 4,
                ),
            ),
        );
    }

    public function getDummyTag($tag)
    {
        return array(
            self::ATTR_TAG => $tag,
        );
    }
}