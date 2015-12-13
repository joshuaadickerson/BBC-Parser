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
        return '<div class=\"quoteheader\">: <a href=\"http://www.google.com\" class=\"bbc_link\" target=\"_blank\">http://www.google.com</a>]</div><blockquote class=\"bbc_standard_quote\">a link in the quote? uhhh okay</blockquote>';
    }
}