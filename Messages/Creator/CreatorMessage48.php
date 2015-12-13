<?php

/* The original message
[img width=500]http://www.google.com/intl/en_ALL/images/srpr/logo1w.png[/img]
*/


class Message48 implements MessageInterface
{
    public static function name()
    {
        return 'Message48';
    }

    public static function input()
    {
        return '[img width=500]http://www.google.com/intl/en_ALL/images/srpr/logo1w.png[/img]';
    }

    public static function stored()
    {
        return '[img width=500]http://www.google.com/intl/en_ALL/images/srpr/logo1w.png[/img]';
    }

    public static function output()
    {
        return '<img src=\"http://www.google.com/intl/en_ALL/images/srpr/logo1w.png\" alt=\"\" style=\"width:100%;max-width:500px;\" class=\"bbc_img resized\" />';
    }
}