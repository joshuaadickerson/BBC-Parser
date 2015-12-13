<?php

/* The original message
You\\n[code=me]\\nget 1\\n[/code]and [code]\\nyou get one[/code]
*/


class Message109 implements MessageInterface
{
    public static function name()
    {
        return 'Message109';
    }

    public static function input()
    {
        return 'You\\n[code=me]\\nget 1\\n[/code]and [code]\\nyou get one[/code]';
    }

    public static function stored()
    {
        return 'You\\n[code=me]\\nget 1\\n[/code]and [code]\\nyou get one[/code]';
    }

    public static function output()
    {
        return 'You\\n[code=me]\\nget 1\\n[/code]and [code]\\nyou get one[/code]';
    }
}