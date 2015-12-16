<?php

/* The original message
[font=Tahoma]Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.[/font]
*/


class Message94 implements MessageInterface
{
    public static function name()
    {
        return 'Message94';
    }

    public static function input()
    {
        return '[font=Tahoma]Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.[/font]';
    }

    public static function stored()
    {
        return '[font=Tahoma]Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.[/font]';
    }

    public static function output()
    {
        return '<span style=\"font-family: Tahoma;\" class=\"bbc_font\">Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.</span>';
    }
}