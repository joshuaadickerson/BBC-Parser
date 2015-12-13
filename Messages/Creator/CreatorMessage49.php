<?php

/* The original message
[img height=50]http://www.google.com/intl/en_ALL/images/srpr/logo1w.png[/img]
*/


class Message49 implements MessageInterface
{
    public static function name()
    {
        return 'Message49';
    }

    public static function input()
    {
        return '[img height=50]http://www.google.com/intl/en_ALL/images/srpr/logo1w.png[/img]';
    }

    public static function stored()
    {
        return '[img height=50]http://www.google.com/intl/en_ALL/images/srpr/logo1w.png[/img]';
    }

    public static function output()
    {
        return '<img src=\"http://www.google.com/intl/en_ALL/images/srpr/logo1w.png\" alt=\"\" style=\"max-height:50px;\" class=\"bbc_img resized\" />';
    }
}