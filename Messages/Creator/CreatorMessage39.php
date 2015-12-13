<?php

/* The original message
[size=1]BIG E[/size]
*/


class Message39 implements MessageInterface
{
    public static function name()
    {
        return 'Message39';
    }

    public static function input()
    {
        return '[size=1]BIG E[/size]';
    }

    public static function stored()
    {
        return '[size=1]BIG E[/size]';
    }

    public static function output()
    {
        return '<span style=\"font-size: 0.7em;\" class=\"bbc_size\">BIG E</span>';
    }
}