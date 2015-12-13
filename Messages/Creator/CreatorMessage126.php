<?php

/* The original message
[url]www.ñchan.org[/url]
*/


class Message126 implements MessageInterface
{
    public static function name()
    {
        return 'Message126';
    }

    public static function input()
    {
        return '[url]www.ñchan.org[/url]';
    }

    public static function stored()
    {
        return '[url=http://www.ñchan.org]www.ñchan.org[/url]';
    }

    public static function output()
    {
        return '[url=http://www.ñchan.org]www.ñchan.org[/url]';
    }
}