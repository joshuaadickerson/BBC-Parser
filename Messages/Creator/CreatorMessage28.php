<?php

/* The original message
and the good old whatzup??? which should not show
*/


class Message28 implements MessageInterface
{
    public static function name()
    {
        return 'Message28';
    }

    public static function input()
    {
        return 'and the good old whatzup??? which should not show';
    }

    public static function stored()
    {
        return 'and the good old whatzup??? which should not show';
    }

    public static function output()
    {
        return 'and the good old whatzup??? which should not show';
    }
}