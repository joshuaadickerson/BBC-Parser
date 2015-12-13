<?php

/* The original message
[color=red]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/color]
*/


class Message84 implements MessageInterface
{
    public static function name()
    {
        return 'Message84';
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
        return '[color=red]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/color]';
    }
}