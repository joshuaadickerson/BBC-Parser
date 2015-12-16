<?php

/* The original message
[quote][quote][quote][quote]Some[quote]basic[/quote]nesting[/quote]Still[/quote]not[/quote]deep[/quote]enough
*/


class Message55 implements MessageInterface
{
    public static function name()
    {
        return 'Message55';
    }

    public static function input()
    {
        return '[quote][quote][quote][quote]Some[quote]basic[/quote]nesting[/quote]Still[/quote]not[/quote]deep[/quote]enough';
    }

    public static function stored()
    {
        return '[quote][quote][quote][quote]Some[quote]basic[/quote]nesting[/quote]Still[/quote]not[/quote]deep[/quote]enough';
    }

    public static function output()
    {
        return '<div class=\"quoteheader\">quote</div><blockquote class=\"bbc_standard_quote\"><div class=\"quoteheader\">quote</div><blockquote class=\"bbc_alternate_quote\"><div class=\"quoteheader\">quote</div><blockquote class=\"bbc_standard_quote\"><div class=\"quoteheader\">quote</div><blockquote class=\"bbc_alternate_quote\">Some<div class=\"quoteheader\">quote</div><blockquote class=\"bbc_standard_quote\">basic</blockquote>nesting</blockquote>Still</blockquote>not</blockquote>deep</blockquote>enough';
    }
}