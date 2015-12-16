<?php

/* The original message
[url=http://www.google.com]iam@batman.net[/url]
*/


class Message80 implements MessageInterface
{
    public static function name()
    {
        return 'Message80';
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