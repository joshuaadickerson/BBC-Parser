<?php

class SimpleList implements MessageInterface
{
    public static function name()
    {
        return 'Simple List';
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
        return '<ul class="bbc_list"><li>simple</li><li>list</li></ul>';
    }
}