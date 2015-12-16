<?php

/* The original message
[url=https://www.google.com]you@mailed.it[/url]
*/


class Message73 implements MessageInterface
{
    public static function name()
    {
        return 'Message73';
    }

    public static function input()
    {
        return '[url=https://www.google.com]you@mailed.it[/url]';
    }

    public static function stored()
    {
        return '[url=https://www.google.com]you@mailed.it[/url]';
    }

    public static function output()
    {
        return '<a href=\"https://www.google.com\" class=\"bbc_link\" target=\"_blank\">you@mailed.it</a>';
    }
}