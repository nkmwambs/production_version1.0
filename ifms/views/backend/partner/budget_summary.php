<?php
//print_r($month_totals);
?>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-primary">

			<div class="panel-heading">
				<div class="panel-title"><?= get_phrase('budget_summary') ?></div>
			</div>

			<div class="panel-body" style="overflow-y: auto;">

				<div class="row">
					<div class="col-sm-12">
						<button onclick="PrintElem('#summary-chart');" class="btn btn-success btn-icon pull-right"><i class="fa fa-print"></i><?= get_phrase('print'); ?></button>
					</div>

					<div class="col-sm-12">
						<form>
							<div class="form-group">
								<label class="control-label col-sm-3"><?= get_phrase('financial_year'); ?></label>
								<div class="col-sm-4">
									<div class="input-group col-sm-5 col-sm-offset-1">
										<a href="" id="prev_fy" class="input-group-addon scroll-fy"><i class="glyphicon glyphicon-minus"></i></a>
										<input id="scrolled_fy" value="<?= $fyr; ?>" type="text" class="form-control text-center" name="scrolled_fy" placeholder="FY" readonly="readonly">
										<a href="" id="next_fy" class="input-group-addon scroll-fy"><i class="glyphicon glyphicon-plus"></i></a>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>

				<hr />
				<div id="summary-chart">
					<?php
					$budgetable_revenue_accounts = $this->finance_model->budgeted_revenue_accounts();

					foreach ($budgetable_revenue_accounts as $revenue_account) {
					?>
						<span style="font-weight: bold;font-size: 15pt;"><?= $revenue_account->AccText; ?> - <?= $revenue_account->AccName; ?> <?= get_phrase('budget_summary'); ?></span>

						<table class="table table-striped table-bordered display">

							<thead>
								<tr>
									<th><?= get_phrase('revenue_account'); ?></th>
									<?php foreach ($months_range as $month) { ?>
										<th><?= $month; ?></th>
									<?php } ?>
									<th><?= get_phrase('annual_total'); ?></th>
								</tr>
							</thead>
							<?php
							if (isset($budget_summary[$revenue_account->AccNo])) {
								foreach ($budget_summary[$revenue_account->AccNo] as $expense_account => $budget_items) {
							?>
									<tbody>

										<tr>
											<td><?= $expense_account; ?></td>
											<?php
											foreach ($budget_items as $amount) {
											?>

												<td><?= number_format($amount, 2); ?></td>
											<?php
											}
											?>
										</tr>
									<?php
								}

									?>
									</tbody>
									<tfoot>
										<tr>
											<td><?= get_phrase('totals'); ?></td>

											<?php
											$total_per_month = $budget_summary["totals"][$revenue_account->AccNo];
											?>
											<?php foreach (order_of_months_in_fy() as $budget_month) { ?>
												<td><?= number_format(array_sum(array_column($total_per_month['month_spread'], $budget_month)), 2); ?></td>
											<?php } ?>
											<td><?= number_format($total_per_month['grand_total'], 2); ?></td>
										</tr>
									</tfoot>
								<?php } ?>
						</table>

					<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$('.scroll-fy').click(function() {
		var fy = $(this).siblings('input').val();

		if ($(this).attr('id') === 'next_fy') {
			$(this).siblings('input').val(parseInt(fy) + 1);
		} else {
			$(this).siblings('input').val(parseInt(fy) - 1);
		}


		$(this).attr('href', '<?php echo base_url(); ?>ifms.php/partner/scroll_budget_summary/' + $(this).siblings('input').val());

	});

	function PrintElem(elem) {
		$(elem).printThis({
			debug: false,
			importCSS: true,
			importStyle: true,
			printContainer: false,
			loadCSS: "",
			pageTitle: ".",
			removeInline: false,
			printDelay: 333,
			header: null,
			formValues: true
		});
	}
</script>