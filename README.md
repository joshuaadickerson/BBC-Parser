This is a very slow and thorough rewrite of the BBC parsing in Elkarte.

I have done this once or twice before with miserable results. Now, I am going to do it much thoroughly.

Each commit should pass all of the tests (as I write those tests).
Not every commit will result in a faster parser, but the end result should be faster and better for resources.
In the end, I hope to make it much more maintainable and more object oriented.

Long term, this should use preg_metch with offset capture and even an AST

preg_split() on any [$tag and [/$tag (itemcodes includes) and ]

in index.php, setting SAVE_TOP_RESULTS to true will result in it creating a csv file.
To parse this CSV file, open TopResults.php

===
Changes
* `$no_autolink_tags` no longer exists. It is now an attribute of the tag as "autolink"
* you can get the bbc without loading parse_bbc(). Seperate loading from parsing
* changed substr() == str to substr_compare(). Don't use strpos() when you want to do a substr_compare either
* seperate construction of the parser from the execution more
* replace substr() . substr() with substr_replace()
* removed ftp:// autolinking and the [ftp] tag. Pointless to keep it