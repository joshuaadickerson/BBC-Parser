<?php

namespace BBC;

// @todo add attribute for TEST_PARAM_STRING and TEST_CONTENT so people can test the content
// @todo change ATTR_TEST to be able to test the entire message with the current offset

class RegexCodes extends Codes
{
	protected $codes;
	protected $tags;
	protected $opening_tags;
	protected $closing_tags;
	protected $itemcodes;
	protected $disabled;

	public function add(array $code, $skip_check = false)
	{
		if (!$skip_check)
		{
			$this->checkCode($code);
		}

		$tag = $code[self::ATTR_TAG];
		$opening_tag = '[' . $tag;
		$closing_tag = '[/' . $tag . ']';

		// They could be adding a disabled tag
		if ($this->isDisabled($tag))
		{
			$code[self::ATTR_DISABLED] = true;
		}
		elseif (!empty($code[self::ATTR_DISABLED]))
		{
			$this->disable($code[self::ATTR_DISABLED]);
		}

		// Add a new opening tag
		if (!isset($this->opening_tags[$opening_tag]))
		{
			$this->opening_tags[$opening_tag] = $closing_tag;
		}

		// Add a new closing tag
		if (!isset($this->closing_tags[$closing_tag]))
		{
			$this->closing_tags[$closing_tag] = $opening_tag;
		}

		// Finally, add it to the codes array
		if (!isset($this->codes[$tag]))
		{
			$this->codes[$opening_tag] = array();
		}

		$this->codes[$opening_tag][] = $code;
	}

	public function getTokenRegex()
	{
		// @todo itemcodes should be ([\n \t;>][itemcode])
		// Capture ] and &quot; as well
		$split_chars = array('(\])', '(&quot;)');

		// Get a list of just tags
		$tags = $this->getTags();

		// Sort the tags by their length
		usort($tags, function ($a, $b) {
			return strlen($b) - strlen($a);
		});

		foreach ($tags as $bbc)
		{
			$split_chars[] = '(' . preg_quote('[' . $bbc) . ')';
			// Closing tags are easy. They must have [/.*]
			$split_chars[] = '(' . preg_quote('[/' . $bbc) . '])';
		}

		// Now add the itemcodes
		foreach ($this->getItemCodes() as $code => $dummy)
		{
			$split_cars[] = '[\n\s;>](\[' . preg_quote($code) . '\])';
		}

		return '~' . implode('|', $split_chars) . '~';
	}

	public function remove($tag)
	{

	}

	public function getCodes()
	{
		return $this->codes;
	}

	protected function addTag($tag)
	{
		$this->tags['[' . $tag] = '[/' . $tag . ']';
		$this->closing_tags['[/' . $tag . ']'] = '[' . $tag;;
	}

	protected function setBBC($input)
	{
		foreach ($input as $code)
		{
			$this->add($code);
		}
	}

	protected function setTags($codes)
	{
		$tags = array();
		foreach ($codes as $tag => $code)
		{
			$tags[$tag] = '[/' . $tag . ']';
		}
	}

	public function getOpeningTags()
	{
		return $this->opening_tags;
	}

	public function getClosingTags()
	{
		return $this->closing_tags;
	}

	// The same as getDefault() used to do but instead of returning, it sets them as properties
	// You can get what you want by loading the codes and then using getCodes()
	// If you want to load the default codes over what's existing, you should create a new instance of this
	protected function loadDefaultCodes()
	{

	}
}