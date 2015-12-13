<?php

/* The original message
[color=#ff0088]Quisque viverra feugiat purus, in luctus faucibus felis eget viverra.[/color]
*/


class Message87 implements MessageInterface
{
    public static function name()
    {
        return 'Message87';
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
        return '[color=#ff0088]Quisque viverra feugiat purus, in luctus faucibus felis eget viverra.[/color]';
    }
}