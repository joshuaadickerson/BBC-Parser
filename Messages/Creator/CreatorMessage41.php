<?php

/* The original message
[list][li]short list[/li][li]to do[/li][li]growing[/li][/list]
*/


class Message41 implements MessageInterface
{
    public static function name()
    {
        return 'Message41';
    }

    public static function input()
    {
        return '[list][li]short list[/li][li]to do[/li][li]growing[/li][/list]';
    }

    public static function stored()
    {
        return '[list][li]short list[/li][li]to do[/li][li]growing[/li][/list]';
    }

    public static function output()
    {
        return '<ul class=\"bbc_list\"><li>short list</li><li>to do</li><li>growing</li></ul>';
    }
}