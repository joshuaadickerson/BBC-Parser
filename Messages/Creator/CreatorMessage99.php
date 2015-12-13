<?php

/* The original message
[quote=&quot;[url]http://www.google.com[/url]]&quot;]a link in the quote? uhhh okay[/quote]
*/


class Message99 implements MessageInterface
{
    public static function name()
    {
        return 'Message99';
    }

    public static function input()
    {
        return '[quote=&quot;[url]http://www.google.com[/url]]&quot;]a link in the quote? uhhh okay[/quote]';
    }

    public static function stored()
    {
        return '[quote=&quot;[url=http://www.google.com]http://www.google.com[/url]]&quot;]a link in the quote? uhhh okay[/quote]';
    }

    public static function output()
    {
        return '[quote=&quot;[url=http://www.google.com]http://www.google.com[/url]]&quot;]a link in the quote? uhhh okay[/quote]';
    }
}