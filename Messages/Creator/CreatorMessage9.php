<?php

/* The original message
[i]Italics[/i]
*/


class Message9 implements MessageInterface
{
    public static function name()
    {
        return 'Message9';
    }

    public static function input()
    {
        return '[i]Italics[/i]';
    }

    public static function stored()
    {
        return '[i]Italics[/i]';
    }

    public static function output()
    {
        return '[i]Italics[/i]';
    }
}