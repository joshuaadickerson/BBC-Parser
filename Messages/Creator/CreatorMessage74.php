<?php

/* The original message
[url=http://www.elkarte.org/community/index.php [^]]ask us for assistance[/url]
*/


class Message74 implements MessageInterface
{
    public static function name()
    {
        return 'Message74';
    }

    public static function input()
    {
        return '[url=http://www.elkarte.org/community/index.php [^]]ask us for assistance[/url]';
    }

    public static function stored()
    {
        return '[url=http://www.elkarte.org/community/index.php [^]]ask us for assistance[/url]';
    }

    public static function output()
    {
        return '<a href=\"http://www.elkarte.org/community/index.php [^\" class=\"bbc_link\" target=\"_blank\">]ask us for assistance</a>';
    }
}