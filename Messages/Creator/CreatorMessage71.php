<?php

/* The original message
[url=https://www.google.com]http://www.google.com/404[/url]
*/


class Message71 implements MessageInterface
{
    public static function name()
    {
        return 'Message71';
    }

    public static function input()
    {
        return '[url=https://www.google.com]http://www.google.com/404[/url]';
    }

    public static function stored()
    {
        return '[url=https://www.google.com]http://www.google.com/404[/url]';
    }

    public static function output()
    {
        return '<a href=\"https://www.google.com\" class=\"bbc_link\" target=\"_blank\">http://www.google.com/404</a>';
    }
}