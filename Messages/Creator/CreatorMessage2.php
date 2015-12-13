<?php

/* The original message
0
*/


class Message2 implements MessageInterface
{
    public static function name()
    {
        return 'Message2';
    }

    public static function input()
    {
        return '0';
    }

    public static function stored()
    {
        return '0';
    }

    public static function output()
    {
        return '0';
    }
}