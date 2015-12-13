<?php

/* The original message
[table][tr][th][/th][/tr][tr][td][/td][/tr][tr][td][/td][/tr][/table]
*/


class Message47 implements MessageInterface
{
    public static function name()
    {
        return 'Message47';
    }

    public static function input()
    {
        return '[table][tr][th][/th][/tr][tr][td][/td][/tr][tr][td][/td][/tr][/table]';
    }

    public static function stored()
    {
        return '[table][tr][th][/th][/tr][tr][td][/td][/tr][tr][td][/td][/tr][/table]';
    }

    public static function output()
    {
        return '[table][tr][th][/th][/tr][tr][td][/td][/tr][tr][td][/td][/tr][/table]';
    }
}