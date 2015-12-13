<?php

/* The original message
[img width=43 alt=&quot;google&quot; height=50]http://www.google.com/intl/en_ALL/images/srpr/logo1w.png[/img]
*/


class Message50 implements MessageInterface
{
    public static function name()
    {
        return 'Message50';
    }

    public static function input()
    {
        return '[img width=43 alt=&quot;google&quot; height=50]http://www.google.com/intl/en_ALL/images/srpr/logo1w.png[/img]';
    }

    public static function stored()
    {
        return '[img width=43 alt=&quot;google&quot; height=50]http://www.google.com/intl/en_ALL/images/srpr/logo1w.png[/img]';
    }

    public static function output()
    {
        return '[img width=43 alt=&quot;google&quot; height=50]http://www.google.com/intl/en_ALL/images/srpr/logo1w.png[/img]';
    }
}