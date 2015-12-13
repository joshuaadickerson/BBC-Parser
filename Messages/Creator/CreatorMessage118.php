<?php

/* The original message
[u][i]Why do you do this to yourself?[/u][/i]
*/


class Message118 implements MessageInterface
{
    public static function name()
    {
        return 'Message118';
    }

    public static function input()
    {
        return '[u][i]Why do you do this to yourself?[/u][/i]';
    }

    public static function stored()
    {
        return '[u][i]Why do you do this to yourself?[/u][/i]';
    }

    public static function output()
    {
        return '<span class=\"bbc_u\"><em>Why do you do this to yourself?</em></span>[/i]';
    }
}