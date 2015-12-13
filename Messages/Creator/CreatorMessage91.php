<?php

/* The original message
[font=Arial]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/font]
*/


class Message91 implements MessageInterface
{
    public static function name()
    {
        return 'Message91';
    }

    public static function input()
    {
        return '[font=Arial]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/font]';
    }

    public static function stored()
    {
        return '[font=Arial]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/font]';
    }

    public static function output()
    {
        return '[font=Arial]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/font]';
    }
}