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
        return '<a href=\"http://www.google.com\" class=\"bbc_link\" target=\"_blank\">http://www.google.com</a>';
    }
}