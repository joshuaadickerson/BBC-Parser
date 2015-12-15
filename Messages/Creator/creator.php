<?php

// Messages
$message_file = '../../Messages.php';
$message_dir  = __DIR__ . '/Creator';
$messages = require_once $message_file;

// Parser
$parser_dir = '../../Tests/Parser/';
require_once $parser_dir . '/Parser.php';
require_once $parser_dir . '/Codes.php';
require_once $parser_dir . '/DefaultCodes.php';
require_once $parser_dir . '/SmileyParser.php';
require_once $parser_dir . '/Autolink.php';
require_once $parser_dir . '/HtmlParser.php';

require_once '../../BBCHelpers.php';

$bbc = new \BBC\DefaultCodes(array(), array());
$autolink = new \BBC\Autolink($bbc);
$html = new \BBC\HtmlParser;

$parser = new \BBC\Parser($bbc, $autolink, $html);
$smiley_parser = new \BBC\SmileyParser;

// Preparser
require_once '../../PreparserTests/OldPreparser/OldPreParser.php';

$GLOBALS['modSettings']['enableBBC'] = true;
$GLOBALS['modSettings']['autoLinkUrls'] = true;

foreach ($messages as $i => $input)
{
    $class_name = 'Message' . $i;
    $filename = 'Message' . $i . '.php';

    // These aren't preparsed. This is how they will be stored.
    $stored = $input;
    preparsecode($stored);

    if ($stored !== $input)
    {
        echo "\nMessage $i needs to be preparsed<br>";
    }

    $output = $parser->parse($stored);
    $smiley_parser->parse($output);

    $escaped_input = addslashes($input);
    $escaped_stored = addslashes($stored);
    $escaped_output = addslashes($output);

    $file_contents = <<<EOF
<?php

/* The original message
$escaped_input
*/


class $class_name implements MessageInterface
{
    public static function name()
    {
        return '$class_name';
    }

    public static function input()
    {
        return '$escaped_input';
    }

    public static function stored()
    {
        return '$escaped_stored';
    }

    public static function output()
    {
        return '$escaped_output';
    }
}
EOF;

    file_put_contents($message_dir . $filename, $file_contents);
}
