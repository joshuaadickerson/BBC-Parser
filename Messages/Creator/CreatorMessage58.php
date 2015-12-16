<?php

/* The original message
[quote=Joe Doe joe@email.com]Here is what Joe said.[/quote]
*/


class Message58 implements MessageInterface
{
    public static function name()
    {
        return 'Message58';
    }

    public static function input()
    {
        return '[quote=Joe Doe joe@email.com]Here is what Joe said.[/quote]';
    }

    public static function stored()
    {
        return '[quote=Joe Doe joe@email.com]Here is what Joe said.[/quote]';
    }

    public static function output()
    {
        return '<div class=\"quoteheader\">quote from: Joe Doe joe@email.com</div><blockquote class=\"bbc_standard_quote\">Here is what Joe said.</blockquote>';
    }
}