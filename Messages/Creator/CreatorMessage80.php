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
        return '<a href=\"http://www.yahoo.com\" class=\"bbc_link\" target=\"_blank\">another URL</a> in it![/url]';
    }
}