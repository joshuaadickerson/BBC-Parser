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
        return '[b][i][u]Bold, italics, underline[/u][/i][/b]';
    }
}