<?php

class EmptyString implements MessageInterface
{
    public static function name()
    {
        return 'Empty string';
    }

    public static function input()
    {
        return '';
    }

    public static function stored()
    {
        return '';
    }

    public static function output()
    {
        return '';
    }
}