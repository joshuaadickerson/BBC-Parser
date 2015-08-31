<?php

// This will be renamed to index.php as soon as I am ready.

namespace BBC;

// Sanitize inputs
$type = isset($_GET['type']) ? $_GET['type'] : false;

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

// Include the test file
require_once 'TestBBC.php';
$testBBC = new TestBBC;

$possible_tests = $testBBC->getPossibleTests();

$input = array(
	'type' => array(
		'test' => $type === 'test' ? ' selected="selected"' : '',
		'bench' => $type === 'bench' ? ' selected="selected"' : '',
		'individual' => $type === 'individual' ? ' selected="selected"' : '',
	),
	'tests' => array(
		'a' => isset($_GET['a']) && isset($possible_tests[$_GET['a']]) ? $possible_tests[$_GET['a']]['name'] : 'Old parse_bbc',
		'b' => isset($_GET['b']) && isset($possible_tests[$_GET['b']]) ? $possible_tests[$_GET['b']]['name'] : 'Parser',
	),
	'iterations' => isset($_GET['iterations']) ? min($_GET['iterations'], 10000) : 0,
	'debug' => isset($_GET['debug']) && $_GET['debug'] ? 'checked="checked"' : '',
	'fatal' => isset($_GET['fatal']) && $_GET['fatal'] ? 'checked="checked"' : '',
	'msg' => $msgs,
);

$testBBC->setInput($input);

define('SAVE_TOP_RESULTS', true);

// Run the test (based on type)
$test_types = array(
	'test' => 'tests',
	'bench' => 'benchmark',
	'individual' => 'individual',
);

require_once 'BBCHelpers.php';

if (isset($test_types[$type]))
{
	call_user_func(array($testBBC, $test_types[$type]), $input);
	$results = $testBBC->getResults();
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
			require 'Templates/' . ucfirst($type) . 'Output.php';
		}
	}
	?>
</div>
<div class="modal" id="controls" tabindex="-1" role="dialog" aria-labelledby="controlsLabel">
	<form class="modal-dialog" role="document" method="get">
		<div class="modal-content form-horizontal">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Controls</h4>
			</div>
			<div class="modal-body form-horizontal">
				<div class="form-group">
					<label for="type" class="col-sm-4 control-label">Type of test to run</label>
					<div class="col-sm-8">
						<select name="type" class="form-control">
							<option value="test" <?= $input['type']['test'] ?>>Test</option>
							<option value="bench" <?= $input['type']['bench'] ?>>Benchmark</option>
							<option value="individual" <?= $input['type']['individual'] ?>>Individual</option>
						</select>

					</div>
				</div>
				<div class="form-group">
					<label for="a" class="col-sm-4 control-label">Test A</label>
					<div class="col-sm-8">
						<select name="a" class="form-control">
						<?php

						foreach ($possible_tests as $possible)
						{
							echo '
								<option value="', $possible['name'], '"', $possible['name'] == $input['tests']['a'] ? ' selected' : '' , '>', $possible['name'], '</option>';
						}

						?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="b" class="col-sm-4 control-label">Test B</label>
					<div class="col-sm-8">
						<select name="b" class="form-control">
							<?php

							foreach ($possible_tests as $possible)
							{
								echo '
								<option value="', $possible['name'], '"', $possible['name'] == $input['tests']['b'] ? ' selected' : '' , '>', $possible['name'], '</option>';
							}

							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="iterations" class="col-sm-4 control-label">Number of iterations</label>
					<div class="col-sm-8">
						<input name="iterations" type="text" value="<?= $testBBC->getIterations() ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="msg" class="col-sm-4 control-label">Comma separated list of message ids to parse (blank for all)</label>
					<div class="col-sm-8">
						<input name="msg" type="text" value="<?= isset($input['msg']) && is_array($input['msg']) ? implode(',', $input['msg']) : '' ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="fatal" class="col-sm-4 control-label">End tests if one fails</label>
					<div class="col-sm-8">
						<input name="fatal" type="checkbox" <?= $input['fatal'] ?> class="form-control">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Save changes</button>
			</div>
		</div><!-- /.modal-content -->
	</form><!-- /.modal-dialog -->
</div>

<div id="request_time" class="pull-right">
	Total request time: <?= round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 4) ?>
</div>

<script>
	$(document).ready(function(){
		$('.table').DataTable({
			columnDefs: [
				{ type: 'natural', targets: 0 }
			]
		});
	});</script>
</body>
</html>