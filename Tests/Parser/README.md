This is the new parser and the reason for this whole endeavor.

## Codes
I completely changed how you access the codes. First off, you can now access them without touching the parser. You can change them and use different objects for different purposes. For instance, use one codes object for messages and another for signatures without losing your codes.

If you try to use your old BBC it won't work. I'm working on a converter for that but it isn't ready at the time of me writing this. 

One big thing I did was to change all of the keys to constants. This has nothing to do with performance. I did it purely so they are standardized. My IDE tells me a constant isn't found so I am less likely to make a typo. It also gives me info about what the constant means so I can remember what unparsed equals does.

It also does a lot more work up front. It checks the length of the tags, if they are disabled, and some other stuff. If I could do more, I would.

## Parsers
There are now 4 separate parsers: BBC, HTML, autolinks, and smileys. Now you can change them out without having to edit any code. 

The BBC parser needs the HTML and autolink parsers. If you want to change them, you should do so by passing them in the constructor of the BBC parser. If you do not pass them the BBC class will attempt to create them. If there is no need to use the HTML parser, it won't try. 

The smileys parser is completely on its own. The only thing that the smiley and BBC parsers have in common is that the BBC parser sets markers for where the smiley parser should look for smileys.

## Caching
The parsers no longer handle anything to do with caching. Well, at the time of writing this, the smileys parser still checks the cache for the smileys in the database. If you want to cache a message because it took a long time to parse, you need to do that outside the parser. Quite honestly, I doubt anyone ever saw anything from parse_bbc() go in to the cache anyway. In my testing I don't have any tests that last .05 seconds. Not even close. Hardcoding that time means you can never catch more either.

I do recommend caching messages though. The parser is certainly CPU intensive. If you display 10 messages with 10 signatures and have 10 news items, that's 110 items on the page that will need to be parsed. If you cache that you will save a lot of time between the database and the parser. 

With that in mind, I added the ability to tell if a message (string) can be cached. Call `$parser->canCache()` and it will return a bool. If any code that was found has a reason why it can't be cached, it will set this to false. The reason would normally be because it does something user-specific like permissions checks. No default codes use this but I am interested to see what does.

## Hooks
I added some new hooks in the parsers and kept a lot of the old ones. Since they are classes and in usage they will be either in a DIC or a global variable, you can always change the variable to another class which extends these, you can really change everything. The hooks just make it even easier (and you don't have to worry about conflicts).

This a list for each parser. The _passed_ is what is passed to the hook. The usage is just a way I thought the hooks could be used. Don't take it as _how_ they should be used, just as an example.

### BBC Parser
* `integrate_post_parsebbc`
    * passed: `$message`
    * usage: you can completely change the entire message. I doubt this one will get used much and I am sure it shouldn't really be used.
* `integrate_bbc_load_html_parser`
    * passed: `$parser`
    * usage: if no HTML parser is passed to the BBC parser it will need to create one. That is when this gets called. If you want to change the HTML parser with a hook, this is where you probably want to do it.
* `integrate_recursive_bbc`
    * passed: `$autolinker`, `$html`
    * usage: since there is recursion in the parser it needs to create a new Parser. This just does everything you do when you create a new BBC parser object. If you do it for a new BBC parser object, you probably want to do it here. That means if you change out the autolinker or HTML parser, you should do that here as well.

### Autolink Parser
* `integrate_autolink_area`
    * passed: `$data`, `$this->bbc`
    * usage: you probably won't want to use this since there are hooks to change the regular expressions already.
* `integrate_autolink_load`
    * passed: `$search_url`, `$replace_url`, `$search_email`, `$replace_email`, `$this->bbc`
    * usage: add/change any of the regular expressions. If you use this you will probably want to also use `integrate_possible_autolink`.
* `integrate_possible_autolink`
    * passed: `$possible_link`, `$possible_email`
    * usage: check if the message contains a possible link or email. Change the `$possible_link` so you can have something like `tel.123456789.call` in a message and it will autolink. You could also do something like ftp.mysite.com and it would link to ftp://ftp.mysite.com.

## HTML Parser
* `integrate_html_parser_load`
    * passed: `$empty_tags`, `$closable_tags`
    * usage: change which tags get formatted as empty and closable. Add closable tags and if they are open, they will be closed
    
## Smiley Parser
At the time of writing this there are no hooks for the smiley parser.