<?php

/* The original message
[b]This statement is bold[/b]
*/


class Message29 implements MessageInterface
{
    public static function name()
    {
        return 'Message29';
    }

    public static function input()
    {
        return '[b]This statement is bold[/b]';
    }

    public static function stored()
    {
        return '[b]This statement is bold[/b]';
    }

    public static function output()
    {
        return '<strong class=\"bbc_strong\">This statement is bold</strong>';
    }
}