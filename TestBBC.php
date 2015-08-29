<?php

namespace BBC;

require_once './Tests/BBCTest.php';

class TestBBC
{
	const MAX_ITERATIONS = 10000;

	protected $input = array();
	protected $results = array();
	protected $methods = array();
	protected $msg_path = 'Messages.php';
	protected $messages = array();
	protected $save_result = true;
	protected $iterations = 10000;
	protected $tests = array();

	public function __construct(array $input)
	{
		$this->setInput($input);
	}

	public function setInput($input)
	{
		$this->globalSettings();
		$this->input = $input;

		$this->tests = $this->getPossibleTests();

		$this->setMethods($input['tests']['a'], $input['tests']['b']);
		$this->messages = $this->setMessages(isset($input['msg']) ? $input['msg'] : null);
		$this->iterations = isset($input['iterations']) ? min((int) $input['iterations'], self::MAX_ITERATIONS) : $this->iterations;
		$this->save_result = isset($input['save_result']) ? (bool) $input['save_result'] : $this->save_result;

		return $this;
	}

	public function setMethods($a, $b)
	{
		$this->methods['a'] = $this->tests[$this->input['tests']['a']]['object'];
		$this->methods['b'] = $this->tests[$this->input['tests']['b']]['object'];
	}

	public function getPossibleTests($test_dir_path = 'Tests')
	{
		$test_dir = new \DirectoryIterator($test_dir_path);
		$tests = array();
		foreach ($test_dir as $dir)
		{
			$test_file = $dir->getPath() . '/' . $dir->getFilename() . '/Test.php';
			$filename = $dir->getFilename();

			if ($dir->isDir() && $filename[0] !== '.' && file_exists($test_file))
			{
				require_once $test_file;
				$classname = 'BBC\\Tests\\' . $filename . '\\Test';
				$test = new $classname;
				$name = $test->getName();

				$tests[$name] = array(
					'name' => $name,
					'filename' => $test_file,
					'classname' => $classname,
					'object' => $test,
				);
			}
		}

		return $tests;
	}

	public function globalSettings()
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
			'smiley_set' => false,
			'name' => 'what\'s in',
		);

		if (!defined('SUBSDIR'))
		{
			define('SUBSDIR', __DIR__);
		}
	}

	public function getResults()
	{
		return $this->results;
	}

	public function individual()
	{
		$this->results = array(
			'messages' => $this->messages,
			'num_messages' => count($this->messages),
			'tests' => array(),
		);

		$object = $this->methods['a'];

		if (is_callable(array($object, 'setup')))
		{
			$object->setup();
		}

		foreach ($this->messages as $i => $message)
		{
			$GLOBALS['current_message_number'] = $i;
			$GLOBALS['current_message'] = $message;

			if (is_callable($object, 'beforeMessage'))
			{
				$object->beforeMessage();
			}

			$this->results['tests'][$i] = array(
				'message' => $message,
				'result' => $object->parseMessage($message),
			);

			if (is_callable($object, 'afterMessage'))
			{
				$object->afterMessage();
			}
		}
	}

	public function benchmark()
	{
		$this->results = array(
			'messages' => $this->messages,
			'num_messages' => count($this->messages),
			'iterations' => $this->iterations,
			'tests' => array(),
		);

		// Do the setup
		foreach ($this->methods as $letter => $method)
		{
			if (is_callable(array($method, 'setup')))
			{
				$time = microtime(true);
				$method->setup();
				$this->results['setup_time'][$letter] = microtime(true) - $time;
			}
			else
			{
				$this->results['setup_time'][$letter] = false;
			}
		}


		// Test the codes
		$this->codes();

		// Test the messages
		// Individual messages to see if there is one that is screwing things up
		foreach ($this->messages as $i => $message)
		{
			$GLOBALS['current_message_number'] = $i;
			$GLOBALS['current_message'] = $message;

			$this->results['tests'][$i] = array(
				'message' => $message,
				'pass' => true,
				'time_diff' => 0,
				'time_winner' => '',
				'order' => '',
				'a' => array(),
				'b' => array(),
			);

			// Every message is a new test
			//shuffle_assoc($this->methods);

			foreach ($this->methods as $letter => $method)
			{
				$this->results['tests'][$i][$letter] = $this->runBenchmark($method, $message);
			}

			if ($this->save_result)
			{
				$this->results['tests'][$i]['pass'] = $this->results['tests'][$i]['a']['result'] === $this->results['tests'][$i]['b']['result'];
				$this->results['tests'][$i]['time_diff'] = $this->results['tests'][$i]['a']['total_time'] - $this->results['tests'][$i]['b']['total_time'];
			}

			// Figure out the order of the test
			$order = array();
			foreach ($this->methods as $letter => $dummy)
			{
				$order[] = $letter;
			}
			$this->results['tests'][$i]['order'] = implode(',', $order);

			$this->results['tests'][$i]['time_winner'] = $this->results['tests'][$i]['a']['total_time'] > $this->results['tests'][$i]['b']['total_time'] ? 'b' : 'a';
			if ($this->results['tests'][$i]['a']['total_time'] == 0)
			{
				$this->results['tests'][$i]['time_diff_percent'] = 0;
			}
			else
			{
				$this->results['tests'][$i]['time_diff_percent'] = round(($this->results['tests'][$i]['time_diff'] / $this->results['tests'][$i]['a']['total_time']) * 100, 2);
			}
		}
	}

	protected function setup()
	{
		foreach ($this->methods as $letter => $method)
		{
			if (is_callable(array($method, 'setup')))
			{
				$time = microtime(true);
				$method->setup();
				$this->results['setup_time'][$letter] = microtime(true) - $time;
			}
			else
			{
				$this->results['setup_time'][$letter] = false;
			}
		}
	}

	protected function destroy()
	{
		foreach ($this->methods as $letter => $method)
		{
			if (is_callable(array($method, 'destroy')))
			{
				$time = microtime(true);
				$method->destroy();
				$this->results['setup_time'][$letter] = microtime(true) - $time;
			}
			else
			{
				$this->results['setup_time'][$letter] = false;
			}
		}
	}

	protected function codes()
	{
		foreach ($this->methods as $letter => $method)
		{
			if (is_callable(array($method, 'codes')))
			{
				if (is_callable(array($method, 'beforeCodes')))
				{
					$method->beforeCodes();
				}

				$time = microtime(true);
				$method->setup();
				$results['tests']['codes'][$letter] = microtime(true) - $time;

				if (is_callable(array($method, 'afterCodes')))
				{
					$method->afterCodes();
				}
			}
			else
			{
				$results['tests']['codes'][$letter] = false;
			}
		}
	}

	protected function runBenchmark(\BBC\Tests\BBCTest $object, $message)
	{
		$diagnostics = array(
			'memory_before' => memory_get_usage(),
			'memory_peak_before' => memory_get_peak_usage(),
			'time_before' => microtime(true),
		);

		for ($i = 0; $i < $this->iterations; $i++)
		{
			// This isn't very fair. If we need to call this, we add overhead.
			// Then again, the only time this should happen is for poorly written code, so I guess it deserves it heh
			if (is_callable($object, 'beforeMessage'))
			{
				$object->beforeMessage();
			}

			if ($this->save_result)
			{
				if (!isset($result))
				{
					$result = $object->parseMessage($message);
				}
				else
				{
					$object->parseMessage($message);
				}
			}
			else
			{
				$object->parseMessage($message);
			}

			if (is_callable($object, 'afterMessage'))
			{
				$object->afterMessage();
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

	protected function setMessages($msg_id)
	{
		$messages = require $this->msg_path;

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

	public function tests()
	{
		$this->iterations = 1;
		$this->benchmark();
	}
}