<?php

/* The original message
www.autolink.org/?a[]=1&a[]=2;a[]=3;b[a]=6&b[b]=99
*/


class Message70 implements MessageInterface
{
    public static function name()
    {
        return 'Message70';
    }

    public static function input()
    {
        return 'www.autolink.org/?a[]=1&a[]=2;a[]=3;b[a]=6&b[b]=99';
    }

    public static function stored()
    {
        return 'www.autolink.org/?a&#91;]=1&a&#91;]=2;a&#91;]=3;b[a]=6&b[b]=99';
    }

    public static function output()
    {
        return '<a href=\"http://www.autolink.org/?a&#91;\" class=\"bbc_link\" target=\"_blank\">www.autolink.org/?a&#91;</a>]=1&a&#91;]=2;a&#91;]=3;b[a]=6&b<strong class=\"bbc_strong\">=99</strong>';
    }
}