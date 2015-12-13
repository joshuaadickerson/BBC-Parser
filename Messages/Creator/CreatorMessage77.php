<?php

/* The original message
[email=jack@theripper.com]www.bing.com[/email]
*/


class Message77 implements MessageInterface
{
    public static function name()
    {
        return 'Message77';
    }

    public static function input()
    {
        return '[email=jack@theripper.com]www.bing.com[/email]';
    }

    public static function stored()
    {
        return '[email=jack@theripper.com]www.bing.com[/email]';
    }

    public static function output()
    {
        return '[email=jack@theripper.com]www.bing.com[/email]';
    }
}