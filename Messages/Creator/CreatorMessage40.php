<?php

/* The original message
[list][li]short list[/li][/list]
*/


class Message40 implements MessageInterface
{
    public static function name()
    {
        return 'Message40';
    }

    public static function input()
    {
        return '[list][li]short list[/li][/list]';
    }

    public static function stored()
    {
        return '[list][li]short list[/li][/list]';
    }

    public static function output()
    {
        return '[list][li]short list[/li][/list]';
    }
}