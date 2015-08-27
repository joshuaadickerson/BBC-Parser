<?php

class Autolink
{
	public function __construct()
	{

	}

	public function links(&$data)
	{

		// Parse any URLs.... have to get rid of the @ problems some things cause... stupid email addresses.
		if (!$this->bbc->isDisabled('url') && (strpos($data, '://') !== false || strpos($data, 'www.') !== false))
		{
			// Switch out quotes really quick because they can cause problems.
			$data = str_replace(array('&#039;', '&nbsp;', '&quot;', '"', '&lt;'), array('\'', "\xC2\xA0", '>">', '<"<', '<lt<'), $data);

			$result = preg_replace($this->autolink_search, $this->autolink_replace, $data);

			// Only do this if the preg survives.
			if (is_string($result))
			{
				$data = $result;
			}

			// Switch those quotes back
			$data = str_replace(array('\'', "\xC2\xA0", '>">', '<"<', '<lt<'), array('&#039;', '&nbsp;', '&quot;', '"', '&lt;'), $data);
		}
	}

	public function email(&$data)
	{
		// Next, emails...
		if (!$this->bbc->isDisabled('email') && strpos($data, '@') !== false)
		{
			$data = preg_replace('~(?<=[\?\s\x{A0}\[\]()*\\\;>]|^)([\w\-\.]{1,80}@[\w\-]+\.[\w\-\.]+[\w\-])(?=[?,\s\x{A0}\[\]()*\\\]|$|<br />|&nbsp;|&gt;|&lt;|&quot;|&#039;|\.(?:\.|;|&nbsp;|\s|$|<br />))~u', '[email]$1[/email]', $data);
			$data = preg_replace('~(?<=<br />)([\w\-\.]{1,80}@[\w\-]+\.[\w\-\.]+[\w\-])(?=[?\.,;\s\x{A0}\[\]()*\\\]|$|<br />|&nbsp;|&gt;|&lt;|&quot;|&#039;)~u', '[email]$1[/email]', $data);
		}
	}
}