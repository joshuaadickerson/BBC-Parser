<?php

/* The original message
[code][b]Bold[/b]
	Italics
	Underline
	Strike through[/code]
*/


class Message115 implements MessageInterface
{
    public static function name()
    {
        return 'Message115';
    }

    public static function input()
    {
        return '[code][b]Bold[/b]
	Italics
	Underline
	Strike through[/code]';
    }

    public static function stored()
    {
        return '[code][b]Bold[/b]<br />	Italics<br />	Underline<br />	Strike through[/code]';
    }

    public static function output()
    {
        return '<div class=\"codeheader\">code: <a href=\"javascript:void(0);\" onclick=\"return elkSelectText(this);\" class=\"codeoperation\">select</a></div><pre class=\"bbc_code prettyprint\">[b]Bold[/b]<br /><span class=\"tab\">	</span>Italics<br /><span class=\"tab\">	</span>Underline<br /><span class=\"tab\">	</span>Strike through</pre>';
    }
}