<?php

/* The original message
[font=Monospace]Quisque viverra feugiat purus, in luctus faucibus felis eget viverra.[/font]
*/


class Message93 implements MessageInterface
{
    public static function name()
    {
        return 'Message93';
    }

    public static function input()
    {
        return '[font=Monospace]Quisque viverra feugiat purus, in luctus faucibus felis eget viverra.[/font]';
    }

    public static function stored()
    {
        return '[font=Monospace]Quisque viverra feugiat purus, in luctus faucibus felis eget viverra.[/font]';
    }

    public static function output()
    {
        return '<span style=\"font-family: Monospace;\" class=\"bbc_font\">Quisque viverra feugiat purus, in luctus faucibus felis eget viverra.</span>';
    }
}