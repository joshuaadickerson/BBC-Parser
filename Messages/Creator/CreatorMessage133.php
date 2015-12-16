<?php

/* The original message
[b]unclosed bold
*/


class Message133 implements MessageInterface
{
    public static function name()
    {
        return 'Message133';
    }

    public static function input()
    {
        return '[b]unclosed bold';
    }

    public static function stored()
    {
        return '[b]unclosed bold';
    }

    public static function output()
    {
        return '<strong class=\"bbc_strong\">unclosed bold</strong>';
    }
}