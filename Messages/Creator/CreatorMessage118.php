<?php

/* The original message
&lt;b&gt;Bold&lt;/b&gt;&lt;i&gt;italics&lt;/i&gt;
*/


class Message118 implements MessageInterface
{
    public static function name()
    {
        return 'Message118';
    }

    public static function input()
    {
        return '&lt;b&gt;Bold&lt;/b&gt;&lt;i&gt;italics&lt;/i&gt;';
    }

    public static function stored()
    {
        return '&lt;b&gt;Bold&lt;/b&gt;&lt;i&gt;italics&lt;/i&gt;';
    }

    public static function output()
    {
        return '&lt;b&gt;Bold&lt;/b&gt;&lt;i&gt;italics&lt;/i&gt;';
    }
}