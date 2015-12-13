<?php

/* The original message
[url=http://www.google.com]www.bing.com[/url]
*/


class Message75 implements MessageInterface
{
    public static function name()
    {
        return 'Message75';
    }

    public static function input()
    {
        return '[url=http://www.google.com]www.bing.com[/url]';
    }

    public static function stored()
    {
        return '[url=http://www.google.com]www.bing.com[/url]';
    }

    public static function output()
    {
        return '[url=http://www.google.com]www.bing.com[/url]';
    }
}