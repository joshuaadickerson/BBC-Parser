<?php

/* The original message
[code]I [b]am[/b] a robot [quote]bee boo bee boop[/quote][/code]
*/


class Message110 implements MessageInterface
{
    public static function name()
    {
        return 'Message110';
    }

    public static function input()
    {
        return '[code]I [b]am[/b] a robot [quote]bee boo bee boop[/quote][/code]';
    }

    public static function stored()
    {
        return '[code]I [b]am[/b] a robot [quote]bee boo bee boop[/quote][/code]';
    }

    public static function output()
    {
        return '<div class=\"codeheader\">: <a href=\"javascript:void(0);\" onclick=\"return elkSelectText(this);\" class=\"codeoperation\"></a></div><pre class=\"bbc_code prettyprint\">I [b]am[/b] a robot [quote]bee boo bee boop[/quote]</pre>';
    }
}