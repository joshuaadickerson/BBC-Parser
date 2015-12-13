<?php

/* The original message
www.google.com
*/


class Message65 implements MessageInterface
{
    public static function name()
    {
        return 'Message65';
    }

    public static function input()
    {
        return 'www.google.com';
    }

    public static function stored()
    {
        return 'www.google.com';
    }

    public static function output()
    {
        return 'www.google.com';
    }
}