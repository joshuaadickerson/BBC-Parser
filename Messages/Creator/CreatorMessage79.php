<?php

/* The original message
[email=jack@theripper.com]www.bing.com[/email]
*/


class Message79 implements MessageInterface
{
    public static function name()
    {
        return 'Message79';
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
        return '<a href=\"mailto:jack@theripper.com\" class=\"bbc_email\">www.bing.com</a>';
    }
}