<?php

/* The original message
[s]Strike through[/s]
*/


class Message11 implements MessageInterface
{
    public static function name()
    {
        return 'Message11';
    }

    public static function input()
    {
        return '[s]Strike through[/s]';
    }

    public static function stored()
    {
        return '[s]Strike through[/s]';
    }

    public static function output()
    {
        return '[s]Strike through[/s]';
    }
}