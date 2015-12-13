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
        return '[*]Ahoy!<br />[*]Me[@]Matey<br />[+]Shiver<br />[x]Me<br />[#]Timbers<br />[!]<br />[*]I[*]dunno[*]why';
    }
}