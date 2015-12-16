<?php

/* The original message
You\\n[code=me]\\nget 1\\n[/code]and [code]\\nyou get one[/code]
*/


class Message111 implements MessageInterface
{
    public static function name()
    {
        return 'Message111';
    }

    public static function input()
    {
        return 'You\\n[code=me]\\nget 1\\n[/code]and [code]\\nyou get one[/code]';
    }

    public static function stored()
    {
        return 'You\\n[code=me]\\nget 1\\n[/code]and [code]\\nyou get one[/code]';
    }

    public static function output()
    {
        return 'You\\n<div class=\"codeheader\">code: (me) <a href=\"#\" onclick=\"return elkSelectText(this);\" class=\"codeoperation\">select</a></div><pre class=\"bbc_code prettyprint\">\\nget 1\\n</pre>and <div class=\"codeheader\">code: <a href=\"javascript:void(0);\" onclick=\"return elkSelectText(this);\" class=\"codeoperation\">select</a></div><pre class=\"bbc_code prettyprint\">\\nyou get one</pre>';
    }
}