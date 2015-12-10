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
	 * Except when type self::TYPE_PARSED_CONTENT or self::TYPE_CLOSED, a callback to validate the data as $data.
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
	 * When ATTR_DISALLOW_PARENTS is used, this gets put before the tag.
	 */
	const ATTR_DISALLOW_BEFORE = 19;
	/**
	 * * When ATTR_DISALLOW_PARENTS is used, this gets put after the tag.
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
	/**
	 * If the message contains a code with this, the message should not be cached
	 */
	const ATTR_NO_CACHE = 25;

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

	// These are mainly for *ATTR_QUOTED since there are 3 options
	const OPTIONAL = -1;
	const NONE = 0;
	const REQUIRED = 1;


	/**
	 * An array of self::ATTR_*
	 * ATTR_TAG and ATTR_TYPE are required for every tag.
	 * The rest of the attributes depend on the type and other options.
	 */
	protected $bbc = array();
	protected $itemcodes = array();
	protected $additional_bbc = array();
	protected $disabled = array();
	protected $parsing_codes = array();

	public function __construct(array $codes = array(), array $disabled = array())
	{
		$this->additional_bbc = $codes;

		foreach ($disabled as $tag)
		{
			$this->disable($tag);
		}

		foreach ($codes as $tag)
		{
			$this->add($tag);
		}
	}

	/**
	 * Add a code
	 * @param array $code
	 */
	public function add(array $code)
	{
		/*$this->checkCode($code);

		$first_char = $code[self::ATTR_TAG][0];

		if (!isset($this->bbc[$first_char]))
		{
			$this->bbc[$first_char] = array();
		}

		$this->bbc[$first_char][] = $code;*/

		$this->bbc[] = $code;
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

		call_integration_hook('integrate_item_codes', array(&$item_codes));

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
		$item_codes = $this->getItemCodes();
		call_integration_hook('bbc_codes_parsing', array(&$bbc, &$item_codes));

		if (!$this->isDisabled('li') && !$this->isDisabled('list'))
		{
			foreach ($item_codes as $c => $dummy)
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
		}

		return $return;
	}

	public function setParsingCodes()
	{
		$this->parsing_bbc = $this->getForParsing();
		return $this;
	}

	public function hasChar($char)
	{
		return isset($this->parsing_codes[$char]);
	}

	public function getCodesByChar($char)
	{
		return $this->parsing_codes[$char];
	}

	protected function getItemCodeTag($code)
	{
		return array(
			self::ATTR_TAG => $code,
			self::ATTR_TYPE => self::TYPE_ITEMCODE,
			self::ATTR_BLOCK_LEVEL => true,
			self::ATTR_LENGTH => 1,
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
		call_integration_hook('integrate_bbc_set_printing', array($this));

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

		$this->disabled[$tag] = $tag;
		return $this;
	}

	public function enable($tag)
	{
		unset($this->disabled[$tag]);
		return $this;
	}

	public function enableAll()
	{
		$this->disabled = array();
		return $this;
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

	public function tabToHtmlTab(&$tag, &$data, $disabled)
	{
		$data = tabToHtmlTab($data);
	}

	public function addProtocol(&$tag, &$data, $disabled)
	{
		if (strpos($data, 'http://') !== 0 && strpos($data, 'https://') !== 0)
		{
			$data = 'http://' . $data;
		}
	}
}