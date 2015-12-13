<?php

/* The original message
[*]one dot
[*]two dots
*/


class Message60 implements MessageInterface
{
    public static function name()
    {
        return 'Message60';
    }

    public static function input()
    {
        return '[*]one dot
[*]two dots';
    }

    public static function stored()
    {
        return '[*]one dot<br />[*]two dots';
    }

    public static function output()
    {
        return '<ul style=\"list-style-type: disc\" class=\"bbc_list\"><li>one dot</li><li>two dots</li></ul>';
    }
}