<?php

/* The original message
Everyone\\n[code]\\ngets a line\\n[/code]\\nbreak
*/


class Message108 implements MessageInterface
{
    public static function name()
    {
        return 'Message108';
    }

    public static function input()
    {
        return 'Everyone\\n[code]\\ngets a line\\n[/code]\\nbreak';
    }

    public static function stored()
    {
        return 'Everyone\\n[code]\\ngets a line\\n[/code]\\nbreak';
    }

    public static function output()
    {
        return 'Everyone\\n[code]\\ngets a line\\n[/code]\\nbreak';
    }
}