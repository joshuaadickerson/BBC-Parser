<?php

/* The original message
This message doesn\'t actually have [ bbc
*/


class Message17 implements MessageInterface
{
    public static function name()
    {
        return 'Message17';
    }

    public static function input()
    {
        return 'This message doesn\'t actually have [ bbc';
    }

    public static function stored()
    {
        return 'This message doesn\'t actually have [ bbc';
    }

    public static function output()
    {
        return 'This message doesn\'t actually have [ bbc';
    }
}