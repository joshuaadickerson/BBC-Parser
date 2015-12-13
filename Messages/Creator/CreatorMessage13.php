<?php

/* The original message
Super[sup]script[/sup]
*/


class Message13 implements MessageInterface
{
    public static function name()
    {
        return 'Message13';
    }

    public static function input()
    {
        return 'Super[sup]script[/sup]';
    }

    public static function stored()
    {
        return 'Super[sup]script[/sup]';
    }

    public static function output()
    {
        return 'Super<sup>script</sup>';
    }
}