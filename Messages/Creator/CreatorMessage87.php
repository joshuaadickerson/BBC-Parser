<?php

/* The original message
[color=blue]Volutpat tellus vulputate dui venenatis quis euismod turpis pellentesque.[/color]
*/


class Message87 implements MessageInterface
{
    public static function name()
    {
        return 'Message87';
    }

    public static function input()
    {
        return '[color=blue]Volutpat tellus vulputate dui venenatis quis euismod turpis pellentesque.[/color]';
    }

    public static function stored()
    {
        return '[color=blue]Volutpat tellus vulputate dui venenatis quis euismod turpis pellentesque.[/color]';
    }

    public static function output()
    {
        return '<span style=\"color: blue;\" class=\"bbc_color\">Volutpat tellus vulputate dui venenatis quis euismod turpis pellentesque.</span>';
    }
}