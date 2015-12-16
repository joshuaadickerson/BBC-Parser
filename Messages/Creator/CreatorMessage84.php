<?php

/* The original message
[quote]Economics is [b]everywhere[/b] :)
and understanding economics can help you make better www.decisio.ns and lead a happier life.[/quote]
*/


class Message84 implements MessageInterface
{
    public static function name()
    {
        return 'Message84';
    }

    public static function input()
    {
        return '[quote]Economics is [b]everywhere[/b] :)
and understanding economics can help you make better www.decisio.ns and lead a happier life.[/quote]';
    }

    public static function stored()
    {
        return '[quote]Economics is [b]everywhere[/b] :)<br />and understanding economics can help you make better www.decisio.ns and lead a happier life.[/quote]';
    }

    public static function output()
    {
        return '<div class=\"quoteheader\">quote</div><blockquote class=\"bbc_standard_quote\">Economics is <strong class=\"bbc_strong\">everywhere</strong> <img src=\"http://www.google.com/smileys//smiley.gif\" alt=\"&#58;&#41;\" title=\"smile\" class=\"smiley\" /><br />and understanding economics can help you make better <a href=\"http://www.decisio.ns\" class=\"bbc_link\" target=\"_blank\">www.decisio.ns</a> and lead a happier life.</blockquote>';
    }
}