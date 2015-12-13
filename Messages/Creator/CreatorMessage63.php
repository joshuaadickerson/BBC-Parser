<?php

/* The original message
https://google.com
*/


class Message63 implements MessageInterface
{
    public static function name()
    {
        return 'Message63';
    }

    public static function input()
    {
        return 'https://google.com';
    }

    public static function stored()
    {
        return 'https://google.com';
    }

    public static function output()
    {
        return 'https://google.com';
    }
}