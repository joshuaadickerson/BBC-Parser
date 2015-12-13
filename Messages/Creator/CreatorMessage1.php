<?php

/* The original message
false
*/


class Message1 implements MessageInterface
{
    public static function name()
    {
        return 'Message1';
    }

    public static function input()
    {
        return 'false';
    }

    public static function stored()
    {
        return 'false';
    }

    public static function output()
    {
        return 'false';
    }
}