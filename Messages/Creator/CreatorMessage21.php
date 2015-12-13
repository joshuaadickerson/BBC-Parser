<?php

/* The original message
do [u even] know what you talkin bout
*/


class Message21 implements MessageInterface
{
    public static function name()
    {
        return 'Message21';
    }

    public static function input()
    {
        return 'do [u even] know what you talkin bout';
    }

    public static function stored()
    {
        return 'do [u even] know what you talkin bout';
    }

    public static function output()
    {
        return 'do [u even] know what you talkin bout';
    }
}