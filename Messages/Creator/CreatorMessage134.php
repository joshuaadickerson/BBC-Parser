<?php

/* The original message
[table][tr][td]unclosed table[/td][/tr]
*/


class Message134 implements MessageInterface
{
    public static function name()
    {
        return 'Message134';
    }

    public static function input()
    {
        return '[table][tr][td]unclosed table[/td][/tr]';
    }

    public static function stored()
    {
        return '[table][tr][td]unclosed table[/td][/tr][/table]';
    }

    public static function output()
    {
        return '<div class=\"bbc_table_container\"><table class=\"bbc_table\"><tr><td>unclosed table</td></tr></table></div>';
    }
}