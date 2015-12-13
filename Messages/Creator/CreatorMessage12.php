<?php

/* The original message
[b][i][u]Bold, italics, underline[/u][/i][/b]
*/


class Message12 implements MessageInterface
{
    public static function name()
    {
        return 'Message12';
    }

    public static function input()
    {
        return '[b][i][u]Bold, italics, underline[/u][/i][/b]';
    }

    public static function stored()
    {
        return '[b][i][u]Bold, italics, underline[/u][/i][/b]';
    }

    public static function output()
    {
        return '<strong class=\"bbc_strong\"><em><span class=\"bbc_u\">Bold, italics, underline</span></em></strong>';
    }
}