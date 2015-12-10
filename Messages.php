<?php
/**
 * The test messages.
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
	'do [u even] know what you talkin bout',
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
	'[img width=43 alt=&quot;google&quot; height=50]http://www.google.com/intl/en_ALL/images/srpr/logo1w.png[/img]',

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
	'Nothing should [table',

	// Disallowed?
	'[size=2]inside size[size=3] - and now even deeper [/size] pull back a little.[/size]',

	// Some non-english characters?
	'[url]www.ñchan.org[/url]',
	'www.ñchan.org',
	'http://www.ñchan.org',

	// https://github.com/SimpleMachines/SMF2.1/issues/3106
	'[list][li]Test[/li][li]More[code]Some COde[/code][/li][/list]',

	// Long messages (put last so I don't have to see them) These usually take too long to run
	// A really long message but without bbc
	//str_repeat('hello world', 1000),
	//str_repeat("This is [i]a[/i] test\nof the [u]emergency[/u] [code]broadcast[/code] system.", 100),

	// Master of abuse
	'[hr][hr][br][center][img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img][/center][center][color=#FF0000][size=36pt]► Founders ◄[/size][/color][/center][center][img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img][/center][br][hr][br][center][size=14pt][color=#FF0000][b]►  [url=http://virtualinteractivege.com/index.php?action=profile;area=summary;u=2][F]Ninja™[/url][/b][/size][size=12pt]: 3D objects creator, inventory designer, site admin and the one who has set up the forum, too.[/size][/center][br][hr][hr][br][center][img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img][/center][center][color=#FF0000][size=36pt]► Developers ◄[/size][/color][/center][center][img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img][/center][br][hr][br][center][size=14pt][color=#FF0000][b]►  [url=http://virtualinteractivege.com/index.php?action=profile;area=summary;u=93]integer[/url][/b][/size][size=12pt]: One of our new programmers, very busy at the moment.[/size][/center][br][hr][hr][br][center][img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img][/center][center][color=#FF0000][size=36pt]► Mapmakers ◄[/size][/color][/center][center][img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img][/center][br][hr][br][center][size=14pt][color=#FF0000][b]Vacant[/b][/size][size=12pt]: We are currently looking for a Mapmaker, applications are being reviewed.[/size][/center][br][hr][br][center][color=#FF0000][b][size=14pt]► [url=http://virtualinteractivege.com/index.php?action=profile;u=101]Gunnyboy[/url][/size][/b][/color][size=12pt]: Has been promoted to Mapmaker for having created some game content! Congratulations![/size][/center][br][hr][hr][br][center][img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img][center][color=#00FF00][size=36pt]► GameMasters ◄[/size][/color][/center][center][img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img][/center][br][hr][br][center][size=14pt][color=#00FF00][b][s]Vacant[/s][/b][/size][size=12pt]: We don\'t need any GameMaster and we are not hiring any of them at the moment.[/size][/center][br][hr][hr][br][center][img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img][/center][center][color=#0000FF][size=36pt]► GameSages ◄[/size][/color][/center][center][img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img][/center][br][hr][br][center][size=14pt][color=#152DC6][b]Vacant[/b][/size][size=12pt]: GameSage applications are currently closed until the game is released, but exceptions are being made on a per-case basis.[/size][/center][br][hr][hr][br][center][img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img][/center][center][color=#00FFFF][size=36pt]► ForumSages ◄[/size][/color][/center][center][img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img][/center][br][hr][br][center][color=#00FFFF][b][size=14pt]► [url=http://virtualinteractivege.com/index.php?action=profile;u=70]♞ Burke Knight ♞[/url][/size][/b][/color][size=12pt]: Very trusty and expert about our forum software. He has years of experience with it. He is now our server admin, too.[/size][/center][br][hr][br][color=#00FFFF][b][size=14pt]►  [url=http://virtualinteractivege.com/index.php?action=profile;u=79]Fortytwo[/url][/size][/b][/color][size=12pt]: Extremely expert as regards .css coding, helped me a LOT with suggestions and fixes. Also taught me how to code the rest of the forum (90 hours of .css coding or something like that).[/size][/center][br][hr][br][center][size=14pt][color=#00FFFF][b]Vacant[/b][/color][/size][size=12pt]: Be very active on the forum and you might be asked to do it. You will need to have a great knowledge of how the forum works, too.[/size][/center][br][hr][hr][br][center][img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img][/center][center][color=#800080][size=36pt]► Helpers ◄[/size][/color][/center][center][img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img]  [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img][/center][br][hr][br][center][size=14pt][b][color=#800080]►[/color] [url=http://virtualinteractivege.com/index.php?action=profile;u=37][color=#800080]Wolf[/color][/url][/b][/size][size=12pt]: Gave us many ideas and helped a lot with testing features and spotting bugs.[/size][br][br][hr][br][center][size=14pt][color=#800080][b]Vacant[/b][/color][/size][size=12pt]: Help us out, stay online, post, invite people and behave in a kind way to have a chance to get on board and we are currently selecting people![/size][br][br][hr][hr][hr][br][center][size=36pt][b][color=#FF0000]Who among these is on VIGE [b]official staff[/b] and who is not?[/color][/b][/size][/center][br][list][li][size=14pt]The [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img] [color=#FF0000][b]VIGE Founder[/b][/color] [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img], the [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img] [color=#FF0000][b]Developer[/b][/color] [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img], the [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img] [color=#FF0000][b]Mapmaker[/b][/color] [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img] and the [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img] [color=#00FF00][b]Gamemaster[/b][/color] [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img] groups are on [u][b]official VIGE staff[/b][/u], and they are paid for it.[/size][/li][/list][list][li][size=14pt]The [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img] [color=#0000FF][b]GameSage[/b][/color] [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img], the [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img] [color=#00FFFF][b]ForumSage[/b][/color] [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img] and the [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img] [color=#800080][b]Helper[/b][/color] [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img] groups are [i]not[/i] on VIGE official staff and they are volunteers (not directly paid for their contributions).[/size][/li][/list][br][hr][hr][br][center][size=36pt][b][color=#FF0000]What are these roles about?[/color][/b][/size][/center][list]
[li][img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img] [color=red][size=16pt]Founders[/size][/color] [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img][size=12pt]: there is only one now - me ([url=http://virtualinteractivege.com/index.php?action=profile;area=summary;u=2][color=red][F]Ninja™[/color][/url]), since the other Founder is not currently working on the project anymore. We are the ones who had the idea of this game and who started the whole project, moreover the ones working/who worked extremely hard on it. I also take care of 3D stuff such as characters and weapons.[/size][/li][/list][list]
[li][img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img] [color=red][size=16pt]Developers[/size][/color] [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img][size=12pt]: will update this. The developers\' task is mainly coding, the whole game scripting.[/li][/list][list]
[li][img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img] [color=red][size=16pt]Mapmakers[/size][/color] [img]http://virtualinteractivege.com/Themes/default/images/starred.gif[/img][size=12pt]: we are currently reviewing applications for this position. Mapmakers\' job is taking care of the maps by designing them, making them and patching them if necessary.[/li][/list][list][li][img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img] [color=lime][size=16pt]Gamemasters[/size][/color] [img]http://virtualinteractivege.com/Themes/default/images/stargreen.gif[/img][size=12pt]: as stated before we are not hiring any at the moment because there is no need but we will hire some in the future for sure when the server will be bigger. Their job should be basically reading tickets, organizing events, moderating the forum and even banning people if needed. They will also have full mod powers on the forum (obviously), almost as many as we have... Almost since I always have more permissions and a fail-safe security.
These people will be paid and will have a contract, further details will be provided in the future when we will be hiring one.[/size][/li][/list][list][li][img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img] [color=blue][size=16pt]Gamesages[/size][/color] [img]http://virtualinteractivege.com/Themes/default/images/starblue.gif[/img][size=12pt]: these are very important users, Gamesages will be chosen in the future among very active and fair players, active both on the forum and in game. They will have more powers than a normal player, in fact they will have mod powers on the forum (not full mod but a lot of them, without ability to ban). They will be able to mute players in game and they will be in direct touch with both founders and gamemasters to quickly report players behaving badly. These players will not be directly rewarded with a monetary compensation but we will possibly give them some custom items if they do their job correctly. They will also have the right to organize events (as well as the Gamemasters). 
How to become a Gamesage in other words? Report players, be fair and very active, show a good knowledge of both forum and game (good use of BBCode and overall useful answers, plus helping out in game - receiving good feedback can help) [u]then when we will open the applications try to apply![/u][/size][/li][/list][list][li][img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img] [color=cyan][size=16pt]Forumsages[/size][/color] [img]http://virtualinteractivege.com/Themes/default/images/starcyan.gif[/img][size=12pt]: these users will be chosen if they are a lot online on the forum and less in game, they will have pretty much the same mod powers as the gamesages on the forum but not in game. Some Forumsages might be chosen if they show a good knowledge of how forums work, if they report rules-breaking messages and post intelligent things. They might become Gamesages too, they are for sure the ones who will most likely become Gamesages, but it\'s not 100% sure. Just to clarify; there will be no applications for Forumsages, they will be chosen by our team.[/size][/li][/list][list][li][img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img] [color=#800080][size=16pt]Helpers[/size][/color] [img]http://virtualinteractivege.com/Themes/default/images/starviolet.gif[/img][size=12pt]: This membergroup is the one in which the majority of people (among the ones who want to become a Gamesage one day) will start, it grants a few more powers than the average user, not many but it\'s a good place to start ;) it\'s fairly obvious that any abuse of these powers will be severely punished. You can either be selected for this or you can apply for it via the "Joinable membergroups" menu in your profile view - please provide valid reasons for your request otherwise it will be discarded quickly.[/size][br]
[hr]
[size=16pt][color=red]Important note[/color]: nobody can be in more than one of these groups and they have different colors to be noticed more easily. [color=red][url=http://virtualinteractivege.com/index.php?action=groups;sa=members;group=9]Founders[/url][/color], [color=red][url=http://virtualinteractivege.com/index.php?action=groups;sa=members;group=22]Developers[/url][/color] and [color=red][url=http://virtualinteractivege.com/index.php?action=groups;sa=members;group=23]Mapmakers[/url][/color] are [color=red]red[/color], [color=lime][url=http://virtualinteractivege.com/index.php?action=groups;sa=members;group=2]Gamemasters[/url][/color] are [color=lime]green[/color], [color=blue][url=http://virtualinteractivege.com/index.php?action=groups;sa=members;group=10]Gamesages[/url][/color] are [color=blue]blue[/color] (with the exception of the user [url=http://virtualinteractivege.com/index.php?action=profile;u=37][color=blue]Wolf[/color][/url] who isn\'t a GameSage in spite of the blue color), [color=cyan][url=http://virtualinteractivege.com/index.php?action=groups;sa=members;group=17]Forumsages[/url][/color] are [color=cyan]cyan[/color] and [color=#800080][url=http://virtualinteractivege.com/index.php?action=groups;sa=members;group=20]Helpers[/url][/color] are [color=#800080]violet[/color].[/size][br][br][hr][right][color=red][size=12pt][url=http://virtualinteractivege.com/index.php?action=profile;area=summary;u=2][F]Ninja™[/url][/size][/color][/right][br][br][/list]',
);