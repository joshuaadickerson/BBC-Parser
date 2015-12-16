<?php

/* The original message
[code]	this 	has 	tabs


	tab
 tab
[/code]
even	some	outside		THE code
*/


class Message113 implements MessageInterface
{
    public static function name()
    {
        return 'Message113';
    }

    public static function input()
    {
        return '[code]	this 	has 	tabs


	tab
 tab
[/code]
even	some	outside		THE code';
    }

    public static function stored()
    {
        return '[code]	this 	has 	tabs<br /><br /><br />	tab<br /> tab<br />[/code]<br />even	some	outside		THE code';
    }

    public static function output()
    {
        return '<div class=\"codeheader\">code: <a href=\"javascript:void(0);\" onclick=\"return elkSelectText(this);\" class=\"codeoperation\">select</a></div><pre class=\"bbc_code prettyprint\"><span class=\"tab\">	</span>this <span class=\"tab\">	</span>has <span class=\"tab\">	</span>tabs<br /><br /><br /><span class=\"tab\">	</span>tab<br />&nbsp;tab<br /></pre>even&nbsp;&nbsp;&nbsp;some&nbsp;&nbsp;&nbsp;outside&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;THE code';
    }
}