<?php

/* The original message
[code]bee boop bee booo[/code]
*/


class Message107 implements MessageInterface
{
    public static function name()
    {
        return 'Message107';
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
        return '<div class=\"codeheader\">: <a href=\"javascript:void(0);\" onclick=\"return elkSelectText(this);\" class=\"codeoperation\"></a></div><pre class=\"bbc_code prettyprint\">bee boop bee booo</pre>';
    }
}