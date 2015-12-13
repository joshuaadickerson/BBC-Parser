<?php

/* The original message
Sub[sub]script[/sub]
*/


class Message14 implements MessageInterface
{
    public static function name()
    {
        return 'Message14';
    }

    public static function input()
    {
        return 'Sub[sub]script[/sub]';
    }

    public static function stored()
    {
        return 'Sub[sub]script[/sub]';
    }

    public static function output()
    {
        return 'Sub<sub>script</sub>';
    }
}