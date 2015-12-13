<?php

/* The original message
[img src=www.here.com/index.php?action=dlattach] this is actually a security issue
*/


class Message120 implements MessageInterface
{
    public static function name()
    {
        return 'Message120';
    }

    public static function input()
    {
        return '[img src=www.here.com/index.php?action=dlattach] this is actually a security issue';
    }

    public static function stored()
    {
        return '[img src=www.here.com/index.php?action=dlattach] this is actually a security issue';
    }

    public static function output()
    {
        return '[img src=www.here.com/index.php?action=dlattach] this is actually a security issue';
    }
}