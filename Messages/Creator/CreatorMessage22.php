<?php

/* The original message
This one is sneaky: [/] [ /] [  /] [   /]
*/


class Message22 implements MessageInterface
{
    public static function name()
    {
        return 'Message22';
    }

    public static function input()
    {
        return 'This one is sneaky: [/] [ /] [  /] [   /]';
    }

    public static function stored()
    {
        return 'This one is sneaky: [/] [ /] [&nbsp; /] [&nbsp;  /]';
    }

    public static function output()
    {
        return 'This one is sneaky: [/] [ /] [&nbsp; /] [&nbsp;  /]';
    }
}