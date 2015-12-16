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

globalSettings();

$bbc = new \BBC\DefaultCodes(array(), array());
$autolink = new \BBC\Autolink($bbc);
$html = new \BBC\HtmlParser;

$parser = new \BBC\Parser($bbc, $autolink, $html);
$smiley_parser = new \BBC\SmileyParser($modSettings['smileys_url'] . '/' . $user_info['smiley_set'] . '/');

// Preparser
require_once '../../PreparserTests/OldPreparser/OldPreParser.php';

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


function globalSettings()
{
    global $txt, $modSettings, $user_info, $scripturl;

    $scripturl = 'http://localhost';

    $txt = array(
        'code' => 'code',
        'code_select' => 'select',
        'quote' => 'quote',
        'quote_from' => 'quote from',
        'search_on' => 'search on',
        'spoiler' => 'spoiler',

        // For the smilies
        'icon_cheesy' => 'cheesy',
        'icon_rolleyes' => 'rolleyes',
        'icon_angry' => 'angry',
        'icon_laugh' => 'laugh',
        'icon_smiley' => 'smile',
        'icon_wink' => 'wink',
        'icon_grin' => 'grin',
        'icon_sad' => 'sad',
        'icon_shocked' => 'shocked',
        'icon_cool' => 'cool',
        'icon_tongue' => 'tongue',
        'icon_huh' => 'huh',
        'icon_embarrassed' => 'embarrassed',
        'icon_lips' => 'lips',
        'icon_kiss' => 'kiss',
        'icon_cry' => 'cry',
        'icon_undecided' => 'undecided',
        'icon_angel' => 'angel',
    );

    $modSettings = array(
        'smiley_enable' => false,
        'enableBBC' => true,
        'todayMod' => '3',
        'cache_enable' => false,
        'autoLinkUrls' => true,
        // These will have to be set to test that block, but that is for later
        'max_image_width' => false,
        'max_image_height' => false,
        'smileys_url' => 'http://www.google.com/smileys',
        'enablePostHTML' => true,
    );

    $user_info = array(
        'smiley_set' => false,
        'name' => 'what\'s in',
    );

    if (!defined('SUBSDIR')) {
        define('SUBSDIR', __DIR__);
    }
}