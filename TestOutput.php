<?php
$num_tests = count($results);
$num_pass = 0;
foreach ($results as $result)
{
	if ($result['pass'])
	{
		$num_pass++;
	}
}
$num_fail = $num_tests - $num_pass;
?>
Tests: <?= $num_tests ?><br>
Pass: <?= $num_pass ?><br>
Fail: <?= $num_fail ?><br>
<form method="get" action="index.php?type=test">
	<input type="hidden" name="type" value="test">
	<table class="table table-striped table-bordered table-condensed" data-page-length="1000">
		<colgroup>
			<col class="col-md-1">
			<col class="col-md-3">
			<col class="col-md-4">
			<col class="col-md-4">
		</colgroup>
		<thead>
		<tr>
			<th>#</th>
			<th>Message</th>
			<th>Old Result</th>
			<th>New Result</th>
		</tr>
		</thead>

		<tbody>
		<?php
		foreach ($results as $test_num => $result)
		{
			echo '<!-- TEST #', $test_num, ' -->';
			echo $result['pass'] ? '<tr>' : '<tr class="danger">';
			echo '
		<th scope="row" class="form-group"><input type="checkbox" name="msg[]" value="', $test_num, '">&nbsp;<label for="msg">', $test_num, '</label></th>
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
			<div class="code">', htmlspecialchars($result['return']['old']), '</div>
		</td>
		<td>
			<div class="code">', htmlspecialchars($result['return']['new']), '</div>
		</td>';
			echo '</tr>';
			echo '<!-- // END TEST #', $test_num. ' -->';
		}
		?>
		</tbody>
	</table>

	<button type="submit">Submit</button>
</form>