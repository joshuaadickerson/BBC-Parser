<?php

/* The original message
[quote link=topic=14764.msg87204#msg87204 author=Mutt & Jeff date=1329175080]I started a band called 999 Megabytes. We don&apos;t have a gig yet.[/quote]
*/


class Message57 implements MessageInterface
{
    public static function name()
    {
        return 'Message57';
    }

    public static function input()
    {
        return '[quote link=topic=14764.msg87204#msg87204 author=Mutt & Jeff date=1329175080]I started a band called 999 Megabytes. We don&apos;t have a gig yet.[/quote]';
    }

    public static function stored()
    {
        return '[quote link=topic=14764.msg87204#msg87204 author=Mutt & Jeff date=1329175080]I started a band called 999 Megabytes. We don&apos;t have a gig yet.[/quote]';
    }

    public static function output()
    {
        return '<div class=\"quoteheader\"><a href=\"?topic=14764.msg87204#msg87204\">: Mutt & Jeff&nbsp; <time title=\"February 13, 2012, 11:18:00 PM\" datetime=\"2012-02-13 23:18\" data-timestamp=\"1329175080\">February 13, 2012, 11:18:00 PM</time></a></div><blockquote class=\"bbc_standard_quote\">I started a band called 999 Megabytes. We don&apos;t have a gig yet.</blockquote>';
    }
}