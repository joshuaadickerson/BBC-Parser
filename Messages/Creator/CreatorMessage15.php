<?php

/* The original message
[sup]Super[/sup]-[sub]sub[/sub]-script
*/


class Message15 implements MessageInterface
{
    public static function name()
    {
        return 'Message15';
    }

    public static function input()
    {
        return '[sup]Super[/sup]-[sub]sub[/sub]-script';
    }

    public static function stored()
    {
        return '[sup]Super[/sup]-[sub]sub[/sub]-script';
    }

    public static function output()
    {
        return '<sup>Super</sup>-<sub>sub</sub>-script';
    }
}