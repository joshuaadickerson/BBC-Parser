<?php

/* The original message
[list][li]quick[li]no[li]time[li]for[li]closing[li]tags[/list]
*/


class Message42 implements MessageInterface
{
    public static function name()
    {
        return 'Message42';
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
        return '[list][li]quick[/li][li]no[list][li]time[/li][li]for[list][li]closing[/li][/list][li]tags[/li][/list]';
    }
}