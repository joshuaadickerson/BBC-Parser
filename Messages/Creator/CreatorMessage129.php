<?php

/* The original message
[list][li]Test[/li][li]More[code]Some COde[/code][/li][/list]
*/


class Message129 implements MessageInterface
{
    public static function name()
    {
        return 'Message129';
    }

    public static function input()
    {
        return '[list][li]Test[/li][li]More[code]Some COde[/code][/li][/list]';
    }

    public static function stored()
    {
        return '[list][li]Test[/li][/list][li]More[/li][/list][code]Some COde[/code][list][/li][/list]';
    }

    public static function output()
    {
        return '[list][li]Test[/li][/list][li]More[/li][/list][code]Some COde[/code][list][/li][/list]';
    }
}