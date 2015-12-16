<?php

/* The original message
[quote]Some[quote]basic[/quote]nesting[/quote]
*/


class Message54 implements MessageInterface
{
    public static function name()
    {
        return 'Message54';
    }

    public static function input()
    {
        return '[quote]Some[quote]basic[/quote]nesting[/quote]';
    }

    public static function stored()
    {
        return '[quote]Some[quote]basic[/quote]nesting[/quote]';
    }

    public static function output()
    {
        return '<div class=\"quoteheader\">quote</div><blockquote class=\"bbc_standard_quote\">Some<div class=\"quoteheader\">quote</div><blockquote class=\"bbc_alternate_quote\">basic</blockquote>nesting</blockquote>';
    }
}