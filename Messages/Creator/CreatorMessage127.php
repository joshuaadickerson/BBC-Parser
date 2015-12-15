<?php

/* The original message
www.ñchan.org
*/


class Message127 implements MessageInterface
{
    public static function name()
    {
        return 'Message127';
    }

    public static function input()
    {
        return 'www.ñchan.org';
    }

    public static function stored()
    {
        return 'www.ñchan.org';
    }

    public static function output()
    {
        return '<a href=\"http://www.ñchan.org\" class=\"bbc_link\" target=\"_blank\">www.ñchan.org</a>';
    }
}