<?php

/* The original message
[quote author=Gates]Measuring programming progress by lines of code is like measuring aircraft building progress by weight.[/quote]
*/


class Message53 implements MessageInterface
{
    public static function name()
    {
        return 'Message53';
    }

    public static function input()
    {
        return '[quote author=Gates]Measuring programming progress by lines of code is like measuring aircraft building progress by weight.[/quote]';
    }

    public static function stored()
    {
        return '[quote author=Gates]Measuring programming progress by lines of code is like measuring aircraft building progress by weight.[/quote]';
    }

    public static function output()
    {
        return '<div class=\"quoteheader\">quote from: Gates</div><blockquote class=\"bbc_standard_quote\">Measuring programming progress by lines of code is like measuring aircraft building progress by weight.</blockquote>';
    }
}