<?php

/* The original message
[code]this is unclosed
*/


class Message132 implements MessageInterface
{
    public static function name()
    {
        return 'Message132';
    }

    public static function input()
    {
        return '[code]this is unclosed';
    }

    public static function stored()
    {
        return '[code]this is unclosed[/code]';
    }

    public static function output()
    {
        return '<div class=\"codeheader\">code: <a href=\"javascript:void(0);\" onclick=\"return elkSelectText(this);\" class=\"codeoperation\">select</a></div><pre class=\"bbc_code prettyprint\">this is unclosed</pre>';
    }
}