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
        return '[url=http://www.google.com]Google[/url]. Basic unparsed equals';
    }
}