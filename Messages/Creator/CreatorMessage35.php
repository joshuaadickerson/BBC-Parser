<?php

/* The original message
[code]	this 		has 	tabs


	tab
 tab
[/code]
even	some	outside		THE code
*/


class Message35 implements MessageInterface
{
    public static function name()
    {
        return 'Message35';
    }

    public static function input()
    {
        return '[code]	this 		has 	tabs


	tab
 tab
[/code]
even	some	outside		THE code';
    }

    public static function stored()
    {
        return '[code]	this 		has 	tabs<br /><br /><br />	tab<br /> tab<br />[/code]<br />even	some	outside		THE code';
    }

    public static function output()
    {
        return '[code]	this 		has 	tabs<br /><br /><br />	tab<br /> tab<br />[/code]<br />even	some	outside		THE code';
    }
}