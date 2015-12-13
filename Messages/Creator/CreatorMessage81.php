<?php

/* The original message
Testing autolink then a url: www.google.com [url=this no worky] [b]a tag to close it [/b] [/url] just to make sure
*/


class Message81 implements MessageInterface
{
    public static function name()
    {
        return 'Message81';
    }

    public static function input()
    {
        return 'Testing autolink then a url: www.google.com [url=this no worky] [b]a tag to close it [/b] [/url] just to make sure';
    }

    public static function stored()
    {
        return 'Testing autolink then a url: www.google.com [url=http://this no worky] [b]a tag to close it [/b] [/url] just to make sure';
    }

    public static function output()
    {
        return 'Testing autolink then a url: www.google.com [url=http://this no worky] [b]a tag to close it [/b] [/url] just to make sure';
    }
}