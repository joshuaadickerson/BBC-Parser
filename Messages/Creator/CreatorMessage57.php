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
        return '[quote link=topic=14764.msg87204#msg87204 author=Mutt & Jeff date=1329175080]I started a band called 999 Megabytes. We don&apos;t have a gig yet.[/quote]';
    }
}