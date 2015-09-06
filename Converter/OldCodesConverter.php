<?php

// Takes your old BBC array and makes it work with the new format.
class OldCodesConverter
{
	protected $attributes = array(
		'tag'              => Codes::ATTR_TAG,
		'type'             => Codes::ATTR_TYPE,
		'parameters'       => Codes::ATTR_PARAMETERS,
		'test'             => Codes::ATTR_TEST,
		'content'          => Codes::ATTR_CONTENT,
		'before'           => Codes::ATTR_BEFORE,
		'after'            => Codes::ATTR_AFTER,
		'disabled_content' => Codes::ATTR_DISABLED_CONTENT,
		'disabled_before'  => Codes::ATTR_DISABLED_BEFORE,
		'disabled_after'   => Codes::ATTR_DISABLED_AFTER,
		'block_level'      => Codes::ATTR_BLOCK_LEVEL,
		'trim'             => Codes::ATTR_TRIM,
		'validate'         => Codes::ATTR_VALIDATE,
		'quoted'           => Codes::ATTR_QUOTED,
		'require_parents'  => Codes::ATTR_REQUIRE_PARENTS,
		'require_children' => Codes::ATTR_REQUIRE_CHILDREN,
		'' => Codes::ATTR_QUOTED,
		'' => Codes::ATTR_QUOTED,
		'' => Codes::ATTR_QUOTED,

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

	);

	public function convert(array $code)
	{
		$this->issues = array();
		$new_code = array();

		if (isset($code['type']))
		{
			if (isset($this->types[$code['type']]))
			{

			}
		}

		foreach ($code as $attr => $val)
		{
			if (isset($attributes[$attr]))
			{

			}
			else
			{
				$this->addIssue('Attribute was not found: ' . $attr, false);
			}
		}
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
}