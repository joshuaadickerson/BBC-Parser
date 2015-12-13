<?php

/* The original message
[b]Bold[/b]
*/


class Message8 implements MessageInterface
{
    public static function name()
    {
        return 'Message8';
    }

    public static function input()
    {
        return '[b]Bold[/b]';
    }

    public static function stored()
    {
        return '[b]Bold[/b]';
    }

    public static function output()
    {
        return '<strong class=\"bbc_strong\">Bold</strong>';
    }
}