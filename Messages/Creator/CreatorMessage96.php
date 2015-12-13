<?php

/* The original message
[img alt=MyImage height=100 width=100]http://www.google.com/img.png[/img]
*/


class Message96 implements MessageInterface
{
    public static function name()
    {
        return 'Message96';
    }

    public static function input()
    {
        return '[img alt=MyImage height=100 width=100]http://www.google.com/img.png[/img]';
    }

    public static function stored()
    {
        return '[img alt=MyImage height=100 width=100]http://www.google.com/img.png[/img]';
    }

    public static function output()
    {
        return '<img src=\"http://www.google.com/img.png\" alt=\"MyImage height=100\" style=\"width:100%;max-width:100px;\" class=\"bbc_img resized\" />';
    }
}