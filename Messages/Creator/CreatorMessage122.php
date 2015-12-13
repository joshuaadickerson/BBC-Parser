<?php

/* The original message
[list][li]quick[li]no[li]time[li]for[li]closing[li]tags[/list]
*/


class Message122 implements MessageInterface
{
    public static function name()
    {
        return 'Message122';
    }

    public static function input()
    {
        return '[list][li]quick[li]no[li]time[li]for[li]closing[li]tags[/list]';
    }

    public static function stored()
    {
        return '[list][li]quick[/li][li]no[list][li]time[/li][li]for[list][li]closing[/li][/list][li]tags[/li][/list]';
    }

    public static function output()
    {
        return '<ul class=\"bbc_list\"><li>quick</li><li>no<ul class=\"bbc_list\"><li>time</li><li>for<ul class=\"bbc_list\"><li>closing</li></ul>[li]tags</li></ul></li></ul>';
    }
}