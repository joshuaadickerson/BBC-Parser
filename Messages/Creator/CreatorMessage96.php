<?php

/* The original message
[font=Times]Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.[/font]
*/


class Message96 implements MessageInterface
{
    public static function name()
    {
        return 'Message96';
    }

    public static function input()
    {
        return '[font=Times]Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.[/font]';
    }

    public static function stored()
    {
        return '[font=Times]Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.[/font]';
    }

    public static function output()
    {
        return '<span style=\"font-family: Times;\" class=\"bbc_font\">Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.</span>';
    }
}