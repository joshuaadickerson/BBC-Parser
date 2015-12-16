<?php

/* The original message
[font=Arial]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/font]
*/


class Message93 implements MessageInterface
{
    public static function name()
    {
        return 'Message93';
    }

    public static function input()
    {
        return '[font=Arial]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/font]';
    }

    public static function stored()
    {
        return '[font=Arial]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/font]';
    }

    public static function output()
    {
        return '<span style=\"font-family: Arial;\" class=\"bbc_font\">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</span>';
    }
}