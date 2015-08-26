<?php

$lines = file('top_time_diff.csv');

$tests = array();
foreach ($lines as $line)
{
	$result = explode(',', $line);
	$result_size = count($result);
	foreach ($result as $pos => $test)
	{
		$val = $result_size - $pos;
		$tests[(int) $test] = !isset($tests[(int) $test]) ? $val : $val + $tests[(int) $test];
	}
}

arsort($tests);
echo '<pre>
Num tests: ' . count($lines);
foreach ($tests as $num => $test)
{
	echo "\nTest num: $num	with a value of	$test";
}