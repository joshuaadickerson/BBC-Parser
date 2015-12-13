<?php

/* The original message
Not ev[en] this on[/en] has bbc
*/


class Message20 implements MessageInterface
{
    public static function name()
    {
        return 'Message20';
    }

    public static function input()
    {
        return 'Not ev[en] this on[/en] has bbc';
    }

    public static function stored()
    {
        return 'Not ev[en] this on[/en] has bbc';
    }

    public static function output()
    {
        return 'Not ev[en] this on[/en] has bbc';
    }
}