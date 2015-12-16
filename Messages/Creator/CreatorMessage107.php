<?php

/* The original message
[left]Left Curabitur tincidunt, lacus eget iaculis tincidunt, elit libero iaculis arcu, eleifend condimentum sem est quis dolor. Curabitur sed tellus. Donec id dolor.[/left]
*/


class Message107 implements MessageInterface
{
    public static function name()
    {
        return 'Message107';
    }

    public static function input()
    {
        return '[left]Left Curabitur tincidunt, lacus eget iaculis tincidunt, elit libero iaculis arcu, eleifend condimentum sem est quis dolor. Curabitur sed tellus. Donec id dolor.[/left]';
    }

    public static function stored()
    {
        return '[left]Left Curabitur tincidunt, lacus eget iaculis tincidunt, elit libero iaculis arcu, eleifend condimentum sem est quis dolor. Curabitur sed tellus. Donec id dolor.[/left]';
    }

    public static function output()
    {
        return '<div style=\"text-align: left;\">Left Curabitur tincidunt, lacus eget iaculis tincidunt, elit libero iaculis arcu, eleifend condimentum sem est quis dolor. Curabitur sed tellus. Donec id dolor.</div>';
    }
}