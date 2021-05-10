<?php
//Cleared Outstanding Cheques

$mfr_submitted = $this->finance_model->mfr_submitted($this->session->center_id, date('Y-m-d', strtotime($param2)));

$oc = $this->finance_model->outstanding_cheques($param2, $this->session->center_id, FALSE);

//Cleared Deposit in Transit

$dep = $this->finance_model->deposit_transit($param2, $this->session->center_id, FALSE);

?>

<i class="entypo-plus-circled"></i>

<?php echo get_phrase('cleared_effects'); ?>
<hr>

<div class="row">
	<div class="col-sm-12">

		<div class="tabs-vertical-env">

			<ul class="nav tabs-vertical">
				<!-- available classes "right-aligned" -->
				<li class="active"><a href="#oschq" data-toggle="tab">Cleared Outstanding Cheques</a></li>
				<li><a href="#dep" data-toggle="tab">Cleared Deposit In Transit</a></li>

			</ul>


			<div class="tab-content">

				<div class="tab-pane active" id="oschq">

					<div class="panel panel-primary" data-collapsed="0">
						<div class="panel-heading">
							<div class="panel-title">
								<i class="entypo-plus-circled"></i>
								<?php echo get_phrase('cleared_outstanding_cheques'); ?>
							</div>
						</div>
						<div class="panel-body" style="max-width:50; overflow: auto;">

							<table class="table table-hover" id="ocTable">
								<thead>
									<tr>
										<th><?php echo get_phrase('action'); ?></th>
										<th><?php echo get_phrase('date'); ?></th>
										<th><?php echo get_phrase('cleared_date'); ?></th>
										<th><?php echo get_phrase('cheque_number'); ?></th>
										<th><?php echo get_phrase('details'); ?></th>
										<th><?php echo get_phrase('amount'); ?></th>
									</tr>
								</thead>

								<body>
									<?php
									$oc_total = 0;
									foreach ($oc as $row) :
										$chq = explode('-', $row['ChqNo']);


										$style = '';
										$label = 'unclear';
										$disable_btn = '';

										$chq_no=$chq[0];

										if ($row['voucher_reversal_to'] > 0 || $row['voucher_reversal_from'] > 0) {
                                           
											$chq_no=sizeof($chq)==3?$chq[1]:$chq[0];
											
											$style = "color:red";

											$label = 'cancelled_cheque';

											$disable_btn = 'disabled';
										}

										

									?>


										<tr>
											<td><div <?php if($mfr_submitted==='1'){echo "style='display:none;'";};?> class="btn btn-danger chqClr" <?=$disable_btn; ?> id='oc-unclear_<?php echo $row['hID'];?>'><?=get_phrase($label);?></div></td>
											<td> <span style="<?=$style;?>;">  <?php echo $row['TDate']?></span></td>
											<td> <span style="<?=$style;?>;" > <?php  echo $row['clrMonth'] ?></span></td>
											<td> <span style="<?=$style;?>;" > <?php  echo $chq_no;?></span></td>
											<td><span style="<?=$style;?>;" >  <?php echo $row['TDescription']?></span></td>
											<td> <span style="<?=$style;?>;"> <?php echo $row['totals'];?></span></td>
											
										</tr>
									<?php
										$oc_total += $row['totals'];
									endforeach;
									?>
									<tr>
										<td><?php echo get_phrase('total'); ?></td>
										<td id="ocTotal" colspan="4" style="text-align: right;"><?php echo number_format($oc_total, 2); ?></td>
									</tr>
								</body>
							</table>

						</div>
					</div>

				</div>


				<div class="tab-pane" id="dep">

					<!-- Deposits -->

					<div class="panel panel-primary" data-collapsed="0">
						<div class="panel-heading">
							<div class="panel-title">
								<i class="entypo-plus-circled"></i>
								<?php echo get_phrase('cleared_deposits_in_transit'); ?>
							</div>
						</div>
						<div class="panel-body" style="max-width:50; overflow: auto;">

							<table class="table table-hover" id="depTable">
								<thead>
									<tr>
										<th><?php echo get_phrase('action'); ?></th>
										<th><?php echo get_phrase('date'); ?></th>
										<th><?php echo get_phrase('cleared_date'); ?></th>
										<th><?php echo get_phrase('details'); ?></th>
										<th><?php echo get_phrase('amount'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$depTotal = 0;
									foreach ($dep as $row) :
										$style = '';
										$label = 'unclear';
										$disable_btn = '';

										$chq_no=$chq[0];

										if ($row['voucher_reversal_to'] > 0 || $row['voucher_reversal_from'] > 0) {
                                           
											$chq_no=sizeof($chq)==3?$chq[1]:$chq[0];
											
											$style = "color:red";

											$label = 'cancelled_deposit';

											$disable_btn = 'disabled';
										}
									?>
										<tr>

											<td>
												<div <?php if ($mfr_submitted === '1') {
															echo "style='display:none;'";
														}; ?> class="btn btn-danger depClr <?=$disable_btn;?>"  id="dep-unclear_<?php echo $row['hID']; ?>"><?php echo get_phrase($label); ?></div>
											</td>
											<td><span style="<?=$style;?>;"><?php echo $row['TDate'] ?></span></td>
											<td><span style="<?=$style;?>;"><?php echo $row['clrMonth'] ?></span></td>
											<td><span style="<?=$style;?>;"><?php echo $row['TDescription'] ?></span></td>
											<td><span style="<?=$style;?>;"><?php echo $row['totals'] ?></span></td>
										</tr>

									<?php
										$depTotal += $row['totals'];
									endforeach;
									?>
									<tr>
										<td><?php echo get_phrase('total'); ?></td>
										<td id="depTotal" colspan="4" style="text-align: right;"><?php echo number_format($depTotal, 2); ?></td>
									</tr>
								</tbody>
							</table>

						</div>
					</div>

				</div>
			</div>
		</div>

	</div>

</div>

<script>
	$(document).ready(function() {

		//Clear Oustanding Cheques
		$('.chqClr').click(function(ev) {
			var hid = this.id;
			//alert(hid);
			BootstrapDialog.confirm('<?php echo get_phrase("Are_you_sure_you_want_to_reinstate_this_cheque?"); ?>', function(result) {
				if (!result) {
					BootstrapDialog.show({
						title: 'Information',
						message: '<?php echo get_phrase('process_aborted'); ?>'
					});
				} else {

					var url = '<?php echo base_url(); ?>ifms.php/partner/clear_effects/' + hid + '/unclear';
					$.ajax({
						url: url,
						success: function(response) {

							BootstrapDialog.show({
								title: 'Alert',
								message: response
							});

							$('#' + hid).parent().parent().remove();
							var total = 0;
							$("#ocTable tr:gt(0)").each(function() {
								var this_row = $(this);
								var amt = $.trim(this_row.find('td:eq(4)').html())
								total += Number(amt);
							});
							$('#ocTotal').html(total);
							$('#osChq').val(total);
						}
					});

				}
			});

		});



		$('.depClr').click(function(ev) {

			var hid = this.id;

			//alert(hid);

			BootstrapDialog.confirm('<?php echo get_phrase("Are_you_sure_you_want_to_clear_this_deposit?"); ?>', function(result) {
				if (!result) {
					BootstrapDialog.show({
						title: 'Information',
						message: '<?php echo get_phrase('process_aborted'); ?>'
					});
				} else {

					var url = '<?php echo base_url(); ?>ifms.php/partner/clear_effects/' + hid + '/unclear';
					$.ajax({
						url: url,
						success: function(response) {

							BootstrapDialog.show({
								title: 'Alert',
								message: response
							});

							$('#' + hid).parent().parent().remove();
							var total = 0;
							$("#depTable tr:gt(0)").each(function() {
								var this_row = $(this);
								var amt = $.trim(this_row.find('td:eq(3)').html())
								total += Number(amt);
							});
							$('#depTotal').html(total);
							$('#depTransit').val(total);
						}
					});

				}

			});

		});


	});
</script>