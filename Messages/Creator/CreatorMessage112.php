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


class Message112 implements MessageInterface
{
    public static function name()
    {
        return 'Message112';
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
        return '';
    }
}