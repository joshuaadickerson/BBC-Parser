<?php

/* The original message
[color=#cccccc]Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.[/color]
*/


class Message88 implements MessageInterface
{
    public static function name()
    {
        return 'Message88';
    }

    public static function input()
    {
        return '[color=#cccccc]Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.[/color]';
    }

    public static function stored()
    {
        return '[color=#cccccc]Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.[/color]';
    }

    public static function output()
    {
        return '<span style=\"color: #cccccc;\" class=\"bbc_color\">Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.</span>';
    }
}