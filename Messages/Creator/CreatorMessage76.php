<?php

/* The original message
[iurl=http://www.google.com]www.bing.com[/iurl]
*/


class Message76 implements MessageInterface
{
    public static function name()
    {
        return 'Message76';
    }

    public static function input()
    {
        return '[iurl=http://www.google.com]www.bing.com[/iurl]';
    }

    public static function stored()
    {
        return '[iurl=http://www.google.com]www.bing.com[/iurl]';
    }

    public static function output()
    {
        return '[iurl=http://www.google.com]www.bing.com[/iurl]';
    }
}