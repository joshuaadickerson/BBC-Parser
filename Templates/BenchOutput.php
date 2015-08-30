<?php
$total_a_time = 0;
$total_b_time = 0;

$stack = array();
$stack_max_len = 5;
$stack_len = 0;

foreach ($results['tests'] as $i => $result)
{
	$total_a_time += $result['a']['total_time'];
	$total_b_time += $result['b']['total_time'];

	if (defined('SAVE_TOP_RESULTS') && SAVE_TOP_RESULTS)
	{
		if (count($stack) < $stack_max_len + 1)
		{
			$stack_len++;
			$stack[$i] = $result['time_diff'];
		}
		else
		{
			foreach ($stack as $k => $v)
			{
				if ($v > $result['time_diff'])
				{
					unset($stack[$k]);
					$stack[$i] = $result['time_diff'];
					arsort($stack);
					break;
				}
			}
		}
	}
}

if (defined('SAVE_TOP_RESULTS') && SAVE_TOP_RESULTS)
{
	asort($stack);
	file_put_contents('top_time_diff.csv', implode(array_keys($stack), ',') . "\n", FILE_APPEND);
}

$total_diff = abs($total_a_time - $total_b_time);
$total_percent = $total_diff > 0 ? round(($total_a_time - $total_b_time) / $total_a_time * 100, 2) : 0;
?>

<div>
	Messages: <?= $results['num_messages'] ?><br>
	Iterations: <?= $results['iterations'] ?><br>
	Total Time In Tests: <?= round($total_a_time + $total_b_time, 2) ?><br>
	Total Time A (<?= $input['tests']['a'] ?>): <?= round($total_a_time, 2) ?><br>
	Total Time B (<?= $input['tests']['b'] ?>): <?= round($total_b_time, 2) ?><br>
	Diff Total Time: <?= $total_diff ?><br>
	Diff Total Time %: <?= $total_percent ?><br>
</div>

<form>
	<input type="hidden" name="type" value="bench">
	<input type="hidden" name="iterations" value="<?= ITERATIONS ?>">
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