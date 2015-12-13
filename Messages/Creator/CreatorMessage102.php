<?php

/* The original message
[center]Center Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam laoreet pulvinar sem. Aenean at odio.[/center]
*/


class Message102 implements MessageInterface
{
    public static function name()
    {
        return 'Message102';
    }

    public static function input()
    {
        return '[center]Center Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam laoreet pulvinar sem. Aenean at odio.[/center]';
    }

    public static function stored()
    {
        return '[center]Center Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam laoreet pulvinar sem. Aenean at odio.[/center]';
    }

    public static function output()
    {
        return '<div class=\"centertext\">Center Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam laoreet pulvinar sem. Aenean at odio.</div>';
    }
}