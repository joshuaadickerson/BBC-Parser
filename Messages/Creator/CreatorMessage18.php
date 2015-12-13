<?php

/* The original message
Neither does [] this one
*/


class Message18 implements MessageInterface
{
    public static function name()
    {
        return 'Message18';
    }

    public static function input()
    {
        return 'Neither does [] this one';
    }

    public static function stored()
    {
        return 'Neither does &#91;] this one';
    }

    public static function output()
    {
        return 'Neither does &#91;] this one';
    }
}