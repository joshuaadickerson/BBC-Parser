<?php

/* The original message
[u]Underline[/u]
*/


class Message10 implements MessageInterface
{
    public static function name()
    {
        return 'Message10';
    }

    public static function input()
    {
        return '[u]Underline[/u]';
    }

    public static function stored()
    {
        return '[u]Underline[/u]';
    }

    public static function output()
    {
        return '<span class=\"bbc_u\">Underline</span>';
    }
}