<?php

/* The original message
[glow=red,2,50]glow[/glow]
*/


class Message115 implements MessageInterface
{
    public static function name()
    {
        return 'Message115';
    }

    public static function input()
    {
        return '[glow=red,2,50]glow[/glow]';
    }

    public static function stored()
    {
        return '[glow=red,2,50]glow[/glow]';
    }

    public static function output()
    {
        return '[glow=red,2,50]glow[/glow]';
    }
}