<?php

/* The original message
hello world
*/


class Message5 implements MessageInterface
{
    public static function name()
    {
        return 'Message5';
    }

    public static function input()
    {
        return 'hello world';
    }

    public static function stored()
    {
        return 'hello world';
    }

    public static function output()
    {
        return 'hello world';
    }
}