<?php

/* The original message
[table][tr][td]remember[/td][td]frontpage?[/td][/tr][/table]
*/


class Message45 implements MessageInterface
{
    public static function name()
    {
        return 'Message45';
    }

    public static function input()
    {
        return '[table][tr][td]remember[/td][td]frontpage?[/td][/tr][/table]';
    }

    public static function stored()
    {
        return '[table][tr][td]remember[/td][td]frontpage?[/td][/tr][/table]';
    }

    public static function output()
    {
        return '[table][tr][td]remember[/td][td]frontpage?[/td][/tr][/table]';
    }
}