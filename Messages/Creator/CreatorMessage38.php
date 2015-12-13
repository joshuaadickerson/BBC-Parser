<?php

/* The original message
[code][/code]
e
*/


class Message38 implements MessageInterface
{
    public static function name()
    {
        return 'Message38';
    }

    public static function input()
    {
        return '[code][/code]
e';
    }

    public static function stored()
    {
        return '[code][/code]<br />e';
    }

    public static function output()
    {
        return '[code][/code]<br />e';
    }
}