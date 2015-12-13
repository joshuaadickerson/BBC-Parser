<?php

/* The original message
[nobbc][b]please do not parse this[/b][/nobbc]
*/


class Message36 implements MessageInterface
{
    public static function name()
    {
        return 'Message36';
    }

    public static function input()
    {
        return '[nobbc][b]please do not parse this[/b][/nobbc]';
    }

    public static function stored()
    {
        return '[nobbc]&#91;b&#93;please do not parse this&#91;/b&#93;[/nobbc]';
    }

    public static function output()
    {
        return '&#91;b&#93;please do not parse this&#91;/b&#93;';
    }
}