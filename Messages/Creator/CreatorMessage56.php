<?php

/* The original message
[quote author=Mutt & Jeff link=topic=14764.msg87204#msg87204 date=1329175080]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/quote]
*/


class Message56 implements MessageInterface
{
    public static function name()
    {
        return 'Message56';
    }

    public static function input()
    {
        return '[quote author=Mutt & Jeff link=topic=14764.msg87204#msg87204 date=1329175080]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/quote]';
    }

    public static function stored()
    {
        return '[quote author=Mutt & Jeff link=topic=14764.msg87204#msg87204 date=1329175080]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/quote]';
    }

    public static function output()
    {
        return '<div class=\"quoteheader\"><a href=\"?topic=14764.msg87204#msg87204\">: Mutt & Jeff&nbsp; <time title=\"February 13, 2012, 11:18:00 PM\" datetime=\"2012-02-13 23:18\" data-timestamp=\"1329175080\">February 13, 2012, 11:18:00 PM</time></a></div><blockquote class=\"bbc_standard_quote\">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</blockquote>';
    }
}