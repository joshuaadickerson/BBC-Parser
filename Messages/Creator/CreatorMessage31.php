<?php

/* The original message
[code]bee boop bee booo[/code]
*/


class Message31 implements MessageInterface
{
    public static function name()
    {
        return 'Message31';
    }

    public static function input()
    {
        return '[code]bee boop bee booo[/code]';
    }

    public static function stored()
    {
        return '[code]bee boop bee booo[/code]';
    }

    public static function output()
    {
        return '<div class=\"codeheader\">code: <a href=\"javascript:void(0);\" onclick=\"return elkSelectText(this);\" class=\"codeoperation\">select</a></div><pre class=\"bbc_code prettyprint\">bee boop bee booo</pre>';
    }
}