<?php

/* The original message
[code]bee boop bee booo[/code]
*/


class Message107 implements MessageInterface
{
    public static function name()
    {
        return 'Message107';
    }

    public static function input()
    {
        return '[code]bee boop bee booo[/code]';
    }

    public static function stored()
    {
        return '[code]bee boop bee booo[/code]';
    }

    public static function output()
    {
        return '[code]bee boop bee booo[/code]';
    }
}