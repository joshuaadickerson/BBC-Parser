<?php

/* The original message
You
[code=me]
get 1
[/code]and [code]
you get one[/code]
*/


class Message33 implements MessageInterface
{
    public static function name()
    {
        return 'Message33';
    }

    public static function input()
    {
        return 'You
[code=me]
get 1
[/code]and [code]
you get one[/code]';
    }

    public static function stored()
    {
        return 'You<br />[code=me]<br />get 1<br />[/code]and [code]<br />you get one[/code]';
    }

    public static function output()
    {
        return 'You<br />[code=me]<br />get 1<br />[/code]and [code]<br />you get one[/code]';
    }
}