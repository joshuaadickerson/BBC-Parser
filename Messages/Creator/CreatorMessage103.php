<?php

/* The original message
[tt]Teletype Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec elit. Fusce eget enim. Nullam tellus felis, sodales nec, sodales ac, commodo eu, ante.[/tt]
*/


class Message103 implements MessageInterface
{
    public static function name()
    {
        return 'Message103';
    }

    public static function input()
    {
        return '[tt]Teletype Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec elit. Fusce eget enim. Nullam tellus felis, sodales nec, sodales ac, commodo eu, ante.[/tt]';
    }

    public static function stored()
    {
        return '[tt]Teletype Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec elit. Fusce eget enim. Nullam tellus felis, sodales nec, sodales ac, commodo eu, ante.[/tt]';
    }

    public static function output()
    {
        return '<span class=\"bbc_tt\">Teletype Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec elit. Fusce eget enim. Nullam tellus felis, sodales nec, sodales ac, commodo eu, ante.</span>';
    }
}