<?php

/* The original message
[url=&quot;www.autolink.org/?a[]=1&a[]=2;a[]=3;b[a]=6&b[b]=99&quot;]www.autolink.org/?a[]=1&a[]=2;a[]=3;b[a]=6&b[b]=99[/url]
*/


class Message76 implements MessageInterface
{
    public static function name()
    {
        return 'Message76';
    }

    public static function input()
    {
        return '[url=&quot;www.autolink.org/?a[]=1&a[]=2;a[]=3;b[a]=6&b[b]=99&quot;]www.autolink.org/?a[]=1&a[]=2;a[]=3;b[a]=6&b[b]=99[/url]';
    }

    public static function stored()
    {
        return '[url=http://&quot;www.autolink.org/?a&#91;]=1&a&#91;]=2;a&#91;]=3;b[a]=6&b[b]=99&quot;]www.autolink.org/?a&#91;]=1&a&#91;]=2;a&#91;]=3;b[a]=6&b[b]=99[/url]';
    }

    public static function output()
    {
        return '<a href=\"http://&quot;www.autolink.org/?a&#91;\" class=\"bbc_link\" target=\"_blank\">=1&a&#91;]=2;a&#91;]=3;b[a]=6&b<strong class=\"bbc_strong\">=99&quot;]www.autolink.org/?a&#91;]=1&a&#91;]=2;a&#91;]=3;b[a]=6&b<strong class=\"bbc_strong\">=99</strong></strong></a>';
    }
}