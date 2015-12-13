<?php

/* The original message
Smile :)
*/


class Message25 implements MessageInterface
{
    public static function name()
    {
        return 'Message25';
    }

    public static function input()
    {
        return 'Smile :)';
    }

    public static function stored()
    {
        return 'Smile :)';
    }

    public static function output()
    {
        return 'Smile <img src=\"//smiley.gif\" alt=\"&#58;&#41;\" title=\"\" class=\"smiley\" />';
    }
}