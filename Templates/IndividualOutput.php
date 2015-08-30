<?php
$num_tests = count($results['tests']);

?>
Test A: <?= $input['tests']['a'] ?>
Number of tests: <?= $num_tests ?><br>
<form method="get">
	<input type="hidden" name="type" value="individual">
	<input type="hidden" name="a" value="<?= $input['tests']['a'] ?>">
	<table class="table table-striped table-bordered table-condensed" data-page-length="1000">
		<colgroup>
			<col class="col-md-1">
			<col class="col-md-2">
			<col class="col-md-4">
			<col class="col-md-5">
		</colgroup>
		<thead>
		<tr>
			<th>#</th>
			<th>Message</th>
			<th>Result</th>
			<th>Codes Used</th>
		</tr>
		</thead>

		<tbody>
		<?php
		foreach ($results['tests'] as $test_num => $result)
		{
			echo '<!-- TEST #', $test_num, ' -->
 		<tr>
		<th scope="row" class="form-group"><label><input type="checkbox" name="msg[]" value="', $test_num, '">&nbsp;', $test_num, '</label></th>
		<td>
			<div class="code">', htmlspecialchars($result['message']), '</div>
		</td>
		<td>
			<pre class="code">', htmlspecialchars($result['result']), '</pre>
		</td>

		<td>
			<pre class="code">', isset($GLOBALS['codes_used'][$result['message']]) ? htmlspecialchars(print_r($GLOBALS['codes_used'][$result['message']], true)) : '<i>NONE</i>', '</pre>
			<pre class="code">', isset($GLOBALS['codes_used_count'][$result['message']]) ? htmlspecialchars(print_r($GLOBALS['codes_used_count'][$result['message']], true)) : '<i>NONE</i>', '</pre>
		</td>';
			echo '</tr>';
			echo '<!-- // END TEST #', $test_num. ' -->';
		}
		?>
		</tbody>
	</table>

	<button type="submit">Submit</button>
</form>