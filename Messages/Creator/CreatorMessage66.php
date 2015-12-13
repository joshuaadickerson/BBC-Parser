<?php

/* The original message
me@email.com
*/


class Message66 implements MessageInterface
{
    public static function name()
    {
        return 'Message66';
    }

    public static function input()
    {
        return 'me@email.com';
    }

    public static function stored()
    {
        return 'me@email.com';
    }

    public static function output()
    {
        return 'me@email.com';
    }
}