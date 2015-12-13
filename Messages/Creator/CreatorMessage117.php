<?php

/* The original message
[i]lets go for italics
*/


class Message117 implements MessageInterface
{
    public static function name()
    {
        return 'Message117';
    }

    public static function input()
    {
        return '[i]lets go for italics';
    }

    public static function stored()
    {
        return '[i]lets go for italics';
    }

    public static function output()
    {
        return '<em>lets go for italics</em>';
    }
}