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
        return '[img alt=MyImage height=100 width=100]http://www.google.com/img.png[/img]';
    }
}