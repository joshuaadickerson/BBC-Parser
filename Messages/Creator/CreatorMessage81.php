<?php

/* The original message
[url=http://www.google.com/]this url has [email=someone@someplace.org]an email[/email][/url]
*/


class Message81 implements MessageInterface
{
    public static function name()
    {
        return 'Message81';
    }

    public static function input()
    {
        return '[url=http://www.google.com/]this url has [email=someone@someplace.org]an email[/email][/url]';
    }

    public static function stored()
    {
        return '[url=http://www.google.com/]this url has [email=someone@someplace.org]an email[/email][/url]';
    }

    public static function output()
    {
        return '<a href=\"http://www.google.com/\" class=\"bbc_link\" target=\"_blank\">this url has [email=someone@someplace.org]an email[/email]</a>';
    }
}