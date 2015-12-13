<?php

/* The original message
[pre]Pre .. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec elit. Fusce eget enim. Nullam tellus felis, sodales nec, sodales ac, commodo eu, ante.[/pre]
*/


class Message106 implements MessageInterface
{
    public static function name()
    {
        return 'Message106';
    }

    public static function input()
    {
        return '[pre]Pre .. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec elit. Fusce eget enim. Nullam tellus felis, sodales nec, sodales ac, commodo eu, ante.[/pre]';
    }

    public static function stored()
    {
        return '[pre]Pre .. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec elit. Fusce eget enim. Nullam tellus felis, sodales nec, sodales ac, commodo eu, ante.[/pre]';
    }

    public static function output()
    {
        return '[pre]Pre .. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec elit. Fusce eget enim. Nullam tellus felis, sodales nec, sodales ac, commodo eu, ante.[/pre]';
    }
}