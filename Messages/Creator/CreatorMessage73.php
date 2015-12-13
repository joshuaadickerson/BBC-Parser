<?php

/* The original message
[url=http://www.google.com/]test www.elkarte.net test[/url]
*/


class Message73 implements MessageInterface
{
    public static function name()
    {
        return 'Message73';
    }

    public static function input()
    {
        return '[url=http://www.google.com/]test www.elkarte.net test[/url]';
    }

    public static function stored()
    {
        return '[url=http://www.google.com/]test www.elkarte.net test[/url]';
    }

    public static function output()
    {
        return '[url=http://www.google.com/]test www.elkarte.net test[/url]';
    }
}