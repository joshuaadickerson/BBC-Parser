<?php

// Test the preparser

namespace BBC;

// Sanitize inputs
require_once 'SanitizeInput.php';
// Include the test file
require_once 'TestBBC.php';
// A lot of functions are shared
require_once 'BBCHelpers.php';

$testBBC = new TestBBC('PreparserTests');

$possible_tests = $testBBC->getPossibleTests();

// We can't set these until we get the possible tests
$input['tests'] = array(
    'a' => isset($_GET['a']) && isset($possible_tests[$_GET['a']]) ? $possible_tests[$_GET['a']]['name'] : 'Old preparser',
    'b' => isset($_GET['b']) && isset($possible_tests[$_GET['b']]) ? $possible_tests[$_GET['b']]['name'] : 'Preparser',
);

$testBBC->setInput($input);

if (isset($test_types[$type]))
{
    call_user_func(array($testBBC, $test_types[$type]), $input);
    $results = $testBBC->getResults();
}

require_once 'Templates/IndexTemplate.php';