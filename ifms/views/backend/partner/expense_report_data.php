<?php //print_r($budget_spread_grid);
?>
<div id="<?= $acc->AccNo; ?>" class="expense_report">
	<caption class="h4"><?= $acc->AccText; ?> - <?= $acc->AccName; ?> <?= get_phrase('expense_report_as_at_'); ?> <?= $month; ?></caption>
	<table class="table table-striped">
		<thead>
			<tr>
				<th><?= get_phrase('account'); ?></th>
				<th><?= get_phrase('month_expenses'); ?></th>
				<th><?= get_phrase('expense_to_date'); ?></th>
				<th><?= get_phrase('budget_to_date'); ?></th>
				<th><?= get_phrase('variance'); ?></th>
				<th><?= get_phrase('percent_variance'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($expense_account as $acc) :
				$month_expense = isset($expense_report_grid['month_expenses'][$acc->AccNo]) ? $expense_report_grid['month_expenses'][$acc->AccNo] : 0;
				$expense_to_date = isset($expense_report_grid['expense_to_date'][$acc->AccNo]) ? $expense_report_grid['expense_to_date'][$acc->AccNo] : 0;
				$budget_to_date = isset($budget_spread_grid[$acc->AccNo]['total_cost']) ? $budget_spread_grid[$acc->AccNo]['total_cost'] : 0;

				$variance = $budget_to_date - $expense_to_date;
				$percentage_variance = 0;

				if ($budget_to_date > 0 && $expense_to_date > 0) {
					$percentage_variance = round((($variance / $budget_to_date) * 100));
				} elseif ($budget_to_date == 0 && $expense_to_date > 0) {
					$percentage_variance = -100;
				}
			?>
				<tr>
					<td><?= $acc->AccText; ?></td>
					<td><?= number_format($month_expense, 2); ?></td>
					<td><?= number_format($expense_to_date, 2); ?></td>
					<td><?= number_format($budget_to_date, 2); ?></td>
					<td><?= number_format($variance, 2); ?></td>
					<td><?= $percentage_variance; ?>%</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<?php 
					$total_month_expense = isset($expense_report_grid['month_expenses']) ? array_sum($expense_report_grid['month_expenses']) : 0;
					$total_to_date_expense = isset($expense_report_grid['expense_to_date']) ? array_sum($expense_report_grid['expense_to_date']) : 0;
					$total_budget_to_date = array_sum(array_column($budget_spread_grid, 'total_cost'));

					$total_variance = $total_budget_to_date - $total_to_date_expense;

					$total_percentage_variance = 0;

					if ($total_budget_to_date > 0 && $total_to_date_expense > 0) {
						$total_percentage_variance = round((($total_variance / $total_budget_to_date) * 100));
					} elseif ($total_budget_to_date == 0 && $total_to_date_expense > 0) {
						$total_percentage_variance = -100;
					}
				?>
				<td><?= get_phrase('total'); ?></td>
				<td><?= number_format($total_month_expense, 2); ?></td>
				<td><?= number_format($total_to_date_expense, 2); ?></td>
				<td><?= number_format($total_budget_to_date, 2); ?></td>
				<td><?=number_format($total_variance,2);?></td>
				<td><?=$total_percentage_variance;?>%</td>
			</tr>
		</tfoot>
	</table>