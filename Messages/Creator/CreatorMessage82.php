<?php

/* The original message
[quote]Economics is [b]everywhere[/b] :)
and understanding economics can help you make better www.decisio.ns and lead a happier life.[/quote]
*/


class Message82 implements MessageInterface
{
    public static function name()
    {
        return 'Message82';
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
        return '[quote]Economics is [b]everywhere[/b] <img src=\"//smiley.gif\" alt=\"&#58;&#41;\" title=\"\" class=\"smiley\" /><br />and understanding economics can help you make better www.decisio.ns and lead a happier life.[/quote]';
    }
}