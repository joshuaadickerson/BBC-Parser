<?php

/* The original message
[quote=&quot;[u]underline[/u]]&quot;]this is weird[/quote]
*/


class Message98 implements MessageInterface
{
    public static function name()
    {
        return 'Message98';
    }

    public static function input()
    {
        return '[quote=&quot;[u]underline[/u]]&quot;]this is weird[/quote]';
    }

    public static function stored()
    {
        return '[quote=&quot;[u]underline[/u]]&quot;]this is weird[/quote]';
    }

    public static function output()
    {
        return '<div class=\"quoteheader\">: [u]underline[/u]]</div><blockquote class=\"bbc_standard_quote\">this is weird</blockquote>';
    }
}