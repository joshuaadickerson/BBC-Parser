<?php

/* The original message
[u][quote]should not get underlined[/quote][/u]
*/


class Message119 implements MessageInterface
{
    public static function name()
    {
        return 'Message119';
    }

    public static function input()
    {
        return '[u][quote]should not get underlined[/quote][/u]';
    }

    public static function stored()
    {
        return '[u][quote]should not get underlined[/quote][/u]';
    }

    public static function output()
    {
        return '<span class=\"bbc_u\"></span><div class=\"quoteheader\"></div><blockquote class=\"bbc_standard_quote\">should not get underlined</blockquote>[/u]';
    }
}