<?php

/* The original message
[color=red]red[/color][color=green]green[/color][color=blue]blue[/color]
*/


class Message83 implements MessageInterface
{
    public static function name()
    {
        return 'Message83';
    }

    public static function input()
    {
        return '[color=red]red[/color][color=green]green[/color][color=blue]blue[/color]';
    }

    public static function stored()
    {
        return '[color=red]red[/color][color=green]green[/color][color=blue]blue[/color]';
    }

    public static function output()
    {
        return '[color=red]red[/color][color=green]green[/color][color=blue]blue[/color]';
    }
}