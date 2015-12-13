<?php

/* The original message
foo bar
*/


class Message6 implements MessageInterface
{
    public static function name()
    {
        return 'Message6';
    }

    public static function input()
    {
        return 'foo bar';
    }

    public static function stored()
    {
        return 'foo bar';
    }

    public static function output()
    {
        return 'foo bar';
    }
}