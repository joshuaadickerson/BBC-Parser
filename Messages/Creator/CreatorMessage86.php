<?php

/* The original message
[color=red]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/color]
*/


class Message86 implements MessageInterface
{
    public static function name()
    {
        return 'Message86';
    }

    public static function input()
    {
        return '[color=red]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/color]';
    }

    public static function stored()
    {
        return '[color=red]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/color]';
    }

    public static function output()
    {
        return '<span style=\"color: red;\" class=\"bbc_color\">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</span>';
    }
}