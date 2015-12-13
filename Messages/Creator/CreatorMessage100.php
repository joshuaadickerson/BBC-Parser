<?php

/* The original message
Footnote[footnote]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec volutpat tellus vulputate dui venenatis quis euismod turpis pellentesque. Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien. Quisque viverra feugiat purus, eu mollis felis condimentum id. In luctus faucibus felis eget viverra. Vivamus et velit orci. In in tellus mauris, at fermentum diam. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Sed a magna nunc, vel tempor magna. Nam dictum, arcu in pretium varius, libero enim hendrerit nisl, et commodo enim sapien eu augue. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Suspendisse potenti. Proin tempor porta porttitor. Nullam a malesuada arcu.[/footnote]
*/


class Message100 implements MessageInterface
{
    public static function name()
    {
        return 'Message100';
    }

    public static function input()
    {
        return 'Footnote[footnote]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec volutpat tellus vulputate dui venenatis quis euismod turpis pellentesque. Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien. Quisque viverra feugiat purus, eu mollis felis condimentum id. In luctus faucibus felis eget viverra. Vivamus et velit orci. In in tellus mauris, at fermentum diam. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Sed a magna nunc, vel tempor magna. Nam dictum, arcu in pretium varius, libero enim hendrerit nisl, et commodo enim sapien eu augue. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Suspendisse potenti. Proin tempor porta porttitor. Nullam a malesuada arcu.[/footnote]';
    }

    public static function stored()
    {
        return 'Footnote[footnote]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec volutpat tellus vulputate dui venenatis quis euismod turpis pellentesque. Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien. Quisque viverra feugiat purus, eu mollis felis condimentum id. In luctus faucibus felis eget viverra. Vivamus et velit orci. In in tellus mauris, at fermentum diam. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Sed a magna nunc, vel tempor magna. Nam dictum, arcu in pretium varius, libero enim hendrerit nisl, et commodo enim sapien eu augue. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Suspendisse potenti. Proin tempor porta porttitor. Nullam a malesuada arcu.[/footnote]';
    }

    public static function output()
    {
        return 'Footnote[footnote]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec volutpat tellus vulputate dui venenatis quis euismod turpis pellentesque. Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien. Quisque viverra feugiat purus, eu mollis felis condimentum id. In luctus faucibus felis eget viverra. Vivamus et velit orci. In in tellus mauris, at fermentum diam. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Sed a magna nunc, vel tempor magna. Nam dictum, arcu in pretium varius, libero enim hendrerit nisl, et commodo enim sapien eu augue. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Suspendisse potenti. Proin tempor porta porttitor. Nullam a malesuada arcu.[/footnote]';
    }
}