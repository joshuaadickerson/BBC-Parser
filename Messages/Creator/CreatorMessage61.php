<?php

/* The original message
[*]Ahoy!
[*]Me[@]Matey
[+]Shiver
[x]Me
[#]Timbers
[!]
[*]I[*]dunno[*]why
*/


class Message61 implements MessageInterface
{
    public static function name()
    {
        return 'Message61';
    }

    public static function input()
    {
        return '[*]Ahoy!
[*]Me[@]Matey
[+]Shiver
[x]Me
[#]Timbers
[!]
[*]I[*]dunno[*]why';
    }

    public static function stored()
    {
        return '[*]Ahoy!<br />[*]Me[@]Matey<br />[+]Shiver<br />[x]Me<br />[#]Timbers<br />[!]<br />[*]I[*]dunno[*]why';
    }

    public static function output()
    {
        return '<ul style=\"list-style-type: disc\" class=\"bbc_list\"><li>Ahoy!</li><li>Me</li><li>Matey<li>Shiver</li><li>Me</li><li>Timbers</li>[!]<br /><li>I</li><li>dunno</li><li>why</li></ul>';
    }
}