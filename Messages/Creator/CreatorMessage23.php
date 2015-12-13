<?php

/* The original message
 :) 
*/


class Message23 implements MessageInterface
{
    public static function name()
    {
        return 'Message23';
    }

    public static function input()
    {
        return ' :) ';
    }

    public static function stored()
    {
        return ' :) ';
    }

    public static function output()
    {
        return '&nbsp;<img src=\"//smiley.gif\" alt=\"&#58;&#41;\" title=\"\" class=\"smiley\" /> ';
    }
}