<?php

/* The original message
Breaker
breaker
1
9
*/


class Message7 implements MessageInterface
{
    public static function name()
    {
        return 'Message7';
    }

    public static function input()
    {
        return 'Breaker
breaker
1
9';
    }

    public static function stored()
    {
        return 'Breaker<br />breaker<br />1<br />9';
    }

    public static function output()
    {
        return 'Breaker<br />breaker<br />1<br />9';
    }
}