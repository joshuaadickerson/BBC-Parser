<?php

/* The original message
[url=http://www.google.com]Google[/url]. Basic unparsed equals
*/


class Message30 implements MessageInterface
{
    public static function name()
    {
        return 'Message30';
    }

    public static function input()
    {
        return '[url=http://www.google.com]Google[/url]. Basic unparsed equals';
    }

    public static function stored()
    {
        return '[url=http://www.google.com]Google[/url]. Basic unparsed equals';
    }

    public static function output()
    {
        return '<a href=\"http://www.google.com\" class=\"bbc_link\" target=\"_blank\">Google</a>. Basic unparsed equals';
    }
}