<?php

namespace BBC;

// Necessary includes
require_once __DIR__ . '/../Tests/Parser/Codes.php';
require_once __DIR__ . '/../Converter/Convert.php';
require_once __DIR__ . '/../Converter/Export.php';
require_once __DIR__ . '/MakerErrors.php';

$export = new Export;

// Create BBC

// Setup the inputs
$selected = array(
    'type' => !empty($_REQUEST['type'])  ? (int) $_REQUEST['type'] : 0,
);

$values = array(
    'type' => getVar('type', 0, 'int'),
    'tag' => getVar('tag', '', 'string'),
    'content' => getVar('content', '', 'string'),
);

$errors = new \MakerErrors;
validateValues($values, $errors);

// Output the template
require_once 'Template.php';

function validateValues(array &$values, &$errors)
{
    // Check type between 0 and 8
    if ($values['type'] < 0 || $values['type'] > 8)
    {
        $errors->add('type', 'Invalid type');
    }

    // Lowercase tag. Check that it is 'a-z0-9_-'
    if (!isValidTag($values['tag']))
    {
        $errors->add('tag', 'Tag may only contain characters: a-z0-9_-');
    }

    // A lot of things involve content
    if (isset($values['content']))
    {
        // Content can only be used for unparsed_content, closed, unparsed_commas_content, and unparsed_equals_content
        if (!in_array($values['type'], array(Codes::TYPE_UNPARSED_CONTENT, Codes::TYPE_CLOSED, Codes::TYPE_UNPARSED_COMMAS_CONTENT, Codes::TYPE_UNPARSED_EQUALS_CONTENT)))
        {
            $errors->add('content', 'Content may only be used for unparsed_content, closed, unparsed_commas_content, and unparsed_equals_content types');
        }

        // Before/after can only be used when content is not used
        if (isset($values['before']))
        {
            $errors->add('before', 'Before and content cannot be used together.');
        }

        if (isset($values['after']))
        {
            $errors->add('after', 'After and content cannot be used together.');
        }
    }

    // Same goes for disabled_content/before/after
    if (isset($values['disabled_content']))
    {
        // Content can only be used for unparsed_content, closed, unparsed_commas_content, and unparsed_equals_content
        if (!in_array($values['type'], array(Codes::TYPE_UNPARSED_CONTENT, Codes::TYPE_CLOSED, Codes::TYPE_UNPARSED_COMMAS_CONTENT, Codes::TYPE_UNPARSED_EQUALS_CONTENT)))
        {
            $errors->add('disabled_content', 'Disabled content may only be used for unparsed_content, closed, unparsed_commas_content, and unparsed_equals_content types');
        }

        if (isset($values['disabled_before']))
        {
            $errors->add('disabled_before', 'Disabled Before and disabled content cannot be used together.');
        }

        if (isset($values['disabled_after']))
        {
            $errors->add('disabled_after', 'Disabled After and disabled content cannot be used together.');
        }
    }

    // Only parsed content (0) and unparsed content (3) can have parameters
    // Block level is bool
    // trim is empty, inside, outside, or both
    // quoted is empty, optional, or required
    // require_parents and children is a comma delimited list of tag names. Lowercase the tag names and check that they are valid tag names
    // Same goes for ATTR_DISALLOW_PARENTS, ATTR_DISALLOW_CHILDREN, ATTR_DISALLOW_BEFORE, ATTR_DISALLOW_AFTER, ATTR_PARSED_TAGS_ALLOWED
    // check to make sure require_parents doesn't contain anything in disallow_parents. Same for children
    // autolink is bool
    // disabled is bool
    // no_cache is bool
}

function filterTagList(array $tags)
{

}

function isValidTag(&$tag)
{
    $tag = strtolower($tag);

    return preg_match('/^[a-z0-9_]+$/', $tag) === 0;
}

function getVar($name, $default_value, $type = null)
{
    if (!isset($_REQUEST[$name]))
    {
        return $default_value;
    }

    if ($type !== null)
    {
        if ($type === 'string')
        {
            return (string) $_REQUEST[$name];
        }

        if ($type === 'int')
        {
            return (int) $_REQUEST[$name];
        }

        if ($type === 'bool')
        {
            return (bool) $_REQUEST[$name];
        }
    }

    return $_REQUEST[$name];
}

function displayErrors(\MakerErrors $errors, $section)
{
    $return = '';
    foreach ($errors->getSection($section) as $error)
    {
        $return .= '
        <div>' . $error['error'] . '</div>';

    }

    return $return;
}