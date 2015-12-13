<?php

/* The original message
[code][b]Bold[/b]
	Italics
	Underline
	Strike through[/code]
*/


class Message113 implements MessageInterface
{
    public static function name()
    {
        return 'Message113';
    }

    public static function input()
    {
        return '[code][b]Bold[/b]
	Italics
	Underline
	Strike through[/code]';
    }

    public static function stored()
    {
        return '[code][b]Bold[/b]<br />	Italics<br />	Underline<br />	Strike through[/code]';
    }

    public static function output()
    {
        return '[code][b]Bold[/b]<br />	Italics<br />	Underline<br />	Strike through[/code]';
    }
}