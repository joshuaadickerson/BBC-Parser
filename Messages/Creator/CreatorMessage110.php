<?php

/* The original message
Everyone\\n[code]\\ngets a line\\n[/code]\\nbreak
*/


class Message110 implements MessageInterface
{
    public static function name()
    {
        return 'Message110';
    }

    public static function input()
    {
        return 'Everyone\\n[code]\\ngets a line\\n[/code]\\nbreak';
    }

    public static function stored()
    {
        return 'Everyone\\n[code]\\ngets a line\\n[/code]\\nbreak';
    }

    public static function output()
    {
        return 'Everyone\\n<div class=\"codeheader\">code: <a href=\"javascript:void(0);\" onclick=\"return elkSelectText(this);\" class=\"codeoperation\">select</a></div><pre class=\"bbc_code prettyprint\">\\ngets a line\\n</pre>\\nbreak';
    }
}