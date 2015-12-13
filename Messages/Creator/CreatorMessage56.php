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
        return '[quote author=Mutt & Jeff link=topic=14764.msg87204#msg87204 date=1329175080]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/quote]';
    }
}