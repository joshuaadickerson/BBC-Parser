<?php

/* The original message
[url=https://www.google.com]http://www.google.com/404[/url]
*/


class Message70 implements MessageInterface
{
    public static function name()
    {
        return 'Message70';
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
        return '[url=https://www.google.com]http://www.google.com/404[/url]';
    }
}