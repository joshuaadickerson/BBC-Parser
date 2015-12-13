<?php

/* The original message
You\\n[code=me]\\nget 1\\n[/code]and [code]\\nyou get one[/code]
*/


class Message109 implements MessageInterface
{
    public static function name()
    {
        return 'Message109';
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
        return 'You\\n<div class=\"codeheader\">: (me) <a href=\"#\" onclick=\"return elkSelectText(this);\" class=\"codeoperation\"></a></div><pre class=\"bbc_code prettyprint\">\\nget 1\\n</pre>and <div class=\"codeheader\">: <a href=\"javascript:void(0);\" onclick=\"return elkSelectText(this);\" class=\"codeoperation\"></a></div><pre class=\"bbc_code prettyprint\">\\nyou get one</pre>';
    }
}