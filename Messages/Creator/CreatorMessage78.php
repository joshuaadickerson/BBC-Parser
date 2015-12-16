<?php

/* The original message
[iurl=http://www.google.com]www.bing.com[/iurl]
*/


class Message78 implements MessageInterface
{
    public static function name()
    {
        return 'Message78';
    }

    public static function input()
    {
        return '[iurl=http://www.google.com]www.bing.com[/iurl]';
    }

    public static function stored()
    {
        return '[iurl=http://www.google.com]www.bing.com[/iurl]';
    }

    public static function output()
    {
        return '<a href=\"http://www.google.com\" class=\"bbc_link\">www.bing.com</a>';
    }
}