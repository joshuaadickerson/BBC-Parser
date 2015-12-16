<?php

/* The original message
[size=6.2]itty bitty (does not pass test)[/size]
*/


class Message125 implements MessageInterface
{
    public static function name()
    {
        return 'Message125';
    }

    public static function input()
    {
        return '[size=6.2]itty bitty (does not pass test)[/size]';
    }

    public static function stored()
    {
        return '[size=6.2]itty bitty (does not pass test)[/size]';
    }

    public static function output()
    {
        return '[size=6.2]itty bitty (does not pass test)[/size]';
    }
}