<?php

/* The original message
http://google.de
*/


class Message64 implements MessageInterface
{
    public static function name()
    {
        return 'Message64';
    }

    public static function input()
    {
        return 'http://google.de';
    }

    public static function stored()
    {
        return 'http://google.de';
    }

    public static function output()
    {
        return '<a href=\"http://google.de\" class=\"bbc_link\" target=\"_blank\">http://google.de</a>';
    }
}