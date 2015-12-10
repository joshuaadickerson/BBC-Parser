<?php

class VeryLong implements MessageInterface
{
    private static $string = 'This is a div with multiple classes and no ID. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec volutpat tellus vulputate dui venenatis quis euismod turpis pellentesque. Suspendisse [sit] amet ipsum eu odio sagittis ultrices at non sapien. Quisque viverra feugiat purus, eu mollis felis condimentum id. In luctus faucibus felis eget viverra. Vivamus et velit orci. In in tellus mauris, at fermentum diam. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Sed a magna nunc, vel tempor magna. Nam dictum, arcu in pretium varius, libero enim hendrerit nisl, et commodo enim sapien eu augue.';

    public static function name()
    {
        return 'Very long string with no BBC';
    }

    public static function input()
    {
        return str_repeat(self::$string, 5);
    }

    public static function stored()
    {
        return str_repeat(self::$string, 5);
    }

    public static function output()
    {
        return str_repeat(self::$string, 5);
    }
}