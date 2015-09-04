<?php

if (defined('SAVE_TOP_RESULTS') && SAVE_TOP_RESULTS)
{
	$testBBC->saveTopResults('top_time_diff.csv', 5, 'time_diff');
}

?>

<div>
	Messages: <?= $results['num_messages'] ?><br>
	Iterations: <?= $results['iterations'] ?><br>
	Total Time In Tests: <?= round($results['totals']['total'], 2) ?><br>
	Total Time A (<?= $input['tests']['a'] ?>): <?= round($results['totals']['a'], 2) ?><br>
	Total Time B (<?= $input['tests']['b'] ?>): <?= round($results['totals']['b'], 2) ?><br>
	Diff Total Time: <?= $results['totals']['diff'] ?><br>
	Diff Total Time %: <?= round($results['totals']['percent'], 2) ?><br>
</div>

<form action="<?= $_SERVER['REQUEST_URI'] ?>">
	<input type="hidden" name="type" value="bench">
	<input type="hidden" name="iterations" value="<?= $testBBC->getIterations() ?>">
<table class="table table-striped table-bordered table-condensed" data-page-length="1000">
	<!--<colgroup>
		<col class="col-md-1">
		<col class="col-md-3">
		<col class="col-md-4">
		<col class="col-md-4">
	</colgroup>-->
	<thead>
	<tr>
		<th>Test</th>
		<th>Order</th>
		<th>Pass</th>
		<th>Time (A)</th>
		<th>Time (B)</th>
		<th>Time Diff</th>
		<th>Time Diff %</th>
		<th>Message</th>
	</tr>
	</thead>

	<tbody>
	<?php
	foreach ($results['tests'] as $test => $result)
	{
		if (!is_array($result))
		{
			continue;
		}

		?>
		<tr>
			<td class="form-group"><label><input type="checkbox" name="msg[]" value="<?= $test ?>">&nbsp;<?= $test ?></label></td>


			<td><?= $result['order'] ?></td>

			<?php
			if (isset($result['pass']))
			{
				echo '<td class="', $result['pass'] ? 'success' : 'danger', '">',  $result['pass'] ? 'pass' : 'fail', '</td>';
			}
			else
			{
				echo '<td></td>';
			}
			?>

			<td class="<?= $result['time_winner'] === 'a' ? 'success' : ''?>">
				<?= $result['a']['total_time'] ?>
			</td>
			<td class="<?= $result['time_winner'] === 'b' ? 'success' : ''?>">
				<?= $result['b']['total_time'] ?>
			</td>
			<td><?= round($result['time_diff'], 4) ?></td>
			<td><?= $result['time_diff_percent'] ?></td>
			<td>
				<?php echo isset($result['message']) ? '<div class="code">' . htmlspecialchars($result['message']) . '</div>' : ''; ?>
				<?php echo isset($result['result']) ? '<div class="code">' . $result['result'] . '</div>' : ''; ?>
			</td>
		</tr>
		<?php
	}
	?>
	</tbody>
</table>

<button type="submit">Submit</button>
</form>