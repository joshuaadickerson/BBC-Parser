<?php

/* The original message
http://www.ñchan.org
*/


class Message128 implements MessageInterface
{
    public static function name()
    {
        return 'Message128';
    }

    public static function input()
    {
        return 'http://www.ñchan.org';
    }

    public static function stored()
    {
        return 'http://www.ñchan.org';
    }

    public static function output()
    {
        return 'http://www.ñchan.org';
    }
}