<?php
$num_tests = count($results['tests']);
$num_pass = 0;
foreach ($results['tests'] as $result)
{
	if ($result['pass'])
	{
		$num_pass++;
	}
}
$num_fail = $num_tests - $num_pass;
?>
Test A: <?= $input['tests']['a'] ?><br>
Test B: <?= $input['tests']['b'] ?><br>
Tests: <?= $num_tests ?><br>
Pass: <?= $num_pass ?><br>
Fail: <?= $num_fail ?><br>
<form action="<?= $_SERVER['REQUEST_URI'] ?>">
	<input type="hidden" name="type" value="test">
	<table class="table table-striped table-bordered table-condensed" data-page-length="1000">
		<colgroup>
			<col class="col-md-1">
			<col class="col-md-2">
			<col class="col-md-2">
			<col class="col-md-2">
			<col class="col-md-5">
		</colgroup>
		<thead>
		<tr>
			<th>#</th>
			<th>Message</th>
			<th>Old Result</th>
			<th>New Result</th>
			<th>Codes Used</th>
		</tr>
		</thead>

		<tbody>
		<?php
		foreach ($results['tests'] as $test_num => $result)
		{
			echo '<!-- TEST #', $test_num, ' -->';
			echo $result['pass'] ? '<tr>' : '<tr class="danger">';
			echo '
		<th scope="row" class="form-group"><label><input type="checkbox" name="msg[]" value="', $test_num, '">&nbsp;', $test_num, '</label></th>
		<td>
			<div class="code">', htmlspecialchars($result['message']), '</div>
		</td>';

			// I really hate outputting both results since they are the same, but datatables complains about colspan
			/*if ($result['pass'])
			{
				echo '
				<td colspan="2">
					<div class="code">', htmlspecialchars($result['return']['old']), '</div>
				</td>';
			}
			else
			{
				echo '
				<td>
					<div class="code">', htmlspecialchars($result['return']['old']), '</div>
				</td>
				<td>
					<div class="code">', htmlspecialchars($result['return']['new']), '</div>
				</td>';
			*/
			echo '
		<td>
			<div class="code">', htmlspecialchars($result['a']['result']), '</div>
		</td>

		<td>
			<div class="code">', htmlspecialchars($result['b']['result']), '</div>
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