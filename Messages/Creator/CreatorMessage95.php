<?php

/* The original message
[url=&quot;http://www.google.com&quot;]quoted url[/url]
*/


class Message95 implements MessageInterface
{
    public static function name()
    {
        return 'Message95';
    }

    public static function input()
    {
        return '[url=&quot;http://www.google.com&quot;]quoted url[/url]';
    }

    public static function stored()
    {
        return '[url=http://&quot;http://www.google.com&quot;]quoted url[/url]';
    }

    public static function output()
    {
        return '<a href=\"http://&quot;http://www.google.com&quot;\" class=\"bbc_link\" target=\"_blank\">quoted url</a>';
    }
}