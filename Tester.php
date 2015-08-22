<?php
/**
 * I realize this can and should be done with PHPUnit or similar,
 * but this is a simple test script that I am mocking up in Gist editor
 * because my computer is in Florida. Once it gets here, this might change.
 *
 * One thing that won't change is PHPUnit won't measure the RAM and mem taken
 * which is one of the main reasons for rewriting.
 */
namespace BBC;

// Some constants
if (!defined('ITERATIONS'))
{
	define('ITERATIONS', 100);
}
if (!defined('DEBUG'))
{
	define('DEBUG', true);
}
if (!defined('FAILED_TEST_IS_FATAL'))
{
	define('FAILED_TEST_IS_FATAL', false);
}

// Neccessary files
require_once 'ParseBBC.php';
require_once 'Codes.php';
require_once 'Parser.php';
require_once 'BBCHelpers.php';

globalSettings();

function globalSettings()
{
	global $txt, $modSettings, $user_info, $scripturl;

	error_reporting(E_ALL);
	ini_set('display_errors', 1);

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
	);

	$user_info = array(
		//'smiley_set' => false,
		'smiley_set' => 'none',
	);

	if (!defined('SUBSDIR'))
	{
		define('SUBSDIR', __DIR__);
	}
}

function tests($input)
{
	$bbc = new Codes;
	$parser = new Parser($bbc);

	setupOldParseBBCGlobals();
	parse_bbc(false);

	$old_method = getOldMethod();

	$new_method = getNewMethod($parser);

	$messages = getMessages(isset($input['msg']) ? $input['msg'] : null);

	$results = array();
	foreach ($messages as $k => $message)
	{
		$old_result = $old_method($message);
		$new_result = $new_method($message);

		$pass = $old_result === $new_result;

		$results[$k] = array(
			'pass' => $pass,
			'message' => $message,
			// I hate wasting memory like this, but datatables complains about colspan
			//'return' => $pass ? array('old' => $old_result) : array('old' => $old_result, 'new' => $new_result),
			'return' => array('old' => $old_result, 'new' => $new_result),
		);

		if (!$pass && $input['fatal'])
		{
			return $results;
		}
	}

	return $results;
}

// @todo randomize which goes first
function benchmark($input)
{
	$messages = getMessages(isset($input['msg']) ? $input['msg'] : null);
	$iterations = $input['iterations'];

	$results = array(
		'num_messages' => count($messages),
		'iterations' => $iterations,
		//'total_time' => array('old' => 0, 'new' => 0),
	);

	setupOldParseBBCGlobals();

	// This needs to run first to even the playing field.
	// Of course old will always win here.
	$results['codes'] = array(
		'old' => runBenchmark(function (){
			parse_bbc(false);
		}, $iterations),
		'new' => runBenchmark(function (){
			new Codes;
		}, $iterations),
	);

	// Setup the BBC for the new method
	$parser = new Parser(new Codes);

	$methods = array(
		'old' => function () use($messages, $iterations) {
			return runBenchmark(function () use ($messages) {
				foreach($messages as $message)
				{
					parse_bbc($message);
				}
			}, $iterations);
		},
		'new' => function () use ($messages, $parser, $iterations) {
			return runBenchmark(function () use ($messages, $parser) {
				foreach($messages as $message)
				{
					$parser->parse($message);
				}
			}, $iterations);
		},
	);

	shuffle_assoc($methods);

	// Now the messages
	/*foreach ($methods as $name => $method)
	{
		$results['all'][$name] = $method();
	}*/

	$methods = array(
		'old' => function ($message) use($iterations) {
			return runBenchmark(function () use ($message) {
				return parse_bbc($message);
			}, $iterations, true);
		},
		'new' => function ($message) use ($parser, $iterations) {
			return runBenchmark(function () use (&$message, $parser) {
				return $parser->parse($message);
			}, $iterations, true);
		},
	);

	// Individual messages to see if there is one that is screwing things up
	foreach ($messages as $i => $message)
	{
		// Every message is a new test
		shuffle_assoc($methods);

		foreach ($methods as $name => $method)
		{
			$results[$i][$name] = $method($message);
		}

		$results[$i]['pass'] = $results[$i]['old']['result'] === $results[$i]['new']['result'];
		$results[$i]['message'] = $message;
	}

	// Setup the diffs
	foreach ($results as &$result)
	{
		if (!is_array($result))
		{
			continue;
		}

		// Figure out the order of the test
		$order = array();
		foreach ($result as $attr => $dummy)
		{
			if (in_array($attr, array('new', 'old')))
			{
				$order[] = $attr;
			}
		}
		$result['order'] = implode(',', $order);

		$result['time_diff'] = $result['old']['total_time'] - $result['new']['total_time'];
		$result['time_winner'] = $result['old']['total_time'] > $result['new']['total_time'] ? 'new' : 'old';

		if ($result['old']['total_time'] == 0)
		{
			$result['time_diff_percent'] = 0;
		}
		else
		{
			$result['time_diff_percent'] = round(($result['time_diff'] / $result['old']['total_time']) * 100, 2);
		}

		$result['mem_diff'] = max($result['old']['memory_usage'], $result['new']['memory_usage']) - min($result['old']['memory_usage'], $result['new']['memory_usage']);
		$result['mem_winner'] = $result['old']['memory_usage'] > $result['new']['memory_usage'] ? 'new' : 'old';

		$result['peak_mem_diff'] = max($result['old']['memory_peak_after'], $result['new']['memory_peak_after']) - min($result['old']['memory_peak_after'], $result['new']['memory_peak_after']);
		$result['peak_mem_winner'] = $result['old']['memory_peak_after'] > $result['new']['memory_peak_after'] ? 'new' : 'old';
	}

	return $results;
}

function setupOldParseBBCGlobals()
{
	global $bbc_codes, $itemcodes, $no_autolink_tags;
	global $disabled, $default_disabled, $parse_tag_cache;

	$bbc_codes = array();
	$itemcodes = array();
	$no_autolink_tags = array();
	$disabled = null;
	$default_disabled = null;
	$parse_tag_cache = null;
}

function resetParseTagCache()
{
	$GLOBALS['parse_tag_cache'] = null;
}

function getOldMethod()
{
	return function ($message) {
		return parse_bbc($message);
	};
}

function getNewMethod($parser)
{
	return function ($message) use ($parser){
		return $parser->parse($message);
	};
}

function getMessages($msg_id = null)
{
	$messages = require 'Messages.php';

	if ($msg_id !== null)
	{
		// Get a list of messages
		if (is_array($msg_id))
		{
			foreach ($messages as $k => $v)
			{
				if (!in_array($k, $msg_id))
				{
					unset($messages[$k]);
				}
			}
		}
		// Get a single message
		elseif (isset($messages[$msg_id]))
		{
			$messages = array($msg_id => $messages[$msg_id]);
		}
		else
		{
			$messages = array();
		}
	}

	return $messages;
}

function runVSBenchmark($name, callable $old, callable $new)
{
	$results = array(
		'old' => runBenchmark($old),
		'new' => runBenchmark($new),
	);
}

function runBenchmark($callback, $iterations = ITERATIONS, $save_result = false)
{
	$diagnostics = array(
		'iterations' => $iterations,
		'memory_before' => memory_get_usage(),
		'memory_peak_before' => memory_get_peak_usage(),
		'time_before' => microtime(true),
	);

	for ($i = 0; $i < $iterations; $i++)
	{
		// This is here because parse_bbc() has a $parse_tag_cache which is normally static
		// but we made it global for the purpose of this test. So because it will attempt
		// to cache the $parse_tags, it will artificially be faster than the new method.
		resetParseTagCache();

		if ($save_result)
		{
			if (!isset($result))
			{
				$result = $callback();
			}
			else
			{
				$callback();
			}
		}
		else
		{
			$callback();
		}
	}

	$diagnostics['result'] = isset($result) ? $result : null;

	$diagnostics['time_after'] = microtime(true);
	$diagnostics['memory_after'] = memory_get_usage();
	$diagnostics['memory_peak_after'] = memory_get_peak_usage();

	// @todo make sure this isn't less
	$diagnostics['memory_usage'] = $diagnostics['memory_after'] - $diagnostics['memory_before'];
	$diagnostics['total_time'] = round($diagnostics['time_after'] - $diagnostics['time_before'], 6);

	return $diagnostics;
}

function debug()
{
	if (!DEBUG)
	{
		return;
	}

	$args = func_get_args();

	foreach ($args as $arg)
	{
		var_dump($arg);
	}
}

// because shuffle doesn't have a shuffle_assoc()
function shuffle_assoc(&$array)
{
	$keys = array_keys($array);

	shuffle($keys);

	foreach($keys as $key)
	{
		$new[$key] = $array[$key];
	}

	$array = $new;

	return true;
}
