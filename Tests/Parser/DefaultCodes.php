<?php

/**
 *
 * @name      ElkArte Forum
 * @copyright ElkArte Forum contributors
 * @license   BSD http://opensource.org/licenses/BSD-3-Clause
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:		BSD, See included LICENSE.TXT for terms and conditions.
 *
 *
 */

namespace BBC;

// @todo add attribute for TEST_PARAM_STRING and TEST_CONTENT so people can test the content
// @todo change ATTR_TEST to be able to test the entire message with the current offset

class DefaultCodes extends Codes
{
    public function __construct(array $codes, array $disabled)
    {
        $this->additional_bbc = $codes;

        foreach ($disabled as $tag)
        {
            $this->disable($tag);
        }

        $this->setDefault();

        foreach ($codes as $tag)
        {
            $this->add($tag);
        }
    }

    public function setDefault()
    {
        $this->bbc = array_merge(
            $this->abbr(),
            $this->anchor(),
            $this->b(),
            $this->br(),
            $this->center(),
            $this->code(),
            $this->color(),
            $this->email(),
            $this->font(),
            $this->footnote(),
            $this->font(),
            $this->hr(),
            $this->i(),
            $this->img(),
            $this->iurl(),
            $this->left(),
            $this->li(),
            $this->list_tag(),
            $this->me(),
            $this->member(),
            $this->nobbc(),
            $this->pre(),
            $this->quote(),
            $this->right(),
            $this->s(),
            $this->size(),
            $this->spoiler(),
            $this->sub(),
            $this->sup(),
            $this->table(),
            $this->td(),
            $this->th(),
            $this->tr(),
            $this->tt(),
            $this->u(),
            $this->url()
        );
    }

    public function abbr()
    {
        return array(
            array(
                self::ATTR_TAG => 'abbr',
                self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
                self::ATTR_BEFORE => '<abbr title="$1">',
                self::ATTR_AFTER => '</abbr>',
                self::ATTR_QUOTED => self::OPTIONAL,
                self::ATTR_DISABLED_AFTER => ' ($1)',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 4,
            ),
        );
    }

    public function anchor()
    {
        return array(
            array(
                self::ATTR_TAG => 'anchor',
                self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
                self::ATTR_TEST => '[#]?([A-Za-z][A-Za-z0-9_\-]*)',
                self::ATTR_BEFORE => '<span id="post_$1">',
                self::ATTR_AFTER => '</span>',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 6,
            ),
        );
    }

    public function b()
    {
        return array(
            array(
                self::ATTR_TAG => 'b',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<strong class="bbc_strong">',
                self::ATTR_AFTER => '</strong>',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 1,
            ),
        );
    }

    public function br()
    {
        return array(
            array(
                self::ATTR_TAG => 'br',
                self::ATTR_TYPE => self::TYPE_CLOSED,
                self::ATTR_CONTENT => '<br />',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => false,
                self::ATTR_LENGTH => 2,
            ),
        );
    }

    public function center()
    {
        return array(
            array(
                self::ATTR_TAG => 'center',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<div class="centertext">',
                self::ATTR_AFTER => '</div>',
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 6,
            ),
        );
    }

    /**
     * @todo the validate attribute will only check if it is disabled once - at the beginning of usage. It should check each time.
     * @return array
     */
    public function code()
    {
        global $txt;

        return array(
            array(
                self::ATTR_TAG => 'code',
                self::ATTR_TYPE => self::TYPE_UNPARSED_CONTENT,
                self::ATTR_CONTENT => '<div class="codeheader">' . $txt['code'] . ': <a href="javascript:void(0);" onclick="return elkSelectText(this);" class="codeoperation">' . $txt['code_select'] . '</a></div><pre class="bbc_code prettyprint">$1</pre>',
                self::ATTR_VALIDATE => $this->isDisabled('code') ? null : array($this, 'tabToHtmlTab'),
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_AUTOLINK => false,
                self::ATTR_LENGTH => 4,
            ),
            array(
                self::ATTR_TAG => 'code',
                self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS_CONTENT,
                self::ATTR_CONTENT => '<div class="codeheader">' . $txt['code'] . ': ($2) <a href="#" onclick="return elkSelectText(this);" class="codeoperation">' . $txt['code_select'] . '</a></div><pre class="bbc_code prettyprint">$1</pre>',
                self::ATTR_VALIDATE => $this->isDisabled('code') ? null : array($this, 'tabToHtmlTab'),
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_AUTOLINK => false,
                self::ATTR_LENGTH => 4,
            ),
        );
    }

    public function color()
    {
        return array(
            array(
                self::ATTR_TAG => 'color',
                self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
                self::ATTR_TEST => '(#[\da-fA-F]{3}|#[\da-fA-F]{6}|[A-Za-z]{1,20}|rgb\((?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\s?,\s?){2}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\))',
                self::ATTR_BEFORE => '<span style="color: $1;" class="bbc_color">',
                self::ATTR_AFTER => '</span>',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 5,
            ),
        );
    }

    public function email()
    {
        return array(
            array(
                self::ATTR_TAG => 'email',
                self::ATTR_TYPE => self::TYPE_UNPARSED_CONTENT,
                self::ATTR_CONTENT => '<a href="mailto:$1" class="bbc_email">$1</a>',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => false,
                self::ATTR_LENGTH => 5,
            ),
            array(
                self::ATTR_TAG => 'email',
                self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
                self::ATTR_BEFORE => '<a href="mailto:$1" class="bbc_email">',
                self::ATTR_AFTER => '</a>',
                self::ATTR_DISALLOW_CHILDREN => array(
                    'email' => 1,
                    'url'   => 1,
                    'iurl'  => 1,
                ),
                self::ATTR_DISABLED_AFTER => ' ($1)',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => false,
                self::ATTR_LENGTH => 5,
            ),
        );
    }

    public function footnote()
    {
        return array(
            array(
                self::ATTR_TAG => 'footnote',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<sup class="bbc_footnotes">%fn%',
                self::ATTR_AFTER => '%fn%</sup>',
                self::ATTR_DISALLOW_PARENTS => array(
                    'footnote' => 1,
                    'code'     => 1,
                    'anchor'   => 1,
                    'url'      => 1,
                    'iurl'     => 1,
                ),
                self::ATTR_DISALLOW_BEFORE => '',
                self::ATTR_DISALLOW_AFTER => '',
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 8,
            ),
            // This won't get run, it's just for testing.
            array(
                self::ATTR_TAG => 'footnote',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<sup class="bbc_footnotes">%fn%',
                self::ATTR_AFTER => '%fn%</sup>',
                self::ATTR_DISALLOW_PARENTS => array(
                    'footnote' => 1,
                    'code'     => 1,
                    'anchor'   => 1,
                    'url'      => 1,
                    'iurl'     => 1,
                ),
                self::ATTR_DISALLOW_BEFORE => '',
                self::ATTR_DISALLOW_AFTER => '',
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 8,
            ),
        );
    }

    public function font()
    {
        return array(
            array(
                self::ATTR_TAG => 'font',
                self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
                self::ATTR_TEST => '[A-Za-z0-9_,\-\s]+?',
                self::ATTR_BEFORE => '<span style="font-family: $1;" class="bbc_font">',
                self::ATTR_AFTER => '</span>',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 4,
            ),
        );
    }

    public function hr()
    {
        return array(
            array(
                self::ATTR_TAG => 'hr',
                self::ATTR_TYPE => self::TYPE_CLOSED,
                self::ATTR_CONTENT => '<hr />',
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_AUTOLINK => false,
                self::ATTR_LENGTH => 2,
            ),
        );
    }

    public function i()
    {
        return array(
            array(
                self::ATTR_TAG => 'i',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<em>',
                self::ATTR_AFTER => '</em>',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 1,
            )
        );
    }

    public function img()
    {
        return array(
            array(
                self::ATTR_TAG => 'img',
                self::ATTR_TYPE => self::TYPE_UNPARSED_CONTENT,
                self::ATTR_PARAM => array(
                    'alt' => array(
                        self::PARAM_ATTR_OPTIONAL => true,
                    ),
                    'width' => array(
                        self::PARAM_ATTR_OPTIONAL => true,
                        self::PARAM_ATTR_VALUE => 'width:100%;max-width:$1px;',
                        self::PARAM_ATTR_MATCH => '(\d+)',
                    ),
                    'height' => array(
                        self::PARAM_ATTR_OPTIONAL => true,
                        self::PARAM_ATTR_VALUE => 'max-height:$1px;',
                        self::PARAM_ATTR_MATCH => '(\d+)',
                    ),
                ),
                self::ATTR_CONTENT => '<img src="$1" alt="{alt}" style="{width}{height}" class="bbc_img resized" />',
                self::ATTR_VALIDATE => array($this, 'addProtocol'),
                self::ATTR_DISABLED_CONTENT => '($1)',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => false,
                self::ATTR_LENGTH => 3,
            ),
            array(
                self::ATTR_TAG => 'img',
                self::ATTR_TYPE => self::TYPE_UNPARSED_CONTENT,
                self::ATTR_CONTENT => '<img src="$1" alt="" class="bbc_img" />',
                self::ATTR_VALIDATE => array($this, 'addProtocol'),
                self::ATTR_DISABLED_CONTENT => '($1)',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => false,
                self::ATTR_LENGTH => 3,
            ),
        );
    }

    public function iurl()
    {
        return array(
            array(
                self::ATTR_TAG => 'iurl',
                self::ATTR_TYPE => self::TYPE_UNPARSED_CONTENT,
                self::ATTR_CONTENT => '<a href="$1" class="bbc_link">$1</a>',
                self::ATTR_VALIDATE => array($this, 'addProtocol'),
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => false,
                self::ATTR_LENGTH => 4,
            ),
            array(
                self::ATTR_TAG => 'iurl',
                self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
                self::ATTR_BEFORE => '<a href="$1" class="bbc_link">',
                self::ATTR_AFTER => '</a>',
                self::ATTR_VALIDATE => function(&$tag, &$data, $disabled) {
                    if ($data[0] === '#')
                    {
                        $data = '#post_' . substr($data, 1);
                    }
                    elseif (strpos($data, 'http://') !== 0 && strpos($data, 'https://') !== 0)
                    {
                        $data = 'http://' . $data;
                    }
                },
                self::ATTR_DISALLOW_CHILDREN => array(
                    'email' => 1,
                    'url'   => 1,
                    'iurl'  => 1,
                ),
                self::ATTR_DISABLED_AFTER => ' ($1)',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => false,
                self::ATTR_LENGTH => 4,
            ),
        );
    }

    public function left()
    {
        return array(
            array(
                self::ATTR_TAG => 'left',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<div style="text-align: left;">',
                self::ATTR_AFTER => '</div>',
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 4,
            ),
        );
    }

    public function li()
    {
        return array(
            array(
                self::ATTR_TAG => 'li',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<li>',
                self::ATTR_AFTER => '</li>',
                self::ATTR_TRIM => self::TRIM_OUTSIDE,
                self::ATTR_REQUIRE_PARENTS => array(
                    'list' => 'list'
                ),
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_DISABLED_BEFORE => '',
                self::ATTR_DISABLED_AFTER => '<br />',
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 2,
            ),
        );
    }

    public function list_tag()
    {
        return array(
            array(
                self::ATTR_TAG => 'list',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<ul class="bbc_list">',
                self::ATTR_AFTER => '</ul>',
                self::ATTR_TRIM => self::TRIM_INSIDE,
                self::ATTR_REQUIRE_CHILDREN => array(
                    'li' => 1,
                    'list' => 1,
                ),
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 4,
            ),
            array(
                self::ATTR_TAG => 'list',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_PARAM => array(
                    'type' => array(
                        self::PARAM_ATTR_MATCH => '(none|disc|circle|square|decimal|decimal-leading-zero|lower-roman|upper-roman|lower-alpha|upper-alpha|lower-greek|lower-latin|upper-latin|hebrew|armenian|georgian|cjk-ideographic|hiragana|katakana|hiragana-iroha|katakana-iroha)',
                    ),
                ),
                self::ATTR_BEFORE => '<ul class="bbc_list" style="list-style-type: {type};">',
                self::ATTR_AFTER => '</ul>',
                self::ATTR_TRIM => self::TRIM_INSIDE,
                self::ATTR_REQUIRE_CHILDREN => array(
                    'li' => 1,
                ),
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 4,
            ),
        );
    }

    public function me()
    {
        return array(
            array(
                self::ATTR_TAG => 'me',
                self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
                self::ATTR_BEFORE => '<div class="meaction">&nbsp;$1 ',
                self::ATTR_AFTER => '</div>',
                self::ATTR_QUOTED => self::OPTIONAL,
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_DISABLED_BEFORE => '/me ',
                self::ATTR_DISABLED_AFTER => '<br />',
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 2,
            ),
        );
    }

    public function member()
    {
        global $scripturl;

        return array(
            array(
                self::ATTR_TAG => 'member',
                self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
                self::ATTR_TEST => '[\d*]',
                self::ATTR_BEFORE => '<span class="bbc_mention"><a href="' . $scripturl . '?action=profile;u=$1">@',
                self::ATTR_AFTER => '</a></span>',
                self::ATTR_DISABLED_BEFORE => '@',
                self::ATTR_DISABLED_AFTER => '',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 6,
            ),
        );
    }

    public function nobbc()
    {
        return array(
            array(
                self::ATTR_TAG => 'nobbc',
                self::ATTR_TYPE => self::TYPE_UNPARSED_CONTENT,
                self::ATTR_CONTENT => '$1',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 5,
            ),
        );
    }

    public function pre()
    {
        return array(
            array(
                self::ATTR_TAG => 'pre',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<pre class="bbc_pre">',
                self::ATTR_AFTER => '</pre>',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 3,
            ),
        );
    }

    public function quote()
    {
        global $scripturl, $txt, $modSettings;
        return array(
            array(
                self::ATTR_TAG => 'quote',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<div class="quoteheader">' . $txt['quote'] . '</div><blockquote>',
                self::ATTR_AFTER => '</blockquote>',
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 5,
            ),
            array(
                self::ATTR_TAG => 'quote',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_PARAM => array(
                    'author' => array(
                        self::PARAM_ATTR_MATCH => '(.{1,192}?)',
                        self::PARAM_ATTR_QUOTED => self::OPTIONAL,
                    ),
                ),
                self::ATTR_BEFORE => '<div class="quoteheader">' . $txt['quote_from'] . ': {author}</div><blockquote>',
                self::ATTR_AFTER => '</blockquote>',
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 5,
            ),
            array(
                self::ATTR_TAG => 'quote',
                self::ATTR_TYPE => self::TYPE_PARSED_EQUALS,
                self::ATTR_BEFORE => '<div class="quoteheader">' . $txt['quote_from'] . ': $1</div><blockquote>',
                self::ATTR_AFTER => '</blockquote>',
                self::ATTR_QUOTED => self::OPTIONAL,
                self::ATTR_PARSED_TAGS_ALLOWED => array(
                    'url',
                    'iurl'
                ),
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 5,
            ),
            array(
                self::ATTR_TAG => 'quote',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_PARAM => array(
                    'author' => array(
                        self::PARAM_ATTR_MATCH => '([^<>]{1,192}?)',
                    ),
                    'link' => array(
                        self::PARAM_ATTR_MATCH => '(?:board=\d+;)?((?:topic|threadid)=[\dmsg#\./]{1,40}(?:;start=[\dmsg#\./]{1,40})?|msg=\d{1,40}|action=profile;u=\d+)',
                    ),
                    'date' => array(
                        self::PARAM_ATTR_MATCH => '(\d+)',
                        self::ATTR_VALIDATE => 'htmlTime',
                    ),
                ),
                self::ATTR_BEFORE => '<div class="quoteheader"><a href="' . $scripturl . '?{link}">' . $txt['quote_from'] . ': {author} ' . ($modSettings['todayMod'] == 3 ? ' - ' : $txt['search_on']) . ' {date}</a></div><blockquote>',
                self::ATTR_AFTER => '</blockquote>',
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 5,
            ),
            array(
                self::ATTR_TAG => 'quote',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_PARAM => array(
                    'author' => array(
                        self::PARAM_ATTR_MATCH => '(.{1,192}?)'
                    ),
                ),
                self::ATTR_BEFORE => '<div class="quoteheader">' . $txt['quote_from'] . ': {author}</div><blockquote>',
                self::ATTR_AFTER => '</blockquote>',
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 5,
            ),
        );
    }

    public function right()
    {
        return array(
            array(
                self::ATTR_TAG => 'right',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<div style="text-align: right;">',
                self::ATTR_AFTER => '</div>',
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 5,
            ),
        );
    }

    public function s()
    {
        return array(
            array(
                self::ATTR_TAG => 's',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<del>',
                self::ATTR_AFTER => '</del>',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 1,
            ),
        );
    }

    public function size()
    {
        return array(
            array(
                self::ATTR_TAG => 'size',
                self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
                self::ATTR_TEST => '[1-7]{1}$',
                self::ATTR_BEFORE => '<span style="font-size: $1;" class="bbc_size">',
                self::ATTR_AFTER => '</span>',
                self::ATTR_VALIDATE => function(&$tag, &$data, $disabled) {
                    $sizes = array(
                        1 => 0.7,
                        2 => 1.0,
                        3 => 1.35,
                        4 => 1.45,
                        5 => 2.0,
                        6 => 2.65,
                        7 => 3.95,
                    );

                    $data = $sizes[(int) $data] . 'em';
                },
                self::ATTR_DISALLOW_PARENTS => array(
                    'size' => 1,
                ),
                self::ATTR_DISALLOW_BEFORE => '<span>',
                self::ATTR_DISALLOW_AFTER => '</span>',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 4,
            ),
            array(
                self::ATTR_TAG => 'size',
                self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
                self::ATTR_TEST => '([1-9][\d]?p[xt]|small(?:er)?|large[r]?|x[x]?-(?:small|large)|medium|(0\.[1-9]|[1-9](\.[\d][\d]?)?)?em)',
                self::ATTR_BEFORE => '<span style="font-size: $1;" class="bbc_size">',
                self::ATTR_AFTER => '</span>',
                self::ATTR_DISALLOW_PARENTS => array(
                    'size' => 1,
                ),
                self::ATTR_DISALLOW_BEFORE => '<span>',
                self::ATTR_DISALLOW_AFTER => '</span>',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 4,
            ),
        );
    }

    public function spoiler()
    {
        global $txt;

        return array(
            array(
                self::ATTR_TAG => 'spoiler',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<span class="spoilerheader">' . $txt['spoiler'] . '</span><div class="spoiler"><div class="bbc_spoiler" style="display: none;">',
                self::ATTR_AFTER => '</div></div>',
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 7,
            ),
        );
    }

    public function sub()
    {
        return array(
            array(
                self::ATTR_TAG => 'sub',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<sub>',
                self::ATTR_AFTER => '</sub>',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 3,
            ),
        );
    }

    public function sup()
    {
        return array(
            array(
                self::ATTR_TAG => 'sup',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<sup>',
                self::ATTR_AFTER => '</sup>',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 3,
            ),
        );
    }

    public function table()
    {
        return array(
            array(
                self::ATTR_TAG => 'table',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<div class="bbc_table_container"><table class="bbc_table">',
                self::ATTR_AFTER => '</table></div>',
                self::ATTR_TRIM => self::TRIM_INSIDE,
                self::ATTR_REQUIRE_CHILDREN => array(
                    'tr' => 1,
                ),
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 5,
            ),
        );
    }

    public function td()
    {
        return array(
            array(
                self::ATTR_TAG => 'td',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<td>',
                self::ATTR_AFTER => '</td>',
                self::ATTR_REQUIRE_PARENTS => array(
                    'tr' => 1,
                ),
                self::ATTR_TRIM => self::TRIM_OUTSIDE,
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_DISABLED_BEFORE => '',
                self::ATTR_DISABLED_AFTER => '',
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 2,
            ),
        );
    }

    public function th()
    {
        return array(
            array(
                self::ATTR_TAG => 'th',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<th>',
                self::ATTR_AFTER => '</th>',
                self::ATTR_REQUIRE_PARENTS => array(
                    'tr' => 1,
                ),
                self::ATTR_TRIM => self::TRIM_OUTSIDE,
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_DISABLED_BEFORE => '',
                self::ATTR_DISABLED_AFTER => '',
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 2,
            ),
        );
    }

    public function tr()
    {
        return array(
            array(
                self::ATTR_TAG => 'tr',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<tr>',
                self::ATTR_AFTER => '</tr>',
                self::ATTR_REQUIRE_PARENTS => array(
                    'table' => 'table'
                ),
                self::ATTR_REQUIRE_CHILDREN => array(
                    'td' => 'td',
                    'th' => 'th',
                ),
                self::ATTR_TRIM => self::TRIM_BOTH,
                self::ATTR_BLOCK_LEVEL => true,
                self::ATTR_DISABLED_BEFORE => '',
                self::ATTR_DISABLED_AFTER => '',
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 2,
            ),
        );
    }

    public function tt()
    {
        return array(
            array(
                self::ATTR_TAG => 'tt',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<span class="bbc_tt">',
                self::ATTR_AFTER => '</span>',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 2,
            ),
        );
    }

    public function u()
    {
        return array(
            array(
                self::ATTR_TAG => 'u',
                self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
                self::ATTR_BEFORE => '<span class="bbc_u">',
                self::ATTR_AFTER => '</span>',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => true,
                self::ATTR_LENGTH => 1,
            ),
        );
    }

    public function url()
    {
        return array(
            array(
                self::ATTR_TAG         => 'url',
                self::ATTR_TYPE        => self::TYPE_UNPARSED_CONTENT,
                self::ATTR_CONTENT     => '<a href="$1" class="bbc_link" target="_blank">$1</a>',
                self::ATTR_VALIDATE    => array($this, 'addProtocol'),
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK    => false,
                self::ATTR_LENGTH      => 3,
            ),
            array(
                self::ATTR_TAG => 'url',
                self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
                self::ATTR_BEFORE => '<a href="$1" class="bbc_link" target="_blank">',
                self::ATTR_AFTER => '</a>',
                self::ATTR_VALIDATE => array($this, 'addProtocol'),
                //self::ATTR_DISALLOW_CHILDREN => array('email', 'url', 'iurl'),
                self::ATTR_DISALLOW_CHILDREN => array(
                    'email' => 1,
                    'url'   => 1,
                    'iurl'  => 1,
                ),
                self::ATTR_DISABLED_AFTER => ' ($1)',
                self::ATTR_BLOCK_LEVEL => false,
                self::ATTR_AUTOLINK => false,
                self::ATTR_LENGTH => 3,
            ),
        );
    }
}