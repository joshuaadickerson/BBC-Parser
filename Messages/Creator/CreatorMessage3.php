<?php

/* The original message
array()
*/


class Message3 implements MessageInterface
{
    public static function name()
    {
        return 'Message3';
    }

    public static function input()
    {
        return 'array()';
    }

    public static function stored()
    {
        return 'array()';
    }

    public static function output()
    {
        return 'array()';
    }
}