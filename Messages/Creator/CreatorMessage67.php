<?php

/* The original message
http://www.cool.guy/linked?no&8)
*/


class Message67 implements MessageInterface
{
    public static function name()
    {
        return 'Message67';
    }

    public static function input()
    {
        return 'http://www.cool.guy/linked?no&8)';
    }

    public static function stored()
    {
        return 'http://www.cool.guy/linked?no&8)';
    }

    public static function output()
    {
        return '<a href=\"http://www.cool.guy/linked?no&8\" class=\"bbc_link\" target=\"_blank\">http://www.cool.guy/linked?no&8</a>)';
    }
}