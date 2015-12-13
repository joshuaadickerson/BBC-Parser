<?php

/* The original message
[size=6.2]itty bitty (does not pass test)[/size]
*/


class Message123 implements MessageInterface
{
    public static function name()
    {
        return 'Message123';
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