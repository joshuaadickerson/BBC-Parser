<?php
$total_old_time = 0;
$total_new_time = 0;

$stack = array();
$stack_max_len = 5;
$stack_len = 0;

foreach ($results as $i => $result)
{
	if (!is_array($result))
	{
		continue;
	}

	$total_old_time += $result['old']['total_time'];
	$total_new_time += $result['new']['total_time'];

	if (defined('SAVE_TOP_RESULTS') && SAVE_TOP_RESULTS)
	{
		if (count($stack) < $stack_max_len + 1)
		{
			$stack_len++;
			$stack[$i] = $result['time_diff_percent'];
		}
		else
		{
			foreach ($stack as $k => $v)
			{
				if ($v < $result['time_diff_percent'])
				{
					unset($stack[$k]);
					$stack[$i] = $result['time_diff_percent'];
					asort($stack);
					break;
				}
			}
		}
	}
}

if (defined('SAVE_TOP_RESULTS') && SAVE_TOP_RESULTS)
{
	asort($stack);
	file_put_contents('top_time_diff_percent.csv', implode(array_keys($stack), ',') . "\n", FILE_APPEND);
}
?>

<div>
	Messages: <?= $results['num_messages'] ?><br>
	Iterations: <?= $results['iterations'] ?><br>
	Total Time In Tests: <?= round($total_old_time + $total_new_time, 2) ?><br>
	Total Old Time: <?= round($total_old_time, 2) ?><br>
	Total New Time: <?= round($total_new_time, 2) ?><br>
	Diff Total Time: <?= round(abs($total_old_time - $total_new_time), 2) ?><br>
	Diff Total Time %: <?= round((max($total_old_time, $total_new_time) - min($total_old_time, $total_new_time) / max($total_old_time, $total_new_time)), 2) ?><br>
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
		<th>Old Time</th>
		<th>New Time</th>
		<th>Time Diff</th>
		<th>Time Diff %</th>
		<!-- <th>Old Mem</th>
		<th>New Mem</th>
		<th>Mem Diff</th>
		<th>Old Peak Mem</th>
		<th>New Peak Mem</th>
		<th>Mem Peak Diff</th> -->
		<th>Message</th>
	</tr>
	</thead>

	<tbody>
	<?php
	foreach ($results as $test => $result)
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

			<td class="<?= $result['time_winner'] === 'old' ? 'success' : ''?>">
				<?= $result['old']['total_time'] ?>
			</td>
			<td class="<?= $result['time_winner'] === 'new' ? 'success' : ''?>">
				<?= $result['new']['total_time'] ?>
			</td>
			<td><?= round($result['time_diff'], 4) ?></td>
			<td><?= round(($result['time_diff'] / max($result['new']['total_time'], $result['old']['total_time'])) * 100, 2) ?></td>

			<!--<td class="<?= $result['mem_winner'] === 'old' ? 'success' : ''?>">
				<?= $result['old']['memory_usage'] ?>
			</td>
			<td class="<?= $result['mem_winner'] === 'new' ? 'success' : ''?>">
				<?= $result['new']['memory_usage'] ?>
			</td>
			<td><?= $result['mem_diff'] ?></td>

			<td class="<?= $result['peak_mem_winner'] === 'old' ? 'success' : ''?>"><?= $result['old']['memory_peak_after'] ?></td>
			<td class="<?= $result['peak_mem_winner'] === 'new' ? 'success' : ''?>"><?= $result['new']['memory_peak_after'] ?></td>
			<td><?= $result['peak_mem_diff'] ?></td> -->

			<td>
				<?php echo isset($result['message']) ? '<div class="code">' . htmlspecialchars($result['message']) . '</div>' : ''; ?>
			</td>
		</tr>
		<?php
	}
	?>
	</tbody>
</table>

<button type="submit">Submit</button>
</form>