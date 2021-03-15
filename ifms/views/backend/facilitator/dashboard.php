<div class="row">
	<div class="col-md-12">
		<div class="row">
			<!-- CALENDAR-->
			<div class="col-md-12 col-xs-12">
				<div class="panel panel-primary " data-collapsed="0">
					<div class="panel-heading">
						<div class="panel-title">
							<i class="entypo-gauge"></i>
							<?php echo get_phrase('dashboard'); ?>
						</div>
					</div>
					<div class="panel-body">

						<a class='btn btn-default' target='__blank' href="<?php echo base_url(); ?>ifms.php/facilitator/direct_cash_transfers_report/<?= $tym; ?>"><?php echo get_phrase('direct_cash_transfers_report'); ?></a>

						<hr />
						<div class="row">
							<div class="col-sm-12">
								<div class="btn btn-default pull-right col-sm-3"><a href="#" class="fa fa-backward scroll" id="prev"></a> <?= get_phrase('you_are_in'); ?> <?= date('F Y', $tym); ?> <input type="text" class="form-control col-sm-1" id="cnt" placeholder="<?= get_phrase('enter_number_of_months'); ?>" /> <a href="#" class="fa fa-forward scroll" id="next"></a></div>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-12">
								<table class="table table-striped datatable">
									<thead>
										<tr>
											<th><?= get_phrase('fcp_number'); ?></th>
											<th><?= get_phrase('mfr_submitted'); ?></th>
											<th><?= get_phrase('mfr_submitted_date'); ?></th>
											<th><?= get_phrase('total_fund_balance'); ?></th>
											<th><?= get_phrase('report_validated'); ?></th>
											<th><?= get_phrase('new'); ?></th>
											<th><?= get_phrase('submitted'); ?></th>
											<th><?= get_phrase('declined'); ?></th>
											<th><?= get_phrase('reinstated'); ?></th>
											<th><?= get_phrase('allow_edit'); ?></th>
											<th><?= get_phrase('unapproved_budget'); ?></th>
											<th><?= get_phrase('action'); ?></th>
										</tr>
									</thead>
									<tbody>
										<?php
										foreach ($cluster_fcps as $fcp) {
											$is_mfr_submitted = isset($dashboard_parameters[$fcp]['is_mfr_submitted']) && $dashboard_parameters[$fcp]['is_mfr_submitted'] == 1 ? true : false;
										?>
											<tr>
												<td><?= $fcp; ?></td>
												<td><?= isset($dashboard_parameters[$fcp]['is_mfr_submitted']) && $dashboard_parameters[$fcp]['is_mfr_submitted'] == 1 ? get_phrase('yes') : get_phrase('no'); ?></td>
												<td><?= isset($dashboard_parameters[$fcp]['mfr_submitted_date']) ? $dashboard_parameters[$fcp]['mfr_submitted_date']: ''; ?></td>
												<td><?= number_format(isset($dashboard_parameters[$fcp]['total_fund_balance']) ? $dashboard_parameters[$fcp]['total_fund_balance']: 0,2); ?></td>
												
												<?php
												$is_mfr_validated = true;

												if ((isset($dashboard_parameters[$fcp]['is_not_mfr_validated']) && $dashboard_parameters[$fcp]['is_not_mfr_validated'] == 1) || !isset($dashboard_parameters[$fcp]['is_not_mfr_validated'])) {
													$is_mfr_validated = false;
												}

												$validate_color = 'danger';
												$validation_status = get_phrase('report_not_validate');
												$disable_validation_button = '';

												if(!$is_mfr_submitted){
													$disable_validation_button = 'disabled';
												}

												if ($is_mfr_validated) {
													$validate_color = 'success';
													$validation_status = get_phrase('report_validated');
												}
												?>
												<td>
													<div <?=$disable_validation_button;?> data-month = "<?=date('Y-m-t',$tym);?>" data-fcp = "<?=$fcp;?>" data-validated = "<?=$is_mfr_validated;?>" class="btn btn-<?= $validate_color;?> validate"><?= $validation_status; ?></div>
												</td>

												<?php
												$new = isset($dashboard_budget[$fcp][0]) ? $dashboard_budget[$fcp][0] : 0;
												$submitted = isset($dashboard_budget[$fcp][1]) ? $dashboard_budget[$fcp][1] : 0;
												$declined = isset($dashboard_budget[$fcp][3]) ? $dashboard_budget[$fcp][3] : 0;
												$reinstated = isset($dashboard_budget[$fcp][4]) ? $dashboard_budget[$fcp][4] : 0;
												$allow_edit = isset($dashboard_budget[$fcp][5]) ? $dashboard_budget[$fcp][5] : 0;

												$total_unapproved_budget = $new + $submitted + $declined + $reinstated + $allow_edit;
												?>
												<td><?= number_format($new, 2); ?></td>
												<td><?= number_format($submitted, 2); ?></td>
												<td><?= number_format($declined, 2); ?></td>
												<td><?= number_format($reinstated, 2); ?></td>
												<td><?= number_format($allow_edit, 2) ?></td>
												<td><?= number_format($total_unapproved_budget, 2); ?></td>
												<td>
													<?php include "include_pf_dashboard_report.php"; ?>
												</td>
											</tr>
										<?php
										}
										?>
									</tbody>
								</table>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>



<script>
	$(document).ready(function() {

		var datatable = $('.table').DataTable({
			stateSave: true
		});


		$('.scroll').on('click', function(ev) {

			var cnt = $('#cnt').val();

			if (cnt === "") {
				cnt = "1";
			}

			var dt = '<?php echo $tym; ?>';

			var flag = $(this).attr('id');

			var url = '<?php echo base_url(); ?>ifms.php/facilitator/dashboard/' + dt + '/' + cnt + '/' + flag;

			$(this).attr('href', url);
		});

	});

	$(document).on('click','.validate',function(){
			var month = $(this).data('month');
			var fcp = $(this).data('fcp');
			var validated = $(this).data('validated');
			var url = "<?=base_url();?>ifms.php/facilitator/validate_mfr";
			var data = {'month':month,'fcp':fcp,'validated':validated};
			var btn = $(this);

			if(btn.hasClass('btn-success')){
				var cnfm = confirm('Are you sure you want to invalidate this report?');

				if(!cnfm) {
					alert('Process aborted');
					return false;
				}
			}
			

			$.post(url,data,function(response){
				//alert(response);
				if(response){
					if(btn.hasClass('btn-danger')){
						btn.removeClass('btn-danger').addClass('btn-success');
						btn.html('<?=get_phrase("report_validated");?>');
						btn.data('validated',1);
					}
				}else{
					if(btn.hasClass('btn-success')){
						btn.removeClass('btn-success').addClass('btn-danger');
						btn.html('<?=get_phrase("report_not_validated");?>');
						btn.data('validated',0);
					}
				}
			});

		});
</script>