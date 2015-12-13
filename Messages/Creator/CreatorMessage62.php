<?php

/* The original message
http://www.google.com
*/


class Message62 implements MessageInterface
{
    public static function name()
    {
        return 'Message62';
    }

    public static function input()
    {
        return 'http://www.google.com';
    }

    public static function stored()
    {
        return 'http://www.google.com';
    }

    public static function output()
    {
        return 'http://www.google.com';
    }
}