<?php

/* The original message
[img alt=&quot;My image&quot; height=&quot;100&quot; width=&quot;100&quot;]http://www.google.com/img.png[/img]
*/


class Message97 implements MessageInterface
{
    public static function name()
    {
        return 'Message97';
    }

    public static function input()
    {
        return '[img alt=&quot;My image&quot; height=&quot;100&quot; width=&quot;100&quot;]http://www.google.com/img.png[/img]';
    }

    public static function stored()
    {
        return '[img alt=&quot;My image&quot; height=&quot;100&quot; width=&quot;100&quot;]http://www.google.com/img.png[/img]';
    }

    public static function output()
    {
        return '<img src=\"http://www.google.com/img.png\" alt=\"&quot;My image&quot; height=&quot;100&quot; width=&quot;100&quot;\" style=\"\" class=\"bbc_img resized\" />';
    }
}