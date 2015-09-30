<?php
$type = isset($_GET['type']) ? $_GET['type'] : false;
$parser_type = isset($_GET['parser']) ? $_GET['parser'] : false;

$msgs = null;
if (isset($_GET['msg']) && $_GET['msg'] !== '')
{
    if (is_array($_GET['msg']))
    {
        $msgs = array_map('intval', $_GET['msg']);
        $msgs = array_unique($msgs);
    }
    elseif (strpos($_GET['msg'], ',') !== false)
    {
        $msgs = explode(',', $_GET['msg']);
        $msgs = array_map('trim', $msgs);
    }
    else
    {
        $msgs = (int) $_GET['msg'];
    }
}

$disabled_tags = array();
if (isset($_GET['disabled_tags']) && $_GET['disabled_tags'] !== '')
{
    if (is_array($_GET['disabled_tags']))
    {
        $disabled_tags = array_unique($disabled_tags);
    }
    elseif (strpos($_GET['disabled_tags'], ',') !== false)
    {
        $disabled_tags = explode(',', $_GET['disabled_tags']);
    }
    else
    {
        $disabled_tags = array($_GET['disabled_tags']);
    }

    $disabled_tags = array_map('trim', $disabled_tags);
}

// Run the test (based on type)
$test_types = array(
    'test' => 'tests',
    'bench' => 'benchmark',
    'individual' => 'individual',
);

$input = array(
    'parser' => array(
        'parser' => $parser_type === 'parser' ? 'selected="selected"' : '',
        'preparser' => $parser_type === 'preparser' ? 'selected="selected"' : '',
    ),
    'type' => array(
        'test' => $type === 'test' ? ' selected="selected"' : '',
        'bench' => $type === 'bench' ? ' selected="selected"' : '',
        'individual' => $type === 'individual' ? ' selected="selected"' : '',
    ),
    'iterations' => isset($_GET['iterations']) ? min($_GET['iterations'], 10000) : 0,
    'debug' => isset($_GET['debug']) && $_GET['debug'] ? 'checked="checked"' : '',
    'fatal' => isset($_GET['fatal']) && $_GET['fatal'] ? 'checked="checked"' : '',
    'msg' => $msgs,
    'disabled' => $disabled_tags,
    'save_top' => !empty($_GET['save_top']),
    // Find the test directory
    'tests_dir' => !empty($_GET['test_dir']) && is_dir(__DIR__ . '/' . $_GET['test_dir']) ? $_GET['test_dir'] : 'Tests',
);