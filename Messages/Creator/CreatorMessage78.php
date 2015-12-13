<?php

/* The original message
[url=http://www.google.com]iam@batman.net[/url]
*/


class Message78 implements MessageInterface
{
    public static function name()
    {
        return 'Message78';
    }

    public static function input()
    {
        return '[url=http://www.google.com]iam@batman.net[/url]';
    }

    public static function stored()
    {
        return '[url=http://www.google.com]iam@batman.net[/url]';
    }

    public static function output()
    {
        return '<a href=\"http://www.google.com\" class=\"bbc_link\" target=\"_blank\">iam@batman.net</a>';
    }
}