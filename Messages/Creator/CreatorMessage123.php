<?php

/* The original message
[quote this=should not=work but=maybe it=will]only a test will tell[/quote]
*/


class Message123 implements MessageInterface
{
    public static function name()
    {
        return 'Message123';
    }

    public static function input()
    {
        return '[quote this=should not=work but=maybe it=will]only a test will tell[/quote]';
    }

    public static function stored()
    {
        return '[quote this=should not=work but=maybe it=will]only a test will tell[/quote]';
    }

    public static function output()
    {
        return '[quote this=should not=work but=maybe it=will]only a test will tell[/quote]';
    }
}