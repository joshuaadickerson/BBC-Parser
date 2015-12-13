<?php

/* The original message
[quote]test[quote]nested 1[quote]nested 2[quote]nested 3[quote]nested 4[quote]nested 5[/quote][quote]nested 4.1[/quote][/quote][quote]nested3.1[/quote][quote]nested3.2[/quote][quote]nested3.3[/quote][quote]nested3.4[/quote][/quote][/quote][/quote][/quote]
*/


class Message59 implements MessageInterface
{
    public static function name()
    {
        return 'Message59';
    }

    public static function input()
    {
        return '[quote]test[quote]nested 1[quote]nested 2[quote]nested 3[quote]nested 4[quote]nested 5[/quote][quote]nested 4.1[/quote][/quote][quote]nested3.1[/quote][quote]nested3.2[/quote][quote]nested3.3[/quote][quote]nested3.4[/quote][/quote][/quote][/quote][/quote]';
    }

    public static function stored()
    {
        return '[quote]test[quote]nested 1[quote]nested 2[quote]nested 3[quote]nested 4[quote]nested 5[/quote][quote]nested 4.1[/quote][/quote][quote]nested3.1[/quote][quote]nested3.2[/quote][quote]nested3.3[/quote][quote]nested3.4[/quote][/quote][/quote][/quote][/quote]';
    }

    public static function output()
    {
        return '[quote]test[quote]nested 1[quote]nested 2[quote]nested 3[quote]nested 4[quote]nested 5[/quote][quote]nested 4.1[/quote][/quote][quote]nested3.1[/quote][quote]nested3.2[/quote][quote]nested3.3[/quote][quote]nested3.4[/quote][/quote][/quote][/quote][/quote]';
    }
}