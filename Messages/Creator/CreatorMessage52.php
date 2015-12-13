<?php

/* The original message
[quote=&quot;Edsger Dijkstra&quot;]If debugging is the process of removing software bugs, then programming must be the process of putting them in[/quote]
*/


class Message52 implements MessageInterface
{
    public static function name()
    {
        return 'Message52';
    }

    public static function input()
    {
        return '[quote=&quot;Edsger Dijkstra&quot;]If debugging is the process of removing software bugs, then programming must be the process of putting them in[/quote]';
    }

    public static function stored()
    {
        return '[quote=&quot;Edsger Dijkstra&quot;]If debugging is the process of removing software bugs, then programming must be the process of putting them in[/quote]';
    }

    public static function output()
    {
        return '[quote=&quot;Edsger Dijkstra&quot;]If debugging is the process of removing software bugs, then programming must be the process of putting them in[/quote]';
    }
}