<?php

/* The original message
[url=https://www.google.com]www.google.com[/url]
*/


class Message71 implements MessageInterface
{
    public static function name()
    {
        return 'Message71';
    }

    public static function input()
    {
        return '[url=https://www.google.com]www.google.com[/url]';
    }

    public static function stored()
    {
        return '[url=https://www.google.com]www.google.com[/url]';
    }

    public static function output()
    {
        return '<a href=\"https://www.google.com\" class=\"bbc_link\" target=\"_blank\">www.google.com</a>';
    }
}