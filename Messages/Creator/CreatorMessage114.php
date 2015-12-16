<?php

/* The original message
[code=php]
	<?php
	/**
	 * This controller is the most important and probably most accessed of all.
	 * It controls topic display, with all related.
	 */
	class Display_Controller
	{
		/**
		 * Default action handler for this controller
		 */
		public function action_index()
		{
			// what to do... display things!
			$this->action_display();
		}?>
	[/code]
*/


class Message114 implements MessageInterface
{
    public static function name()
    {
        return 'Message114';
    }

    public static function input()
    {
        return '[code=php]
	<?php
	/**
	 * This controller is the most important and probably most accessed of all.
	 * It controls topic display, with all related.
	 */
	class Display_Controller
	{
		/**
		 * Default action handler for this controller
		 */
		public function action_index()
		{
			// what to do... display things!
			$this->action_display();
		}?>
	[/code]';
    }

    public static function stored()
    {
        return '[code=php]<br />	<?php<br />	/**<br />	 * This controller is the most important and probably most accessed of all.<br />	 * It controls topic display, with all related.<br />	 */<br />	class Display_Controller<br />	{<br />		/**<br />		 * Default action handler for this controller<br />		 */<br />		public function action_index()<br />		{<br />			// what to do... display things!<br />			$this->action_display();<br />		}?><br />	[/code]';
    }

    public static function output()
    {
        return '<div class=\"codeheader\">code: (php) <a href=\"#\" onclick=\"return elkSelectText(this);\" class=\"codeoperation\">select</a></div><pre class=\"bbc_code prettyprint\"><span class=\"tab\">	</span><?php<br /><span class=\"tab\">	</span>/**<br /><span class=\"tab\">	</span> * This controller is the most important and probably most accessed of all.<br /><span class=\"tab\">	</span> * It controls topic display, with all related.<br /><span class=\"tab\">	</span> */<br /><span class=\"tab\">	</span>class Display_Controller<br /><span class=\"tab\">	</span>{<br /><span class=\"tab\">	</span><span class=\"tab\">	</span>/**<br /><span class=\"tab\">	</span><span class=\"tab\">	</span> * Default action handler for this controller<br /><span class=\"tab\">	</span><span class=\"tab\">	</span> */<br /><span class=\"tab\">	</span><span class=\"tab\">	</span>public function action_index()<br /><span class=\"tab\">	</span><span class=\"tab\">	</span>{<br /><span class=\"tab\">	</span><span class=\"tab\">	</span><span class=\"tab\">	</span>// what to do... display things!<br /><span class=\"tab\">	</span><span class=\"tab\">	</span><span class=\"tab\">	</span>$this->action_display();<br /><span class=\"tab\">	</span><span class=\"tab\">	</span>}?><br /><span class=\"tab\">	</span></pre>';
    }
}