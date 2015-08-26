<?php
/**
 * The test messags.
 * Generally, they go from less to more complex.
 *
 * This should result in the same messages for every call. So don't put any randomization in this.
 */
return array(
	// Nothing. It should just return
	'',

	// It shouldn't treat these as a bool
	'false',
	'0',
	'array()',
	' ',

	// Simple message, no BBC
	'hello world',
	'foo bar',
	"Breaker\nbreaker\n1\n9",

	// Simple BBC
	'[b]Bold[/b]',
	'[i]Italics[/i]',
	'[u]Underline[/u]',
	'[s]Strike through[/s]',
	'[b][i][u]Bold, italics, underline[/u][/i][/b]',
	'Super[sup]script[/sup]',
	'Sub[sub]script[/sub]',
	'[sup]Super[/sup]-[sub]sub[/sub]-script',

	// A longer message but without BBC
	str_repeat('This is a div with multiple classes and no ID. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec volutpat tellus vulputate dui venenatis quis euismod turpis pellentesque. Suspendisse [sit] amet ipsum eu odio sagittis ultrices at non sapien. Quisque viverra feugiat purus, eu mollis felis condimentum id. In luctus faucibus felis eget viverra. Vivamus et velit orci. In in tellus mauris, at fermentum diam. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Sed a magna nunc, vel tempor magna. Nam dictum, arcu in pretium varius, libero enim hendrerit nisl, et commodo enim sapien eu augue.', 5),

	// A message that might have bbc, but really doesn't
	'This message doesn\'t actually have [ bbc',
	'Neither does [] this one',
	'Nor do[es] this one',
	'Not ev[en] this on[/en] has bbc',
	'This one is sneaky: [/] [ /] [  /] [   /]',

	// Time for smilies
	' :) ',
	':)',
	'Smile :)',
	'Smil:)ey face',
	'Now for all of the default:  :)  ;)  :D  ;D  :(  :(  ::)  >:(  >:(  :o  8)  ???  :P  :-[  :-X  :-\  :-\  :-*  :-*  :\'(  O:-) ',
	'and the good old whatzup??? which should not show',

	// Time to test BBC
	'[b]This statement is bold[/b]',
	'[url=http://www.google.com]Google[/url]. Basic unparsed equals',
	'[code]bee boop bee booo[/code]',
	"Everyone\n[code]\ngets a line\n[/code]\nbreak",
	"You\n[code=me]\nget 1\n[/code]and [code]\nyou get one[/code]",
	'[code]I [b]am[/b] a robot [quote]bee boo bee boop[/quote][/code]',
	"[code]	this \t	has 	tabs\n\n\n	tab\n tab\n[/code]\neven\tsome\toutside\t	THE code",
	'[nobbc][b]please do not parse this[/b][/nobbc]',
	'[br][hr][br /][hr /]',
	"[code][/code]\ne",
	'[size=1]BIG E[/size]',

	// Lists are probably the most complicated part of the parser
	'[list][li]short list[/li][/list]',
	'[list][li]short list[/li][li]to do[/li][li]growing[/li][/list]',
	'[list][li]quick[li]no[li]time[li]for[li]closing[li]tags[/list]',
	'[list][li]I[/li][li]feel[list][li]like[/list][li]Santa[/li][/list]',
	'[list type=decimal][li]simple[/li][li]list[/li][/list]',

	// Tables
	'[table][tr][td]remember[/td][td]frontpage?[/td][/tr][/table]',
	'[table][tr][td]let me see[/td][td][table][tr][td]if[/td][td]I[/td][/tr][tr][td]can[/td][td]break[/td][/tr][tr][td]the[/td][td]internet[/td][/td][/tr][/table]',
	'[table][tr][th][/th][/tr][tr][td][/td][/tr][tr][td][/td][/tr][/table]',

	// Images
	'[img width=500]http://www.google.com/intl/en_ALL/images/srpr/logo1w.png[/img]',
	'[img height=50]http://www.google.com/intl/en_ALL/images/srpr/logo1w.png[/img]',
	'[img width=43 alt="google" height=50]http://www.google.com/intl/en_ALL/images/srpr/logo1w.png[/img]',

	// Quotes are actually a huge part of the parser
	'[quote]If at first you do not succeed; call it version 1.0[/quote]',
	'[quote=&quot;Edsger Dijkstra&quot;]If debugging is the process of removing software bugs, then programming must be the process of putting them in[/quote]',
	'[quote author=Gates]Measuring programming progress by lines of code is like measuring aircraft building progress by weight.[/quote]',
	'[quote]Some[quote]basic[/quote]nesting[/quote]',
	'[quote][quote][quote][quote]Some[quote]basic[/quote]nesting[/quote]Still[/quote]not[/quote]deep[/quote]enough',
	'[quote author=Mutt & Jeff link=topic=14764.msg87204#msg87204 date=1329175080]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/quote]',
	'[quote link=topic=14764.msg87204#msg87204 author=Mutt & Jeff date=1329175080]I started a band called 999 Megabytes. We don&apos;t have a gig yet.[/quote]',
	'[quote=Joe Doe joe@email.com]Here is what Joe said.[/quote]',
	'[quote]test[quote]nested 1[quote]nested 2[quote]nested 3[quote]nested 4[quote]nested 5[/quote][quote]nested 4.1[/quote][/quote][quote]nested3.1[/quote][quote]nested3.2[/quote][quote]nested3.3[/quote][quote]nested3.4[/quote][/quote][/quote][/quote][/quote]',

	// Item codes... suck.
	"[*]one dot\n[*]two dots",
	"[*]Ahoy!\n[*]Me[@]Matey\n[+]Shiver\n[x]Me\n[#]Timbers\n[!]\n[*]I[*]dunno[*]why",

	// Autolinks (specifically avoiding FTP)
	'http://www.google.com',
	'https://google.com',
	'http://google.de',
	'www.google.com',
	'me@email.com',
	'http://www.cool.guy/linked?no&8)',
	'http://www.facebook.com/profile.php?id=1439984468#!/group.php?gid=103300379708494&ref=ts',
	'www.ñchan.org',

	// These shouldn't be autolinked.
	'[url=https://www.google.com]http://www.google.com/404[/url]',
	'[url=https://www.google.com]www.google.com[/url]',
	'[url=https://www.google.com]you@mailed.it[/url]',
	'[url=http://www.google.com/]test www.elkarte.net test[/url]',
	'[url=http://www.elkarte.org/community/index.php [^]]ask us for assistance[/url]',

	// URIs in no autolink areas
	'[url=http://www.google.com]www.bing.com[/url]',
	'[iurl=http://www.google.com]www.bing.com[/iurl]',
	'[email=jack@theripper.com]www.bing.com[/email]',
	'[url=http://www.google.com]iam@batman.net[/url]',

	// Links inside links:
	'[url=http://www.google.com/]this url has [email=someone@someplace.org]an email[/email][/url]',
	'[url=http://www.yahoo.com]another URL[/url] in it![/url]',
	'Testing autolink then a url: www.google.com [url=this no worky] [b]a tag to close it [/b] [/url] just to make sure',

	// BBC with smilies and autolinks? Let's try
	"[quote]Economics is [b]everywhere[/b] :)\nand understanding economics can help you make better www.decisio.ns and lead a happier life.[/quote]",

	// Colors
	'[color=red]red[/color][color=green]green[/color][color=blue]blue[/color]',
	'[color=red]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/color]',
	'[color=blue]Volutpat tellus vulputate dui venenatis quis euismod turpis pellentesque.[/color]',
	'[color=#f66]Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.[/color]',
	'[color=#ff0088]Quisque viverra feugiat purus, in luctus faucibus felis eget viverra.[/color]',
	'[color=#cccccc]Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.[/color]',
	'[color=DarkSlateBlue]this is colored![/color]',

	// Fonts
	'[size=4]Font Family[/size]',
	'[font=Arial]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/font]',
	'[font=Tahoma]Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.[/font]',
	'[font=Monospace]Quisque viverra feugiat purus, in luctus faucibus felis eget viverra.[/font]',
	'[font=Times]Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien.[/font]',

	// Some more quoted parameters (they seem to take a long time)
	'[url=&quot;http://www.google.com&quot;]quoted url[/url]',
	'[img alt=MyImage height=100 width=100]http://www.google.com/img.png[/img]',
	'[img alt=&quot;My image&quot; height=&quot;100&quot; width=&quot;100&quot;]http://www.google.com/img.png[/img]',
	'[quote=&quot;[u]underline[/u]]&quot;]this is weird[/quote]',
	'[quote=&quot;[url]http://www.google.com[/url]]&quot;]a link in the quote? uhhh okay[/quote]',

	// Footnotes
	'Footnote[footnote]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec volutpat tellus vulputate dui venenatis quis euismod turpis pellentesque. Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien. Quisque viverra feugiat purus, eu mollis felis condimentum id. In luctus faucibus felis eget viverra. Vivamus et velit orci. In in tellus mauris, at fermentum diam. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Sed a magna nunc, vel tempor magna. Nam dictum, arcu in pretium varius, libero enim hendrerit nisl, et commodo enim sapien eu augue. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Suspendisse potenti. Proin tempor porta porttitor. Nullam a malesuada arcu.[/footnote]',

	// Spoilers
	'[spoiler]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec volutpat tellus vulputate dui venenatis quis euismod turpis pellentesque. Suspendisse sit amet ipsum eu odio sagittis ultrices at non sapien. Quisque viverra feugiat purus, eu mollis felis condimentum id. In luctus faucibus felis eget viverra. Vivamus et velit orci. In in tellus mauris, at fermentum diam. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Sed a magna nunc, vel tempor magna. Nam dictum, arcu in pretium varius, libero enim hendrerit nisl, et commodo enim sapien eu augue. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Suspendisse potenti. Proin tempor porta porttitor. Nullam a malesuada arcu.[/spoiler]',

	// Align
	'[center]Center Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam laoreet pulvinar sem. Aenean at odio.[/center]',
	'[tt]Teletype Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec elit. Fusce eget enim. Nullam tellus felis, sodales nec, sodales ac, commodo eu, ante.[/tt]',
	'[right]Right Curabitur tincidunt, lacus eget iaculis tincidunt, elit libero iaculis arcu, eleifend condimentum sem est quis dolor. Curabitur sed tellus. Donec id dolor.[/right]',
	'[left]Left Curabitur tincidunt, lacus eget iaculis tincidunt, elit libero iaculis arcu, eleifend condimentum sem est quis dolor. Curabitur sed tellus. Donec id dolor.[/left]',
	'[pre]Pre .. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec elit. Fusce eget enim. Nullam tellus felis, sodales nec, sodales ac, commodo eu, ante.[/pre]',

	// Code
	'[code]bee boop bee booo[/code]',
	'Everyone\n[code]\ngets a line\n[/code]\nbreak',
	'You\n[code=me]\nget 1\n[/code]and [code]\nyou get one[/code]',
	'[code]I [b]am[/b] a robot [quote]bee boo bee boop[/quote][/code]',
	"[code]	this 	has 	tabs\n\n\n	tab\n tab\n[/code]\neven\tsome\toutside\t	THE code",
	'[code=php]
	<?php
	/**
	 * This controller is the most important and probably most accessed of all.
	 * It controls topic display, with all related.
	 */
	class Display_Controller
	{
		/**
		 * Default action handler for this controller
		 */
		public function action_index()
		{
			// what to do... display things!
			$this->action_display();
		}?>
	[/code]',
	'[code][b]Bold[/b]
	Italics
	Underline
	Strike through[/code]',
	'[code]email@domain.com
	:]   :/ >[ :p  >_>

	:happy: :aw: :cool: :kiss: :meh: :mmf: :heart:

	[/code]',

	// Just to test unparsed_commas, but you need to add the defintion to the parser
	'[glow=red,2,50]glow[/glow]',

	// I guess it's time to test HTML parsing.
	htmlspecialchars('<b>Bold</b><i>italics</i>'),

	// Bad BBC
	'[i]lets go for italics',
	'[u][i]Why do you do this to yourself?[/u][/i]',
	// This should close the u before opening quote
	'[u][quote]should not get underlined[/quote][/u]',
	'[img src=www.here.com/index.php?action=dlattach] this is actually a security issue',
	'[quote this=should not=work but=maybe it=will]only a test will tell[/quote]',
	'[list][li]quick[li]no[li]time[li]for[li]closing[li]tags[/list]',
	'[size=6.2]itty bitty (does not pass test)[/size]',

	// Some non-english characters?
	'[url]www.ñchan.org[/url]',
	'www.ñchan.org',
	'http://www.ñchan.org',
	// Long messages (put last so I don't have to see them) These usually take too long to run
	// A really long message but without bbc
	//str_repeat('hello world', 1000),
	//str_repeat("This is [i]a[/i] test\nof the [u]emergency[/u] [code]broadcast[/code] system.", 100),

);