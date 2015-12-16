<?php

/* The original message
[size=4]Font Family[/size]
*/


class Message92 implements MessageInterface
{
    public static function name()
    {
        return 'Message92';
    }

    public static function input()
    {
        return '[size=4]Font Family[/size]';
    }

    public static function stored()
    {
        return '[size=4]Font Family[/size]';
    }

    public static function output()
    {
        return '<span style=\"font-size: 1.45em;\" class=\"bbc_size\">Font Family</span>';
    }
}