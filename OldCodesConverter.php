<?php

// Takes your old BBC array and makes it work with the new format.
class OldCodesConverter
{
	protected $attributes = array(
		'tag'        => Codes::ATTR_TAG,
		'type'       => Codes::ATTR_TYPE,
		'parameters' => Codes::ATTR_PARAMETERS,

	);


	protected $types = array(

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
		Codes::ATTR_TAG' . $code[Codes::ATTR_TAG] . ',
		Codes::ATTR_TYPE' . $code[Codes::ATTR_TYPE];

		foreach ($elements as $k => $v)
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