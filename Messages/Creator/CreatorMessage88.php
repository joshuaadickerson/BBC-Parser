<?php

/* The original message
[color=#f66]Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.[/color]
*/


class Message88 implements MessageInterface
{
    public static function name()
    {
        return 'Message88';
    }

    public static function input()
    {
        return '[color=#f66]Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.[/color]';
    }

    public static function stored()
    {
        return '[color=#f66]Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.[/color]';
    }

    public static function output()
    {
        return '<span style=\"color: #f66;\" class=\"bbc_color\">Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.</span>';
    }
}