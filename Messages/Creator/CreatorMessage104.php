<?php

/* The original message
[right]Right Curabitur tincidunt, lacus eget iaculis tincidunt, elit libero iaculis arcu, eleifend condimentum sem est quis dolor. Curabitur sed tellus. Donec id dolor.[/right]
*/


class Message104 implements MessageInterface
{
    public static function name()
    {
        return 'Message104';
    }

    public static function input()
    {
        return '[right]Right Curabitur tincidunt, lacus eget iaculis tincidunt, elit libero iaculis arcu, eleifend condimentum sem est quis dolor. Curabitur sed tellus. Donec id dolor.[/right]';
    }

    public static function stored()
    {
        return '[right]Right Curabitur tincidunt, lacus eget iaculis tincidunt, elit libero iaculis arcu, eleifend condimentum sem est quis dolor. Curabitur sed tellus. Donec id dolor.[/right]';
    }

    public static function output()
    {
        return '<div style=\"text-align: right;\">Right Curabitur tincidunt, lacus eget iaculis tincidunt, elit libero iaculis arcu, eleifend condimentum sem est quis dolor. Curabitur sed tellus. Donec id dolor.</div>';
    }
}