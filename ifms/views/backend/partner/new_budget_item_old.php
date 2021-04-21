<?php $fy_range = range(date('y')-2,date('y')+2);
$this_fy = get_fy(date("Y-m-d"),$this->session->center_id);
?>
<div class="row">
	<div class="col-sm-12">
		<?php echo form_open('', array('class' => 'form-horizontal form-groups-bordered validate'));?>
			<div>
				<label for="" class="control-label col-sm-4">Choose FY: </label>
				<div class="col-sm-6">
					<select id="choose_yr" class="form-control" name="cur_fy">
						<option><?=get_phrase('select');?></option>
						<?php
							foreach($fy_range as $fy){
						?>
							<option value="<?=$fy;?>">FY<?=$fy;?></option>
						<?php
							}
						?>
					</select>
				</div>
				<div class="col-sm-2">
					<div class="badge badge-info"><?php echo "Current FY is FY".$this_fy;?></div>
				</div>
			</div>
		</form>	
	</div>
</div>

<hr />

<?php

$inc_acc = $this->finance_model->budgeted_revenue_accounts();
?>
<div id="load_create_item" class="hidden">
<div class="panel panel-primary">
						
		<div class="panel-heading">
			<div class="panel-title"><?=get_phrase('new_budget_item');?></div>						
		</div>
								
		<div class="panel-body" style="overflow-y: auto;">

			<?php echo form_open(base_url() . 'ifms.php/partner/create_budget_item/'.$this->session->center_id , array('id'=>'frm_schedule','class' => 'form-horizontal form-groups-bordered validate', 'enctype' => 'multipart/form-data'));?>
								
								
				<!-- <div class="form-group">
									
				<div class="col-sm-12" style="overflow-x: scroll;">
					<table class="table table-striped display">
						<thead>
							<tr>
								<th><?=get_phrase('revenue_account');?></th>
								<th><?=get_phrase('financial_year');?></th>
								<th><?=get_phrase('limit');?></th>
								<th><?=get_phrase('new');?></th>
								<th><?=get_phrase('submitted');?></th>
								<th><?=get_phrase('approved');?></th>
								<th><?=get_phrase('declined');?></th>
								<th><?=get_phrase('total');?></th>
								<th><?=get_phrase('status');?></th>
							</tr>
						</thead>
						<tbody>
							<?php
								$limits = $this->db->get_where('plans_limits',array('fy'=>$cur_fy,'icpNo'=>$this->session->center_id))->result_object();
								
								foreach($limits as $limit):
									
									$rev_rec = $this->db->get_where('accounts',array('accID'=>$limit->revenue_id))->row();
									
							?>
								<tr>
									<td><?=$rev_rec->AccText;?></td>
									<td><?=$limit->fy;?></td>
									<td><?=number_format($limit->amount,2);?></td>
									<?php
										$chk_limit = $this->finance_model->limits_status_check($limit->revenue_id,$this->session->center_id,$limit->fy);
									?>
									<td><?=number_format($this->finance_model->plans_annual_totals($limit->fy,$this->session->center_id,$rev_rec->accID,0),2);?></td>
									<td><?=number_format($this->finance_model->plans_annual_totals($limit->fy,$this->session->center_id,$rev_rec->accID,1),2);?></td>
									<td><?=number_format($this->finance_model->plans_annual_totals($limit->fy,$this->session->center_id,$rev_rec->accID,2),2);?></td>
									<td><?=number_format($this->finance_model->plans_annual_totals($limit->fy,$this->session->center_id,$rev_rec->accID,3),2);?></td>
									<td><?=number_format($this->finance_model->plans_annual_totals($limit->fy,$this->session->center_id,$rev_rec->accID),2);?></td>
									<td><span class="label label-<?=$chk_limit->color?>"><?=$chk_limit->msg.' - ['.number_format($chk_limit->dif,2).']';?></span></td>
								</tr>
							
							<?php
								
								endforeach;
							?>
						
						</tbody>
					</table>
				</div>
			</div> -->
			
			
			<div class="form-group">
				<label class="control-label col-sm-4"><?=get_phrase('account');?></label>
						<div class="col-sm-7">
							<select name="AccNo" id="expense_id" class="form-control expense_id" data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>">
								<option value=""><?=get_phrase('select');?></option>
								<?php
											
										
									foreach($inc_acc as $inc):
								?>
								<optgroup label="<?=$inc->AccText." - ".$inc->AccName;?>">
									<?php
										$exp_acc = $this->finance_model->expense_accounts($inc->accID);
												
										foreach($exp_acc as $exp):
									?>
										<option value="<?=$exp->AccNo;?>"><?=$exp->AccText." - ".$exp->AccName;?></option>
									<?php
										endforeach;
									?>
										</optgroup>
									<?php
										endforeach;
									?>
								</select>
							</div>
						</div>	
						
						
						
						<div class="form-group">
							<label class="control-label col-sm-4"><?=get_phrase('budget_tag');?></label>
								<div class="col-sm-7">
									<select class="form-control" name="plan_item_tag_id" id="plan_item_tag_id">
										<option><?=get_phrase('select');?></option>
										<?php
											$cluster_id = $this->db->get_where('users',array('fname'=>$this->session->center_id))->row()->cname;
											$tags = $this->db->get_where('plan_item_tag',array('cname'=>$cluster_id,'status'=>'1'))->result_object();
												
											foreach($tags as $row):
										?>
											<option value="<?=$row->plan_item_tag_id;?>"><?=$row->name;?></option>
										<?php
											endforeach;
										?>											
									</select>
								</div>
							</div>		
							
							
						<div class="form-group">
									<label class="control-label col-sm-4"><?=get_phrase('description');?></label>
									<div class="col-sm-7">
										<input type="text" name="details" id="details" class="form-control details"  data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>"/>
									</div>
								</div>
								
								<div class="form-group">
									<label class="control-label col-sm-4"><?=get_phrase('financial_year');?></label>
									<div class="col-sm-7">
										<input type="text" value="" name="fy" id="fy" readonly="readonly" class="form-control" data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>"/>
									</div>
								</div>
								
								<div class="form-group">
									<label class="control-label col-sm-4"><?=get_phrase('quantity');?></label>
									<div class="col-sm-7">
										<input type="text" id="qty" name="qty" class="form-control header qty"  data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>"/>
									</div>
								</div>
								
								<div class="form-group">
									<label class="control-label col-sm-4"><?=get_phrase('unit_cost');?></label>
									<div class="col-sm-7">
										<input type="text" id="unitCost" name="unitCost" class="form-control header unitCost"  data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>"/>
									</div>
								</div>
								
								<div class="form-group">
									<label class="control-label col-sm-4"><?=get_phrase('often');?></label>
									<div class="col-sm-7">
										<input type="text" id="often" name="often" class="form-control header often"  data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>"/>
									</div>
								</div>
								
								<div class="form-group">
									<label class="control-label col-sm-4"><?=get_phrase('total');?></label>
									<div class="col-sm-7">
										<input type="text" id="totalCost" readonly="readonly" name="totalCost" class="form-control header totalCost"  data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>"/>
									</div>
								</div>	
			
								<?php
									$months = $this->finance_model->months_in_year($this->session->center_id);
								?>
								
								<table class="table">
									<thead>
										<tr>
											<th colspan="12"><?=get_phrase('monthly_spread');?></th>
										</tr>
										<tr>
											<th><?=get_phrase('action');?></th>
											<th style="text-align: center;"><?=$months['month_1_amount'];?></th>
											<th style="text-align: center;"><?=$months['month_2_amount'];?></th>
											<th style="text-align: center;"><?=$months['month_3_amount'];?></th>
											<th style="text-align: center;"><?=$months['month_4_amount'];?></th>
											<th style="text-align: center;"><?=$months['month_5_amount'];?></th>
											<th style="text-align: center;"><?=$months['month_6_amount'];?></th>
											<th style="text-align: center;"><?=$months['month_7_amount'];?></th>
											<th style="text-align: center;"><?=$months['month_8_amount'];?></th>
											<th style="text-align: center;"><?=$months['month_9_amount'];?></th>
											<th style="text-align: center;"><?=$months['month_10_amount'];?></th>
											<th style="text-align: center;"><?=$months['month_11_amount'];?></th>
											<th style="text-align: center;"><?=$months['month_12_amount'];?></th>
										</tr>
									</thead>
									
									<tbody>
										<tr>
											<td><div class="btn btn-warning" id="clear_spread">Clear</div></td>
											<td><input value="0" type="text" class="form-control spread Month_1"  data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>" name="month_1_amount" id="month_1_amount" style="min-width: 80px;" /></td>
											<td><input value="0" type="text" class="form-control spread Month_2"  data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>" name="month_2_amount" id="month_2_amount" style="min-width: 80px;" /></td>
											<td><input value="0" type="text" class="form-control spread Month_3"  data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>" name="month_3_amount" id="month_3_amount" style="min-width: 80px;" /></td>
											<td><input value="0" type="text" class="form-control spread Month_4"  data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>" name="month_4_amount" id="month_4_amount" style="min-width: 80px;" /></td>
											<td><input value="0" type="text" class="form-control spread Month_5"  data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>" name="month_5_amount" id="month_5_amount" style="min-width: 80px;" /></td>
											<td><input value="0" type="text" class="form-control spread Month_6"  data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>" name="month_6_amount" id="month_6_amount" style="min-width: 80px;" /></td>
											<td><input value="0" type="text" class="form-control spread Month_7"  data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>" name="month_7_amount" id="month_7_amount" style="min-width: 80px;" /></td>
											<td><input value="0" type="text" class="form-control spread Month_8"  data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>" name="month_8_amount" id="month_8_amount" style="min-width: 80px;" /></td>
											<td><input value="0" type="text" class="form-control spread Month_9"  data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>" name="month_9_amount" id="month_9_amount" style="min-width: 80px;" /></td>
											<td><input value="0" type="text" class="form-control spread Month_10" data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>" name="month_10_amount" id="month_10_amount" style="min-width: 80px;" /></td>
											<td><input value="0" type="text" class="form-control spread Month_11" data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>" name="month_11_amount" id="month_11_amount" style="min-width: 80px;" /></td>
											<td><input value="0" type="text" class="form-control spread Month_12" data-validate='required' data-message-required="<?php echo get_phrase('value_required');?>" name="month_12_amount" id="month_12_amount" style="min-width: 80px;" /></td>
										</tr>
										<tr>
											<td colspan="13">
												<textarea name="notes" id="notes" class="form-control ckeditor" cols="10" rows="5" placeholder="<?=get_phrase('write_your_notes_here');?>"></textarea>
											</td>
										</tr>
									</tbody>
								</table>
							
							<div class="form-group">
									<div class="col-sm-12">
										<div id="error_msg" style="color:red;"></div>
									</div>
								</div>
							
								<div class="form-group">
									<div class="col-offset-4 col-sm-8">
										<button id="create_item" class="btn btn-primary"><?=get_phrase('create');?></button>	
									</div>
								</div>
			
						
			</form>
			
		</div>
	</div>
</div>	
	

<script>
	//$(document).ready(function(){
		$('.spread').keyup(function(e){			
			
		});
		
		
		$('.header').keyup(function(e){
			
			var spread=0;
			var sum = 0;
			var sum_spread = 0;
			var sum_header = 0;
			
			if($(this).attr('id')==='qty'){	
				sum = $(this).val()*$('.unitCost').val()*$('.often').val();
				
				$('.totalCost').val(sum);
				
				spread = sum/12;
				
				$('.spread').each(function(){
					$(this).val(spread);
				})
			}
			
			if($(this).attr('id')==='unitCost'){
				sum = $(this).val()*$('.qty').val()*$('.often').val();
				
				$('.totalCost').val(sum);
				
				spread = sum/12;
				
				$('.spread').each(function(){
					$(this).val(spread);
				})
			}
			
			if($(this).attr('id')==='often'){
				sum = $(this).val()*$('.unitCost').val()*$('.qty').val();
				
				$('.totalCost').val(sum);
				
				spread = sum/12;
				
				$('.spread').each(function(){
					$(this).val(spread);
				})
			}
				

		});
		
		$('#create_item').click(function(ev){	
			$('#error_msg').html();
			
			if($('#expense_id').val().length===0){
				$('#error_msg').html('<?php echo get_phrase('error:_account_missing');?>');
				ev.preventDefault();
			}
			
			if($('#details').val().length===0){
				$('#error_msg').html('<?php echo get_phrase('error:_description_missing');?>');
				ev.preventDefault();
			}		
			
			if($('#fy').val().length===0){
				$('#error_msg').html('<?php echo get_phrase('error:_financial_year_missing');?>');
				ev.preventDefault();
			}
			
			if($('#qty').val().length===0){
				$('#error_msg').html('<?php echo get_phrase('error:_quantity_missing');?>');
				ev.preventDefault();
			}
			
			if($('#unitCost').val().length===0){
				$('#error_msg').html('<?php echo get_phrase('error:_unit_cost_missing');?>');
				ev.preventDefault();
			}
			
			if($('#often').val().length===0){
				$('#error_msg').html('<?php echo get_phrase('error:_frequency_missing');?>');
				ev.preventDefault();
			}
			
			if($('#totalCost').val().length===0){
				$('#error_msg').html('<?php echo get_phrase('error:_total_cost_missing');?>');
				ev.preventDefault();
			}
			
			var cnt = 0;
			
			$('.spread').each(function(index){
				
				if($(this).val().length===0){
					cnt++;	
				}
				
			});
			
			if(cnt>0){
				$('#error_msg').html('<?php echo get_phrase('error:_spread_missing');?>');
				ev.preventDefault();
			}
			
			var spread = 0;
			$('.spread').each(function(index){
				spread += +$(this).val();
			});
			
			var total = $('#totalCost').val();
			
			//alert(Math.ceil(spread));
			
			if(Math.ceil(spread)!==Math.ceil(total)){
				$('#error_msg').html('<?php echo get_phrase('error:_spread_incorrect');?>');
				ev.preventDefault();
			}
			
			var formUrL = $("#frm_schedule").attr("action");
			var data = $("#frm_schedule").serializeArray();
			
			$.ajax({
				url:formUrL,
				type:"POST",
				data:data,
				beforeSend:function(){
					
				},
				success:function(resp){
					$("#load_create_item").html(resp);
				},
				error:function(){
					
				}
			});
			
			ev.preventDefault();	    
			
		});
		
		$('#clear_spread').click(function(){
			$('.spread').each(function(index){
				$(this).val('0');
			});
		});
		
		$("#choose_yr").change(function(){
			$("#fy").val($(this).val());
			
			if($("#load_create_item").hasClass("hidden")){
				$("#load_create_item").removeClass("hidden");
			}
		});
		
		
	//});
</script>