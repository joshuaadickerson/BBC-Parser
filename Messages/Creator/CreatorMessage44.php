<?php

/* The original message
[list type=decimal][li]simple[/li][li]list[/li][/list]
*/


class Message44 implements MessageInterface
{
    public static function name()
    {
        return 'Message44';
    }

    public static function input()
    {
        return '[list type=decimal][li]simple[/li][li]list[/li][/list]';
    }

    public static function stored()
    {
        return '[list type=decimal][li]simple[/li][li]list[/li][/list]';
    }

    public static function output()
    {
        return '[list type=decimal][li]simple[/li][li]list[/li][/list]';
    }
}