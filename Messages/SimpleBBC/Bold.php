<?php

class Bold implements MessageInterface
{
    public static function name()
    {
        return 'Basic bold';
    }

    public static function input()
    {
        return '[b]Bold[/b]';
    }

    public static function stored()
    {
        return '[b]Bold[/b]';
    }

    public static function output()
    {
        return '<b>Bold</b>';
    }
}