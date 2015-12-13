<?php

/* The original message
Everyone
[code]
gets a line
[/code]
break
*/


class Message32 implements MessageInterface
{
    public static function name()
    {
        return 'Message32';
    }

    public static function input()
    {
        return 'Everyone
[code]
gets a line
[/code]
break';
    }

    public static function stored()
    {
        return 'Everyone<br />[code]<br />gets a line<br />[/code]<br />break';
    }

    public static function output()
    {
        return 'Everyone<br />[code]<br />gets a line<br />[/code]<br />break';
    }
}