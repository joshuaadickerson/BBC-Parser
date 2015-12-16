<?php

/* The original message
[list][li]Test[/li][li]More[code]Some COde[/code][/li][/list]
*/


class Message131 implements MessageInterface
{
    public static function name()
    {
        return 'Message131';
    }

    public static function input()
    {
        return '[list][li]Test[/li][li]More[code]Some COde[/code][/li][/list]';
    }

    public static function stored()
    {
        return '[list][li]Test[/li][/list][li]More[/li][/list][code]Some COde[/code][list][/li][/list]';
    }

    public static function output()
    {
        return '<ul class=\"bbc_list\"><li>Test</li></ul>[li]More[/li][/list]<div class=\"codeheader\">code: <a href=\"javascript:void(0);\" onclick=\"return elkSelectText(this);\" class=\"codeoperation\">select</a></div><pre class=\"bbc_code prettyprint\">Some COde</pre><ul class=\"bbc_list\">[/li]</ul>';
    }
}