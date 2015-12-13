<?php

/* The original message
:)
*/


class Message24 implements MessageInterface
{
    public static function name()
    {
        return 'Message24';
    }

    public static function input()
    {
        return ':)';
    }

    public static function stored()
    {
        return ':)';
    }

    public static function output()
    {
        return '<img src=\"//smiley.gif\" alt=\"&#58;&#41;\" title=\"\" class=\"smiley\" />';
    }
}