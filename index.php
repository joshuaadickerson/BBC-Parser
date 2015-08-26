<?php
namespace BBC;

// Sanitize inputs
$type = isset($_GET['type']) ? $_GET['type'] : false;

if (isset($_GET['msg']))
{
	if (is_array($_GET['msg']))
	{
		$msgs = array();
		foreach ($_GET['msg'] as $msg)
		{
			$msgs[] = (int) $msg;
		}
		$msgs = array_unique($msgs);
	}
	else
	{
		$msgs = $_GET['msg'];
	}
}
else
{
	$msgs = null;
}

$tests = array(
	'parse_bbc',
	'spuds_parse_bbc',
	'parser',
	'regexparser',
);

$input = array(
	'type' => array(
		'test' => $type === 'test' ? ' selected="selected"' : '',
		'bench' => $type === 'bench' ? ' selected="selected"' : '',
		'codes' => $type === 'codes' ? ' selected="selected"' : '',
		'individual' => $type === 'individual' ? ' selected="selected"' : '',
	),
	'tests' => array(
		'a' => isset($_GET['a']) && in_array($_GET['a'], $tests) ? $_GET['a'] : $tests[0],
		'b' => isset($_GET['b']) && in_array($_GET['b'], $tests) ? $_GET['b'] : $tests[1],
	),
	'iterations' => isset($_GET['iterations']) ? min($_GET['iterations'], 10000) : 0,
	'debug' => isset($_GET['debug']) && $_GET['debug'] ? 'checked="checked"' : '',
	'fatal' => isset($_GET['fatal']) && $_GET['fatal'] ? 'checked="checked"' : '',
	'msg' => $msgs,
);

// Setup those constants for the test file
define('ITERATIONS', $input['iterations']);
define('DEBUG', !empty($input['debug']));
define('FAILED_TEST_IS_FATAL', !empty($input['fatal']));
define('SAVE_TOP_RESULTS', true);

// Run the test (based on type)
$test_types = array(
	'test' => 'tests',
	'bench' => 'benchmark',
	'individual' => 'individual',
	//'codes' => 'codes',
);

// Include the test file
require_once 'Tester.php';

if (isset($test_types[$type]))
{
	define('TEST_TYPE', $type);
	$results = call_user_func('\BBC\\' . $test_types[$type], $input);
}

?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>BBC Parser Test</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

	<script src="//cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
	<script src="//cdn.datatables.net/plug-ins/1.10.8/sorting/natural.js"></script>

	<!--
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.7/styles/default.min.css">
	<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.7/highlight.min.js"></script>
	<script>hljs.initHighlightingOnLoad();</script>
	-->
	<style>
		.code {
			height: auto;
			max-height: 10em;
			overflow: auto !important;
			word-break: normal !important;
			word-wrap: normal !important;
			width: 30em;
			margin-bottom: .5em;
		}
	</style>

</head>
<body>
<div class="container-fluid">
	<div id="top">
		<button type="button" class="btn btn-primary btn-lg pull-right" data-toggle="modal" data-target="#controls"><i class="glyphicon glyphicon-cog"></i> Controls</button>
		<h1>BBC Parser Test</h1>
	</div>
	<?php

	// No results to display
	if (empty($results))
	{
		?><div>
		No results to display. Click the "Controls" button to run tests.
		<pre class="well"><?= htmlspecialchars('<html><body>something</body></html>'); ?></pre>
		</div><?php
	}
	// We have results
	else
	{
		if (isset($test_types[$type]))
		{
			require ucfirst($type) . 'Output.php';
		}
	}
	?>
</div>
<div class="modal" id="controls" tabindex="-1" role="dialog" aria-labelledby="controlsLabel">
	<form class="modal-dialog" role="document" method="get">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Controls</h4>
			</div>
			<div class="modal-body form-horizontal">
				<div class="form-group">
					<div class="col-sm-10">
						<label for="type">Type of test to run
							<select name="type" class="form-control">
								<option value="test" <?= $input['type']['test'] ?>>Test</option>
								<option value="bench" <?= $input['type']['bench'] ?>>Benchmark</option>
								<!-- <option value="code" <?= $input['type']['codes'] ?>>Codes</option> -->
							</select>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="fatal">End tests if one fails</label>
					<input name="fatal" type="checkbox" <?= $input['fatal'] ?> class="form-control">
			</div>
			</div>
			<div class="form-group">
				<label for="iterations">Number of iterations</label>
				<input name="iterations" type="text" value="<?= $input['iterations'] ?>" class="form-control">
			</div>
			<div class="form-group">
				<label for="msg">Comma separated list of message ids to parse</label>
				<input name="msg" type="text" value="<?= implode(',', $input['msg']) ?>" class="form-control">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Save changes</button>
			</div>
		</div><!-- /.modal-content -->
	</form><!-- /.modal-dialog -->
</div>
<script>
	$(document).ready(function(){
		$('table').DataTable({
			columnDefs: [
				{ type: 'natural', targets: 0 }
			]
		});
	});</script>
</body>
</html>