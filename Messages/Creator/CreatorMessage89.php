<?php

/* The original message
[color=DarkSlateBlue]this is colored![/color]
*/


class Message89 implements MessageInterface
{
    public static function name()
    {
        return 'Message89';
    }

    public static function input()
    {
        return '[color=DarkSlateBlue]this is colored![/color]';
    }

    public static function stored()
    {
        return '[color=DarkSlateBlue]this is colored![/color]';
    }

    public static function output()
    {
        return '<span style=\"color: DarkSlateBlue;\" class=\"bbc_color\">this is colored!</span>';
    }
}