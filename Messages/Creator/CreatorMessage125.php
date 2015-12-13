<?php

/* The original message
[size=2]inside size[size=3] - and now even deeper [/size] pull back a little.[/size]
*/


class Message125 implements MessageInterface
{
    public static function name()
    {
        return 'Message125';
    }

    public static function input()
    {
        return '[size=2]inside size[size=3] - and now even deeper [/size] pull back a little.[/size]';
    }

    public static function stored()
    {
        return '[size=2]inside size[size=3] - and now even deeper [/size] pull back a little.[/size]';
    }

    public static function output()
    {
        return '[size=2]inside size[size=3] - and now even deeper [/size] pull back a little.[/size]';
    }
}