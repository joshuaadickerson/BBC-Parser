<?php
// These are the messages that go in to the preparser for testing

return array(
    // Taken from Preparse.subs.php
    '[font=something]text[/font]',
    '[font=something, someother]text[/font]',
    '[font=something, \'someother\']text[/font]',
    '[font=\'something\', someother]text[/font]',
    'something[quote][/quote]',
    'something[code]without a closing tag',
    'some open list[list][li]one[/list]',
    'some list[code][list][li]one[/list][/code]',

    // Apparently Apache has/had an issue with too many periods. I guess Apache doesn't have a wife/gf
    str_repeat('.', 1000),

    // Me me me
    '/me , it\'s all about',

    // Empty tags
    '[list][/list]',
    '[i][/i]',

    // Itemcodes and lists
    "[*]list item\n[*]another list item\n[*]yet another list item",

    // Autolinks
    'http://www.google.com',
    'www.google.com',
    'my@email.com',
    'ftp://some.ftp.com',
    'https://secure.google.com',

    // https://github.com/SimpleMachines/SMF2.1/issues/3106
    '[list][li]Test[/li][li]More[code]Some COde[/code][/li][/list]',
);