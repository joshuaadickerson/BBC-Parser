<?php

// Necessary includes
require_once __DIR__ . '/../Tests/Parser/Codes.php';
require_once __DIR__ . '/../Converter/Convert.php';
require_once __DIR__ . '/../Converter/Export.php';

$export = new Export;

// Create BBC

// Setup the inputs
$selected = array(
    'type' => !empty($_REQUEST['type'])  ? (int) $_REQUEST['type'] : 0,
);

$values = array(
    'type' => isset($_REQUEST['type'])  ? (int) $_REQUEST['type'] : 0,
    'tag' => isset($_REQUEST['tag']) ? $_REQUEST['tag'] : '',
    'content' => isset($_REQUEST['content']) ? $_REQUEST['content'] : '',
);

// Output the template
require_once 'Template.php';

function validateValues(array $values)
{
    // Check type between 0 and 8
    // Lowercase tag. Check that it is 'a-z0-9_-'
    // Content can only be used for unparsed_content, closed, unparsed_commas_content, and unparsed_equals_content
    // Before/after can only be used when content is not used
    // Same goes for disabled_content/before/after
    // Block level is bool
    // trim is empty, inside, outside, or both
    // quoted is empty, optional, or required
    // require_parents and children is a comma delimited list of tag names. Lowercase the tag names and check that they are valid tag names
    // Same goes for ATTR_DISALLOW_PARENTS, ATTR_DISALLOW_CHILDREN, ATTR_DISALLOW_BEFORE, ATTR_DISALLOW_AFTER, ATTR_PARSED_TAGS_ALLOWED
    // autolink is bool
    // disabled is bool
    // no_cache is bool
}

function filterTagList(array $tags)
{

}

function isValidTag($tag)
{
    $tag = strtolower($tag);
    // preg_match();

}