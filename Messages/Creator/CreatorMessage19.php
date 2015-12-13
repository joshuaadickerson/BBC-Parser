<?php

/* The original message
Nor do[es] this one
*/


class Message19 implements MessageInterface
{
    public static function name()
    {
        return 'Message19';
    }

    public static function input()
    {
        return 'Nor do[es] this one';
    }

    public static function stored()
    {
        return 'Nor do[es] this one';
    }

    public static function output()
    {
        return 'Nor do[es] this one';
    }
}