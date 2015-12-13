<?php

/* The original message
[table][tr][td]let me see[/td][td][table][tr][td]if[/td][td]I[/td][/tr][tr][td]can[/td][td]break[/td][/tr][tr][td]the[/td][td]internet[/td][/td][/tr][/table]
*/


class Message46 implements MessageInterface
{
    public static function name()
    {
        return 'Message46';
    }

    public static function input()
    {
        return '[table][tr][td]let me see[/td][td][table][tr][td]if[/td][td]I[/td][/tr][tr][td]can[/td][td]break[/td][/tr][tr][td]the[/td][td]internet[/td][/td][/tr][/table]';
    }

    public static function stored()
    {
        return '[table][tr][td]let me see[/td][td]if[/td][td]I[/td][/tr][tr][td]can[/td][td]break[/td][/tr][tr][td]the[/td][td]internet[/td][/tr][/table]';
    }

    public static function output()
    {
        return '<div class=\"bbc_table_container\"><table class=\"bbc_table\"><tr><td>let me see</td><td>if</td><td>I</td></tr><tr><td>can</td><td>break</td></tr><tr><td>the</td><td>internet</td></tr></table></div>';
    }
}