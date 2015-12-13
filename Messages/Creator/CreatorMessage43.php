<?php

/* The original message
[list][li]I[/li][li]feel[list][li]like[/list][li]Santa[/li][/list]
*/


class Message43 implements MessageInterface
{
    public static function name()
    {
        return 'Message43';
    }

    public static function input()
    {
        return '[list][li]I[/li][li]feel[list][li]like[/list][li]Santa[/li][/list]';
    }

    public static function stored()
    {
        return '[list][li]I[/li][li]feel[list][li]like[/li][/list][li]Santa[/li][/list]';
    }

    public static function output()
    {
        return '[list][li]I[/li][li]feel[list][li]like[/li][/list][li]Santa[/li][/list]';
    }
}