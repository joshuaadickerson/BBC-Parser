<?php

/* The original message
[url=http://www.yahoo.com]another URL[/url] in it![/url]
*/


class Message80 implements MessageInterface
{
    public static function name()
    {
        return 'Message80';
    }

    public static function input()
    {
        return '[url=http://www.yahoo.com]another URL[/url] in it![/url]';
    }

    public static function stored()
    {
        return '[url=http://www.yahoo.com]another URL[/url] in it![/url]';
    }

    public static function output()
    {
        return '[url=http://www.yahoo.com]another URL[/url] in it![/url]';
    }
}