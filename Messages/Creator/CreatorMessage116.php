<?php

/* The original message
[code]email@domain.com
	:]   :/ >[ :p  >_>

	:happy: :aw: :cool: :kiss: :meh: :mmf: :heart:

	[/code]
*/


class Message116 implements MessageInterface
{
    public static function name()
    {
        return 'Message116';
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
        return '<div class=\"codeheader\">code: <a href=\"javascript:void(0);\" onclick=\"return elkSelectText(this);\" class=\"codeoperation\">select</a></div><pre class=\"bbc_code prettyprint\">email@domain.com<br /><span class=\"tab\">	</span>:]&nbsp;&nbsp; :/ >[ :p&nbsp; >_><br /><br /><span class=\"tab\">	</span>:happy: :aw: :cool: :kiss: :meh: :mmf: :heart:<br /><br /><span class=\"tab\">	</span></pre>';
    }
}