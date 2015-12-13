<?php

/* The original message
[code]email@domain.com
	:]   :/ >[ :p  >_>

	:happy: :aw: :cool: :kiss: :meh: :mmf: :heart:

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
        return '[code]email@domain.com
	:]   :/ >[ :p  >_>

	:happy: :aw: :cool: :kiss: :meh: :mmf: :heart:

	[/code]';
    }

    public static function stored()
    {
        return '[code]email@domain.com<br />	:]&nbsp;  :/ >[ :p&nbsp; >_><br /><br />	:happy: :aw: :cool: :kiss: :meh: :mmf: :heart:<br /><br />	[/code]';
    }

    public static function output()
    {
        return '[code]email@domain.com<br />	:]&nbsp;  :/ >[ :p&nbsp; >_><br /><br />	:happy: :aw: :cool: :kiss: :meh: :mmf: :heart:<br /><br />	[/code]';
    }
}