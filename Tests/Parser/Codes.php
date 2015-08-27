<?php

namespace BBC;

// @todo add attribute for TEST_PARAM_STRING and TEST_CONTENT so people can test the content
// @todo change ATTR_TEST to be able to test the entire message with the current offset

class Codes
{
	/** the tag's name - must be lowercase */
	const ATTR_TAG = 1;
	/** One of self::TYPE_* */
	const ATTR_TYPE = 2;
	/**
	 * An optional array of parameters, for the form
	 * [tag abc=123]content[/tag].  The array is an associative array
	 * where the keys are the parameter names, and the values are an
	 * array which *may* contain any of self::PARAM_ATTR_*
	 */
	const ATTR_PARAM = 3;
	/**
	 * A regular expression to test immediately after the tag's
	 * '=', ' ' or ']'.  Typically, should have a \] at the end.
	 * Optional.
	 */
	const ATTR_TEST = 4;
	/**
	 * Only available for unparsed_content, closed, unparsed_commas_content, and unparsed_equals_content.
	 * $1 is replaced with the content of the tag.
	 * Parameters are replaced in the form {param}.
	 * For unparsed_commas_content, $2, $3, ..., $n are replaced.
	 */
	const ATTR_CONTENT = 5;
	/**
	 * Only when content is not used, to go before any content.
	 * For unparsed_equals, $1 is replaced with the value.
	 * For unparsed_commas, $1, $2, ..., $n are replaced.
	 */
	const ATTR_BEFORE = 6;
	/**
	 * Similar to before in every way, except that it is used when the tag is closed.
	 */
	const ATTR_AFTER = 7;
	/**
	 * Used in place of content when the tag is disabled.
	 * For closed, default is '', otherwise it is '$1' if block_level is false, '<div>$1</div>' elsewise.
	 */
	const ATTR_DISABLED_CONTENT = 8;
	/**
	 * Used in place of before when disabled.
	 * Defaults to '<div>' if block_level, '' if not.
	 */
	const ATTR_DISABLED_BEFORE = 9;
	/**
	 * Used in place of after when disabled.
	 * Defaults to '</div>' if block_level, '' if not.
	 */
	const ATTR_DISABLED_AFTER = 10;
	/**
	 * Set to true the tag is a "block level" tag, similar to HTML.
	 * Block level tags cannot be nested inside tags that are not block level, and will not be implicitly closed as easily.
	 * One break following a block level tag may also be removed.
	 */
	const ATTR_BLOCK_LEVEL = 11;
	/**
	 * Trim the whitespace after the opening tag or the closing tag or both.
	 * One of self::TRIM_*
	 * Optional
	 */
	const ATTR_TRIM = 12;
	/**
	 * Except when type is missing or 'closed', a callback to validate the data as $data.
	 * Depending on the tag's type, $data may be a string or an array of strings (corresponding to the replacement.)
	 */
	const ATTR_VALIDATE = 13;
	/**
	 * When type is unparsed_equals or parsed_equals only, may be not set,
	 * 'optional', or 'required' corresponding to if the content may be quoted.
	 * This allows the parser to read [tag="abc]def[esdf]"] properly.
	 */
	const ATTR_QUOTED = 14;
	/**
	 * An array of tag names, or not set.
	 * If set, the enclosing tag *must* be one of the listed tags, or parsing won't	occur.
	 */
	const ATTR_REQUIRE_PARENTS = 15;
	/**
	 * similar to require_parents, if set children won't be parsed if they are not in the list.
	 */
	const ATTR_REQUIRE_CHILDREN = 16;
	/**
	 * Similar to, but very different from, require_parents.
	 * If it is set the listed tags will not be parsed inside the tag.
	 */
	const ATTR_DISALLOW_PARENTS = 17;
	/**
	 * Similar to, but very different from, require_children.
	 * If it is set the listed tags will not be parsed inside the tag.
	 */
	const ATTR_DISALLOW_CHILDREN = 18;
	/**
	 * When ATTR_DISALLOW_* is used, this gets put before the tag.
	 */
	const ATTR_DISALLOW_BEFORE = 19;
	/**
	 * * When ATTR_DISALLOW_* is used, this gets put after the tag.
	 */
	const ATTR_DISALLOW_AFTER = 20;
	/**
	 * an array restricting what BBC can be in the parsed_equals parameter, if desired.
	 */
	const ATTR_PARSED_TAGS_ALLOWED = 21;
	/**
	 * (bool) Turn uris like http://www.google.com in to links
	 */
	const ATTR_AUTOLINK = 22;
	/**
	 * The length of the tag
	 */
	const ATTR_LENGTH = 23;
	/**
	 * Whether the tag is disabled
	 */
	const ATTR_DISABLED = 24;

	/** [tag]parsed content[/tag] */
	const TYPE_PARSED_CONTENT = 0;
	/** [tag=xyz]parsed content[/tag] */
	const TYPE_UNPARSED_EQUALS = 1;
	/** [tag=parsed data]parsed content[/tag] */
	const TYPE_PARSED_EQUALS = 2;
	/** [tag]unparsed content[/tag] */
	const TYPE_UNPARSED_CONTENT = 3;
	/** [tag], [tag/], [tag /] */
	const TYPE_CLOSED = 4;
	/** [tag=1,2,3]parsed content[/tag] */
	const TYPE_UNPARSED_COMMAS = 5;
	/** [tag=1,2,3]unparsed content[/tag] */
	const TYPE_UNPARSED_COMMAS_CONTENT = 6;
	/** [tag=...]unparsed content[/tag] */
	const TYPE_UNPARSED_EQUALS_CONTENT = 7;
	/** [*] */
	const TYPE_ITEMCODE = 8;

	/** a regular expression to validate and match the value. */
	const PARAM_ATTR_MATCH = 0;
	/** true if the value should be quoted. */
	const PARAM_ATTR_QUOTED = 1;
	/** callback to evaluate on the data, which is $data. */
	const PARAM_ATTR_VALIDATE = 2;
	/** a string in which to replace $1 with the data. Either it or validate may be used, not both. */
	const PARAM_ATTR_VALUE = 3;
	/** true if the parameter is optional. */
	const PARAM_ATTR_OPTIONAL = 4;

	/**  */
	const TRIM_NONE = 0;
	/**  */
	const TRIM_INSIDE = 1;
	/**  */
	const TRIM_OUTSIDE = 2;
	/**  */
	const TRIM_BOTH = 3;

	const OPTIONAL = -1;

	/**
	 * An array of self::ATTR_*
	 * ATTR_TAG and ATTR_TYPE are required for every tag.
	 * The rest of the attributes depend on the type and other options.
	 */
	protected $bbc = array();
	protected $itemcodes = array();
	protected $additional_bbc = array();
	protected $disabled = array();

	public function __construct(array $tags = array(), array $disabled = array())
	{
		$this->bbc = $this->getDefault();
		//$this->getDefault();
		$this->additional_bbc = $tags;
		$this->disabled = $disabled;

		foreach ($disabled as $tag)
		{
			$this->disable($tag);
		}

		foreach ($tags as $tag)
		{
			$this->add($tag);
		}
	}

	public function add(array $code)
	{
		$this->checkCode($code);

		$first_char = $code[self::ATTR_TAG][0];

		if (!isset($this->bbc[$first_char]))
		{
			$this->bbc[$first_char] = array();
		}

		$this->bbc[$first_char][] = $code;
	}

	protected function checkCode(array &$code)
	{
		if (!isset($tag[self::ATTR_TAG]) || !is_string($tag))
		{
			throw new \InvalidArgumentException('BBC must have a tag name');
		}

		$tag[self::ATTR_TAG] = trim($tag[self::ATTR_TAG]);

		if ($tag[self::ATTR_TAG] == '')
		{
			throw new \InvalidArgumentException('BBC must have a tag name');
		}

		$tag[self::ATTR_TYPE] = empty($tag[self::ATTR_TYPE]) ? self::UNPARSED_CONTENT : $tag[self::ATTR_TYPE];

		if (!is_int($tag[self::ATTR_TYPE]) || $tag[self::ATTR_TYPE] > self::TYPE_PARSED_EQUALS_CONTENT || $tag[self::ATTR_TYPE] < self::UNPARSED_CONTENT)
		{
			throw new \InvalidArgumentException('Invalid type for tag: ' . $tag[self::ATTR_TYPE]);
		}

		if (isset($tag[self::ATTR_PARAM]))
		{
			foreach ($parameters as &$parameter)
			{
				$parameter[self::PARAM_ATTR_QUOTED] = !empty($parameter[self::PARAM_ATTR_QUOTED]);
				$parameter[self::PARAM_ATTR_OPTIONAL] = !empty($parameter[self::PARAM_ATTR_OPTIONAL]);

				if (isset($parameter[self::PARAM_ATTR_VALIDATE]) && isset($parameter[self::PARAM_ATTR_VALUE]))
				{
					throw new \InvalidArgumentException('Parameters may only use value or validate, not both');
				}
			}
		}

		if (!isset($tag[self::ATTR_LENGTH]))
		{
			$tag[self::ATTR_LENGTH] = strlen($tag[self::ATTR_TAG]);
		}

		$tag[self::ATTR_AUTOLINK] = !empty($tag[self::ATTR_AUTOLINK]);
		$tag[self::ATTR_BLOCK_LEVEL] = !empty($tag[self::ATTR_BLOCK_LEVEL]);
	}

	public function remove($code)
	{
		foreach ($this->bbc as $k => $v)
		{
			if ($code === $v[self::ATTR_TAG])
			{
				unset($this->bbc[$k]);
			}
		}
	}

	public function getDefault()
	{
		global $modSettings, $txt, $scripturl;

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
			array(
				self::ATTR_TAG => 'anchor',
				self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
				// USES CLOSING BRACKET
				//self::ATTR_TEST => '[#]?([A-Za-z][A-Za-z0-9_\-]*)\]',
				self::ATTR_TEST => '[#]?([A-Za-z][A-Za-z0-9_\-]*)',
				self::ATTR_BEFORE => '<span id="post_$1">',
				self::ATTR_AFTER => '</span>',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 6,
			),
			array(
				self::ATTR_TAG => 'b',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<strong class="bbc_strong">',
				self::ATTR_AFTER => '</strong>',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 1,
			),
			array(
				self::ATTR_TAG => 'br',
				self::ATTR_TYPE => self::TYPE_CLOSED,
				self::ATTR_CONTENT => '<br />',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => false,
				self::ATTR_LENGTH => 2,
			),
			array(
				self::ATTR_TAG => 'center',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<div class="centertext">',
				self::ATTR_AFTER => '</div>',
				self::ATTR_BLOCK_LEVEL => true,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 6,
			),
			array(
				self::ATTR_TAG => 'code',
				self::ATTR_TYPE => self::TYPE_UNPARSED_CONTENT,
				self::ATTR_CONTENT => '<div class="codeheader">' . $txt['code'] . ': <a href="javascript:void(0);" onclick="return elkSelectText(this);" class="codeoperation">' . $txt['code_select'] . '</a></div><pre class="bbc_code prettyprint">$1</pre>',
				self::ATTR_VALIDATE => $this->isDisabled('code') ? null : function(&$tag, &$data, $disabled) {
					$data = tabToHtmlTab($data);
				},
				self::ATTR_BLOCK_LEVEL => true,
				self::ATTR_AUTOLINK => false,
				self::ATTR_LENGTH => 4,
			),
			array(
				self::ATTR_TAG => 'code',
				self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS_CONTENT,
				self::ATTR_CONTENT => '<div class="codeheader">' . $txt['code'] . ': ($2) <a href="#" onclick="return elkSelectText(this);" class="codeoperation">' . $txt['code_select'] . '</a></div><pre class="bbc_code prettyprint">$1</pre>',
				self::ATTR_VALIDATE => $this->isDisabled('code') ? null : function(&$tag, &$data, $disabled) {
					$data[0] = tabToHtmlTab($data[0]);
				},
				self::ATTR_BLOCK_LEVEL => true,
				self::ATTR_AUTOLINK => false,
				self::ATTR_LENGTH => 4,
			),
			array(
				self::ATTR_TAG => 'color',
				self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
				// USES CLOSING BRACKET
				//self::ATTR_TEST => '(#[\da-fA-F]{3}|#[\da-fA-F]{6}|[A-Za-z]{1,20}|rgb\((?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\s?,\s?){2}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\))\]',
				self::ATTR_TEST => '(#[\da-fA-F]{3}|#[\da-fA-F]{6}|[A-Za-z]{1,20}|rgb\((?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\s?,\s?){2}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\))',
				self::ATTR_BEFORE => '<span style="color: $1;" class="bbc_color">',
				self::ATTR_AFTER => '</span>',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 5,
			),
			array(
				self::ATTR_TAG => 'email',
				self::ATTR_TYPE => self::TYPE_UNPARSED_CONTENT,
				self::ATTR_CONTENT => '<a href="mailto:$1" class="bbc_email">$1</a>',
				self::ATTR_VALIDATE => function(&$tag, &$data, $disabled) {
					$data = strtr($data, array('<br />' => ''));
				},
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => false,
				self::ATTR_LENGTH => 5,
			),
			array(
				self::ATTR_TAG => 'email',
				self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
				self::ATTR_BEFORE => '<a href="mailto:$1" class="bbc_email">',
				self::ATTR_AFTER => '</a>',
				//self::ATTR_DISALLOW_CHILDREN => array('email', 'ftp', 'url', 'iurl'),
				self::ATTR_DISALLOW_CHILDREN => array('email' => 'email', 'ftp' => 'ftp', 'url' => 'url', 'iurl' => 'iurl'),
				self::ATTR_DISABLED_AFTER => ' ($1)',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => false,
				self::ATTR_LENGTH => 5,
			),
			array(
				self::ATTR_TAG => 'footnote',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<sup class="bbc_footnotes">%fn%',
				self::ATTR_AFTER => '%fn%</sup>',
				//self::ATTR_DISALLOW_PARENTS => array('footnote', 'code', 'anchor', 'url', 'iurl'),
				self::ATTR_DISALLOW_PARENTS => array('footnote' => 'footnote', 'code' => 'code', 'anchor' => 'anchor', 'url' => 'url', 'iurl' => 'iurl'),
				self::ATTR_DISALLOW_BEFORE => '',
				self::ATTR_DISALLOW_AFTER => '',
				self::ATTR_BLOCK_LEVEL => true,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 8,
			),
			array(
				self::ATTR_TAG => 'font',
				self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
				// USES CLOSING BRACKET
				//self::ATTR_TEST => '[A-Za-z0-9_,\-\s]+?\]',
				self::ATTR_TEST => '[A-Za-z0-9_,\-\s]+?',
				self::ATTR_BEFORE => '<span style="font-family: $1;" class="bbc_font">',
				self::ATTR_AFTER => '</span>',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 4,
			),
			/*			array(
							self::ATTR_TAG => 'ftp',
							self::ATTR_TYPE => self::TYPE_UNPARSED_CONTENT,
							self::ATTR_CONTENT => '<a href="$1" class="bbc_ftp new_win" target="_blank">$1</a>',
							self::ATTR_VALIDATE => function(&$tag, &$data, $disabled) {
								$data = strtr($data, array('<br />' => ''));
								if (strpos($data, 'ftp://') !== 0 && strpos($data, 'ftps://') !== 0)
								{
									$data = 'ftp://' . $data;
								}
							},
							self::ATTR_BLOCK_LEVEL => false,
							self::ATTR_AUTOLINK => false,
							self::ATTR_LENGTH => 3,
						),
						array(
							self::ATTR_TAG => 'ftp',
							self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
							self::ATTR_BEFORE => '<a href="$1" class="bbc_ftp new_win" target="_blank">',
							self::ATTR_AFTER => '</a>',
							self::ATTR_VALIDATE => function(&$tag, &$data, $disabled) {
								if (strpos($data, 'ftp://') !== 0 && strpos($data, 'ftps://') !== 0)
								{
									$data = 'ftp://' . $data;
								}
							},
							self::ATTR_DISALLOW_CHILDREN => array('email', 'ftp', 'url', 'iurl'),
							self::ATTR_DISABLED_AFTER => ' ($1)',
							self::ATTR_BLOCK_LEVEL => false,
							self::ATTR_AUTOLINK => false,
							self::ATTR_LENGTH => 3,
						),
			*/			array(
				self::ATTR_TAG => 'hr',
				self::ATTR_TYPE => self::TYPE_CLOSED,
				self::ATTR_CONTENT => '<hr />',
				self::ATTR_BLOCK_LEVEL => true,
				self::ATTR_AUTOLINK => false,
				self::ATTR_LENGTH => 2,
			),
			array(
				self::ATTR_TAG => 'i',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<em>',
				self::ATTR_AFTER => '</em>',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 1,
			),
			array(
				self::ATTR_TAG => 'img',
				self::ATTR_TYPE => self::TYPE_UNPARSED_CONTENT,
				self::ATTR_PARAM => array(
					'alt' => array(self::PARAM_ATTR_OPTIONAL => true),
					'width' => array(
						self::PARAM_ATTR_OPTIONAL => true,
						self::PARAM_ATTR_VALUE => 'width:100%;max-width:$1px;',
						self::PARAM_ATTR_MATCH => '(\d+)'
					),
					'height' => array(
						self::PARAM_ATTR_OPTIONAL => true,
						self::PARAM_ATTR_VALUE => 'max-height:$1px;',
						self::PARAM_ATTR_MATCH => '(\d+)'
					),
				),
				self::ATTR_CONTENT => '<img src="$1" alt="{alt}" style="{width}{height}" class="bbc_img resized" />',
				self::ATTR_VALIDATE => function(&$tag, &$data, $disabled) {
					$data = removeBr($data);
					if (strpos($data, 'http://') !== 0 && strpos($data, 'https://') !== 0)
					{
						$data = 'http://' . $data;
					}
				},
				self::ATTR_DISABLED_CONTENT => '($1)',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => false,
				self::ATTR_LENGTH => 3,
			),
			array(
				self::ATTR_TAG => 'img',
				self::ATTR_TYPE => self::TYPE_UNPARSED_CONTENT,
				self::ATTR_CONTENT => '<img src="$1" alt="" class="bbc_img" />',
				self::ATTR_VALIDATE => function(&$tag, &$data, $disabled) {
					$data = removeBr($data);
					if (strpos($data, 'http://') !== 0 && strpos($data, 'https://') !== 0)
					{
						$data = 'http://' . $data;
					}
				},
				self::ATTR_DISABLED_CONTENT => '($1)',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => false,
				self::ATTR_LENGTH => 3,
			),
			array(
				self::ATTR_TAG => 'iurl',
				self::ATTR_TYPE => self::TYPE_UNPARSED_CONTENT,
				self::ATTR_CONTENT => '<a href="$1" class="bbc_link">$1</a>',
				self::ATTR_VALIDATE => function(&$tag, &$data, $disabled) {
					$data = removeBr($data);
					if (strpos($data, 'http://') !== 0 && strpos($data, 'https://') !== 0)
					{
						$data = 'http://' . $data;
					}
				},
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
				//self::ATTR_DISALLOW_CHILDREN => array('email', 'ftp', 'url', 'iurl'),
				self::ATTR_DISALLOW_CHILDREN => array('email' => 'email', 'ftp' => 'ftp', 'url' => 'url', 'iurl' => 'iurl'),
				self::ATTR_DISABLED_AFTER => ' ($1)',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => false,
				self::ATTR_LENGTH => 4,
			),
			array(
				self::ATTR_TAG => 'left',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<div style="text-align: left;">',
				self::ATTR_AFTER => '</div>',
				self::ATTR_BLOCK_LEVEL => true,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 4,
			),
			array(
				self::ATTR_TAG => 'li',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<li>',
				self::ATTR_AFTER => '</li>',
				self::ATTR_TRIM => self::TRIM_OUTSIDE,
				self::ATTR_REQUIRE_PARENTS => array('list' => 'list'),
				self::ATTR_BLOCK_LEVEL => true,
				self::ATTR_DISABLED_BEFORE => '',
				self::ATTR_DISABLED_AFTER => '<br />',
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 2,
			),
			array(
				self::ATTR_TAG => 'list',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<ul class="bbc_list">',
				self::ATTR_AFTER => '</ul>',
				self::ATTR_TRIM => self::TRIM_INSIDE,
				self::ATTR_REQUIRE_CHILDREN => array('li' => 'li', 'list' => 'list'),
				self::ATTR_BLOCK_LEVEL => true,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 4,
			),
			array(
				self::ATTR_TAG => 'list',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_PARAM => array(
					'type' => array(
						self::PARAM_ATTR_MATCH => '(none|disc|circle|square|decimal|decimal-leading-zero|lower-roman|upper-roman|lower-alpha|upper-alpha|lower-greek|lower-latin|upper-latin|hebrew|armenian|georgian|cjk-ideographic|hiragana|katakana|hiragana-iroha|katakana-iroha)'
					),
				),
				self::ATTR_BEFORE => '<ul class="bbc_list" style="list-style-type: {type};">',
				self::ATTR_AFTER => '</ul>',
				self::ATTR_TRIM => self::TRIM_INSIDE,
				self::ATTR_REQUIRE_CHILDREN => array('li' => 'li'),
				self::ATTR_BLOCK_LEVEL => true,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 4,
			),
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
			array(
				self::ATTR_TAG => 'member',
				self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
				// USES CLOSING BRACKET
				self::ATTR_TEST => '[\d*]',
				self::ATTR_BEFORE => '<span class="bbc_mention"><a href="' . $scripturl . '?action=profile;u=$1">@',
				self::ATTR_AFTER => '</a></span>',
				self::ATTR_DISABLED_BEFORE => '@',
				self::ATTR_DISABLED_AFTER => '',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 6,
			),
			array(
				self::ATTR_TAG => 'nobbc',
				self::ATTR_TYPE => self::TYPE_UNPARSED_CONTENT,
				self::ATTR_CONTENT => '$1',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 5,
			),
			array(
				self::ATTR_TAG => 'pre',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<pre class="bbc_pre">',
				self::ATTR_AFTER => '</pre>',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 3,
			),
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
						self::PARAM_ATTR_QUOTED => true
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
				// Don't allow everything to be embedded with the author name.
				//self::ATTR_PARSED_TAGS_ALLOWED => array('url', 'iurl', 'ftp'),
				self::ATTR_PARSED_TAGS_ALLOWED => array('url', 'iurl'),
				self::ATTR_BLOCK_LEVEL => true,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 5,
			),
			array(
				self::ATTR_TAG => 'quote',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_PARAM => array(
					'author' => array(self::PARAM_ATTR_MATCH => '([^<>]{1,192}?)'),
					'link' => array(self::PARAM_ATTR_MATCH => '(?:board=\d+;)?((?:topic|threadid)=[\dmsg#\./]{1,40}(?:;start=[\dmsg#\./]{1,40})?|msg=\d{1,40}|action=profile;u=\d+)'),
					'date' => array(self::PARAM_ATTR_MATCH => '(\d+)', self::ATTR_VALIDATE => 'htmlTime'),
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
					'author' => array(self::PARAM_ATTR_MATCH => '(.{1,192}?)'),
				),
				self::ATTR_BEFORE => '<div class="quoteheader">' . $txt['quote_from'] . ': {author}</div><blockquote>',
				self::ATTR_AFTER => '</blockquote>',
				self::ATTR_BLOCK_LEVEL => true,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 5,
			),
			array(
				self::ATTR_TAG => 'right',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<div style="text-align: right;">',
				self::ATTR_AFTER => '</div>',
				self::ATTR_BLOCK_LEVEL => true,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 5,
			),
			array(
				self::ATTR_TAG => 's',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<del>',
				self::ATTR_AFTER => '</del>',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 1,
			),
			array(
				self::ATTR_TAG => 'size',
				self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
				// USES CLOSING BRACKET
				//self::ATTR_TEST => '([1-9][\d]?p[xt]|small(?:er)?|large[r]?|x[x]?-(?:small|large)|medium|(0\.[1-9]|[1-9](\.[\d][\d]?)?)?em)\]',
				self::ATTR_TEST => '([1-9][\d]?p[xt]|small(?:er)?|large[r]?|x[x]?-(?:small|large)|medium|(0\.[1-9]|[1-9](\.[\d][\d]?)?)?em)',
				self::ATTR_BEFORE => '<span style="font-size: $1;" class="bbc_size">',
				self::ATTR_AFTER => '</span>',
				self::ATTR_DISALLOW_PARENTS => array('size' => 'size'),
				self::ATTR_DISALLOW_BEFORE => '<span>',
				self::ATTR_DISALLOW_AFTER => '</span>',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 4,
			),
			array(
				self::ATTR_TAG => 'size',
				self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
				// USES CLOSING BRACKET
				//self::ATTR_TEST => '[1-7]\]',
				//self::ATTR_TEST => '[1-7]',
				self::ATTR_TEST => '[1-7]{1}$',
				self::ATTR_BEFORE => '<span style="font-size: $1;" class="bbc_size">',
				self::ATTR_AFTER => '</span>',
				self::ATTR_VALIDATE => function(&$tag, &$data, $disabled) {
					$sizes = array(1 => 0.7, 2 => 1.0, 3 => 1.35, 4 => 1.45, 5 => 2.0, 6 => 2.65, 7 => 3.95);
					$data = $sizes[(int) $data] . 'em';
				},
				self::ATTR_DISALLOW_PARENTS => array('size' => 'size'),
				self::ATTR_DISALLOW_BEFORE => '<span>',
				self::ATTR_DISALLOW_AFTER => '</span>',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 4,
			),
			array(
				self::ATTR_TAG => 'spoiler',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<span class="spoilerheader">' . $txt['spoiler'] . '</span><div class="spoiler"><div class="bbc_spoiler" style="display: none;">',
				self::ATTR_AFTER => '</div></div>',
				self::ATTR_BLOCK_LEVEL => true,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 7,
			),
			array(
				self::ATTR_TAG => 'sub',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<sub>',
				self::ATTR_AFTER => '</sub>',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 3,
			),
			array(
				self::ATTR_TAG => 'sup',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<sup>',
				self::ATTR_AFTER => '</sup>',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 3,
			),
			array(
				self::ATTR_TAG => 'table',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<div class="bbc_table_container"><table class="bbc_table">',
				self::ATTR_AFTER => '</table></div>',
				self::ATTR_TRIM => self::TRIM_INSIDE,
				self::ATTR_REQUIRE_CHILDREN => array('tr' => 'tr'),
				self::ATTR_BLOCK_LEVEL => true,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 5,
			),
			array(
				self::ATTR_TAG => 'td',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<td>',
				self::ATTR_AFTER => '</td>',
				self::ATTR_REQUIRE_PARENTS => array('tr' => 'tr'),
				self::ATTR_TRIM => self::TRIM_OUTSIDE,
				self::ATTR_BLOCK_LEVEL => true,
				self::ATTR_DISABLED_BEFORE => '',
				self::ATTR_DISABLED_AFTER => '',
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 2,
			),
			array(
				self::ATTR_TAG => 'th',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<th>',
				self::ATTR_AFTER => '</th>',
				self::ATTR_REQUIRE_PARENTS => array('tr' => 'tr'),
				self::ATTR_TRIM => self::TRIM_OUTSIDE,
				self::ATTR_BLOCK_LEVEL => true,
				self::ATTR_DISABLED_BEFORE => '',
				self::ATTR_DISABLED_AFTER => '',
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 2,
			),
			array(
				self::ATTR_TAG => 'tr',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<tr>',
				self::ATTR_AFTER => '</tr>',
				self::ATTR_REQUIRE_PARENTS => array('table' => 'table'),
				self::ATTR_REQUIRE_CHILDREN => array('td' => 'td', 'th' => 'th'),
				self::ATTR_TRIM => self::TRIM_BOTH,
				self::ATTR_BLOCK_LEVEL => true,
				self::ATTR_DISABLED_BEFORE => '',
				self::ATTR_DISABLED_AFTER => '',
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 2,
			),
			array(
				self::ATTR_TAG => 'tt',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<span class="bbc_tt">',
				self::ATTR_AFTER => '</span>',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 2,
			),
			array(
				self::ATTR_TAG => 'u',
				self::ATTR_TYPE => self::TYPE_PARSED_CONTENT,
				self::ATTR_BEFORE => '<span class="bbc_u">',
				self::ATTR_AFTER => '</span>',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => true,
				self::ATTR_LENGTH => 1,
			),
			array(
				self::ATTR_TAG => 'url',
				self::ATTR_TYPE => self::TYPE_UNPARSED_CONTENT,
				self::ATTR_CONTENT => '<a href="$1" class="bbc_link" target="_blank">$1</a>',
				self::ATTR_VALIDATE => function(&$tag, &$data, $disabled) {
					$data = removeBr($data);
					if (strpos($data, 'http://') !== 0 && strpos($data, 'https://') !== 0)
					{
						$data = 'http://' . $data;
					}
				},
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => false,
				self::ATTR_LENGTH => 3,
			),
			array(
				self::ATTR_TAG => 'url',
				self::ATTR_TYPE => self::TYPE_UNPARSED_EQUALS,
				self::ATTR_BEFORE => '<a href="$1" class="bbc_link" target="_blank">',
				self::ATTR_AFTER => '</a>',
				self::ATTR_VALIDATE => function(&$tag, &$data, $disabled) {
					if (strpos($data, 'http://') !== 0 && strpos($data, 'https://') !== 0)
					{
						$data = 'http://' . $data;
					}
				},
				//self::ATTR_DISALLOW_CHILDREN => array('email', 'ftp', 'url', 'iurl'),
				self::ATTR_DISALLOW_CHILDREN => array('email' => 'email', 'ftp' => 'ftp', 'url' => 'url', 'iurl' => 'iurl'),
				self::ATTR_DISABLED_AFTER => ' ($1)',
				self::ATTR_BLOCK_LEVEL => false,
				self::ATTR_AUTOLINK => false,
				self::ATTR_LENGTH => 3,
			),
		);
	}

	public function getItemCodes()
	{
		$item_codes = array(
			'*' => 'disc',
			'@' => 'disc',
			'+' => 'square',
			'x' => 'square',
			'#' => 'decimal',
			'0' => 'decimal',
			'o' => 'circle',
			'O' => 'circle',
		);

		//call_integration_hook('integrate_item_codes', array(&$item_codes));

		return $item_codes;
	}

	public function getCodes()
	{
		return $this->bbc;
	}

	public function getCodesGroupedByTag()
	{
		$bbc = array();
		foreach ($this->bbc as $code)
		{
			if (!isset($bbc[$code[self::ATTR_TAG]]))
			{
				$bbc[$code[self::ATTR_TAG]] = array();
			}
			$bbc[$code[self::ATTR_TAG]][] = $code;
		}
		return $bbc;
	}

	public function getTags()
	{
		$tags = array();
		foreach ($this->bbc as $tag)
		{
			$tags[$tag[self::ATTR_TAG]] = $tag[self::ATTR_TAG];
		}

		return $tags;
	}

	// @todo besides the itemcodes (just add a arg $with_itemcodes), this way should be standard and saved like that.
	// Even, just remove the itemcodes when needed
	public function getForParsing()
	{
		$bbc = $this->bbc;
		//call_integration_hook('bbc_codes_parsing', array(&$bbc, &$itemcodes));

		if (!$this->isDisabled('li') && !$this->isDisabled('list'))
		{
			foreach ($this->getItemCodes() as $c => $dummy)
			{
				// Skip anything "bad"
				if (!is_string($c) || (is_string($c) && trim($c) === ''))
				{
					continue;
				}

				$bbc[$c] = $this->getItemCodeTag($c);
			}
		}

		$return = array();
		// Find the first letter of the tag faster
		foreach ($bbc as &$code)
		{
			$return[$code[self::ATTR_TAG][0]][] = $code;

			// Add the markers around the string for BEFORE/AFTER
			//$code[Codes::ATTR_BEFORE] = "\n" . $code[Codes::ATTR_BEFORE] . "\n";
			//$code[Codes::ATTR_AFTER] = "\n" . $code[Codes::ATTR_AFTER] . "\n";
		}

		return $return;
	}

	public function newGetBBC()
	{
		$bbc = array();

		// Get the default codes
		foreach ($this->bbc as $code)
		{
			$char = $code[0];
			$tag = $code[self::ATTR_TAG];

			if (!isset($return[$char]))
			{
				$bbc[$code[0]] = array();
			}

			if (!isset($return[$char][$tag]))
			{
				$bbc[$char][$tag] = array(
					'length' => $code[self::ATTR_LENGTH],
					'tag' => $tag,
					'codes' => array(),
				);
			}

			$bbc[$char][$tag]['codes'][] = $code;
		}

		return $bbc;
	}

	protected function getItemCodeTag($code)
	{
		return array(
			self::ATTR_TAG => $code,
			self::ATTR_TYPE => self::TYPE_ITEMCODE,
			self::ATTR_BLOCK_LEVEL => true,
			self::ATTR_LENGTH => 1,
			'itemcode' => true,
		);
	}

	public function setForPrinting()
	{
		// Colors can't well be displayed... supposed to be black and white.
		$this->disable('color');
		$this->disable('me');

		// Links are useless on paper... just show the link.
		$this->disable('url');
		$this->disable('iurl');
		$this->disable('email');

		// @todo Change maybe?
		if (!isset($_GET['images']))
		{
			$this->disable('img');
		}

		// @todo Interface/setting to add more?
		// call_integration_hook();

		return $this;
	}

	public function isDisabled($tag)
	{
		return isset($this->disabled[$tag]);
	}

	public function getDisabled()
	{
		return $this->disabled;
	}

	public function disable($tag)
	{
		// It was already disabled.
		if (isset($this->disabled[$tag]))
		{
			return true;
		}

		/*foreach ($this->bbc as &$bbc)
		{
			if ($bbc[self::ATTR_TAG] === $tag)
			{
				$bbc[self::ATTR_DISABLED] = true;
			}
		}*/

		$this->disabled[$tag] = $tag;
	}

	public function enable($tag)
	{
		if (!isset($this->disabled[$tag]))
		{
			return true;
		}

		foreach ($this->bbc as &$bbc)
		{
			if ($bbc['tag'] === $tag)
			{
				$bbc[self::ATTR_DISABLED] = false;
			}
		}

		unset($this->disabled[$tag]);
	}

	public function setParsedTags($parse_tags)
	{
		foreach ($this->bbc as $k => $code)
		{
			if (!in_array($code[self::ATTR_TAG], $parse_tags))
			{
				//$this->remove($code);
				unset($this->bbc[$k]);

				$this->disabled[$code[self::ATTR_TAG]] = $code[self::ATTR_TAG];
			}
		}
	}
}