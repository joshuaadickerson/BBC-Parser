<?php

/* The original message
[url=http://www.google.com/]this url has [email=someone@someplace.org]an email[/email][/url]
*/


class Message79 implements MessageInterface
{
    public static function name()
    {
        return 'Message79';
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
        return '[url=http://www.google.com/]this url has [email=someone@someplace.org]an email[/email][/url]';
    }
}