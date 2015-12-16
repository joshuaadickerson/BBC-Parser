<?php

/* The original message
[color=#ff0088]Quisque viverra feugiat purus, in luctus faucibus felis eget viverra.[/color]
*/


class Message89 implements MessageInterface
{
    public static function name()
    {
        return 'Message89';
    }

    public static function input()
    {
        return '[color=#ff0088]Quisque viverra feugiat purus, in luctus faucibus felis eget viverra.[/color]';
    }

    public static function stored()
    {
        return '[color=#ff0088]Quisque viverra feugiat purus, in luctus faucibus felis eget viverra.[/color]';
    }

    public static function output()
    {
        return '<span style=\"color: #ff0088;\" class=\"bbc_color\">Quisque viverra feugiat purus, in luctus faucibus felis eget viverra.</span>';
    }
}