<?php

/* The original message
[color=red]red[/color][color=green]green[/color][color=blue]blue[/color]
*/


class Message85 implements MessageInterface
{
    public static function name()
    {
        return 'Message85';
    }

    public static function input()
    {
        return '[color=red]red[/color][color=green]green[/color][color=blue]blue[/color]';
    }

    public static function stored()
    {
        return '[color=red]red[/color][color=green]green[/color][color=blue]blue[/color]';
    }

    public static function output()
    {
        return '<span style=\"color: red;\" class=\"bbc_color\">red</span><span style=\"color: green;\" class=\"bbc_color\">green</span><span style=\"color: blue;\" class=\"bbc_color\">blue</span>';
    }
}