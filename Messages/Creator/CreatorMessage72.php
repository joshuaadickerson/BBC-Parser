<?php

/* The original message
[url=https://www.google.com]you@mailed.it[/url]
*/


class Message72 implements MessageInterface
{
    public static function name()
    {
        return 'Message72';
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
        return '[url=https://www.google.com]you@mailed.it[/url]';
    }
}