<?php

/* The original message
[quote=&quot;[u]underline[/u]]&quot;]this is weird[/quote]
*/


class Message100 implements MessageInterface
{
    public static function name()
    {
        return 'Message100';
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
        return '<div class=\"quoteheader\">quote from: [u]underline[/u]]</div><blockquote class=\"bbc_standard_quote\">this is weird</blockquote>';
    }
}