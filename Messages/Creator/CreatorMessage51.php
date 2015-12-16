<?php

/* The original message
[quote]If at first you do not succeed; call it version 1.0[/quote]
*/


class Message51 implements MessageInterface
{
    public static function name()
    {
        return 'Message51';
    }

    public static function input()
    {
        return '[quote]If at first you do not succeed; call it version 1.0[/quote]';
    }

    public static function stored()
    {
        return '[quote]If at first you do not succeed; call it version 1.0[/quote]';
    }

    public static function output()
    {
        return '<div class=\"quoteheader\">quote</div><blockquote class=\"bbc_standard_quote\">If at first you do not succeed; call it version 1.0</blockquote>';
    }
}