<?php
$cnt_months = range(1, 12);
$expense = $this->db->get_where("accounts",array("accID"=>$account_id))->row();
?>

<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-bordered display"  style="width: 100%;">

			<thead>
				<tr>
					<th><?=get_phrase('action');?></th>
					<th><?=get_phrase('details');?></th>
					<th><?=get_phrase('quantity');?></th>
					<th><?=get_phrase('unit_cost');?></th>
					<th><?=get_phrase('frequency');?></th>
					<th><?=get_phrase('total_cost');?></th>
					<th><?=get_phrase('budget_tag');?></th>
					<th><?=get_phrase('validation_check');?></th>
					<?php
						$months = $this->finance_model->months_in_year($this->session->center_id);
												
						foreach($months as $month):
					?>
					<th><?=$month;?></th>
					<?php
						endforeach;
					?>
					<th><?=get_phrase('last_action_date');?></th>
					<!-- <th><?=get_phrase('status');?></th> -->
				</tr>
			</thead>
			<tbody>
				
			<?php
			  
				if($this->finance_model->plans_per_account($fyr,$this->session->center_id,$expense->AccNo) > 0){
			?>
				<tr class="table-active"><td class="schedule_header" colspan="21"><?=$expense->AccText.' - '.$expense->AccName;?></td></tr>
				
				<?php
					$schedules = $this->db->join('planheader','planheader.planHeaderID=plansschedule.planHeaderID')->get_where('plansschedule',array('AccNo'=>$expense->AccNo,'fy'=>$fyr,'icpNo'=>$this->session->center_id))->result_object();
											
					foreach($schedules as $schedule):
												
				?>
										<tr class="tr-schedule">
												<?php
													$approval = array('new','submitted','approved','declined',"reinstated","allow delete");
													//0-Draft,1=Submitted,2=Approved,3=Declined,4=Reinstated,5=Allow Delete
												?>
												<td>
													<div class="btn-group">
									                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
									                        <?php echo get_phrase('action');?> : <?=ucfirst($approval[$schedule->approved]);?> <span class="caret"></span>
									                    </button>
									                    <ul class="dropdown-menu dropdown-default pull-left" role="menu">
									                    	<?php
									                    		if($schedule->approved == 0 || $schedule->approved == 3 || $schedule->approved == 5  ){
									                    	?>
									                    	<li>
									                        	<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_edit_budget_item/<?php echo $schedule->scheduleID;?>/<?php echo $schedule->approved;?>');">
									                            	<i class="fa fa-binoculars"></i>
																		<?php echo get_phrase('edit');?>
									                               	</a>
									                        </li>
									                        
									                        <li class="divider"></li>
									                        <?php
									                        }
															?>
									                    	
									                    	<?php
									                    		if($schedule->approved == 0 || $schedule->approved == 5 ){
									                    	?>
									                   		
									                        
									                       <li class="divider"></li>
									                   	
									                   		<li>
									                        	<a href="#" class="delete_btn" id="del_<?php echo $schedule->scheduleID;?>">
									                            	<i class="fa fa-trash"></i>
																		<?php echo get_phrase('delete');?>
									                               	</a>
									                        </li>
									                    
									                    	    
									                        <li class="divider"></li>
									                   	
									                   		
									                   		<li>
									                        	<a href="#" class="submit_btn" id="submit_<?php echo $schedule->scheduleID;?>">
									                            	<i class="fa fa-send"></i>
																		<?php echo get_phrase('submit');?>
									                               	</a>
									                        </li>
									                        
									                   		<li class="divider"></li>
									                   		
									                   		<?php
									                   		}
									                   		?>
									                   		
									                   		 <!-- <li>
									                        	<a href="#" onclick="confirm_action('<?php echo base_url();?>ifms.php/partner/plans/duplicate/<?php echo $schedule->scheduleID;?>');">
									                            	<i class="entypo-docs"></i>
																		<?php echo get_phrase('duplicate');?>
									                               	</a>
									                        </li>
									                        
									                        <li class="divider"></li>-->
									                   		
									                   		<li>
									                        	<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_view_budget_notes/<?php echo $schedule->scheduleID;?>');">
									                            	<i class="fa fa-book"></i>
																		<?php echo get_phrase('notes');?>
									                               	</a>
									                        </li>
															
															<li class="divider"></li>
									                   	
									                   		
									                   		<li>
									                        	<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_edit_revenue_account/<?php echo $schedule->scheduleID;?>');">
									                            	<i class="fa fa-eye"></i>
																		<?php echo get_phrase('comments');?>
									                               	</a>
									                        </li>
									                    </ul>
									                  </div> 
									                 <?php if($schedule->notes) {?>
																<i style="color:green;cursor:pointer;" class="fa fa-book" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_view_budget_notes/<?php echo $schedule->scheduleID;?>');"></i>
									                 <?php }?>
												</td> 
												<td><?=$schedule->details;?></td>
												<td><?=$schedule->qty;?></td>
												<td><?=$schedule->unitCost;?></td>
												<td><?=$schedule->often;?></td>
												<td class="td-total-cost" style="text-align: right;"><?=$schedule->totalCost;?></td>
												<td><?php
														echo get_phrase("not_specified");;
														if($this->db->get_where('plan_item_tag',array('plan_item_tag_id'=>$schedule->plan_item_tag_id))->num_rows()>0){
															echo $this->db->get_where('plan_item_tag',array('plan_item_tag_id'=>$schedule->plan_item_tag_id))->row()->name;
														}
														
													?>
												</td>
												<td class="validate"></td>
												<?php
												
													foreach($cnt_months as $schedule_spread):
														$alloc = "month_".$schedule_spread."_amount";
												?>
												<td class='schedule-amount' style="text-align: right;"><?=$schedule->$alloc;?></td>

												<?php
													endforeach;
												?>
												<td><?=$schedule->stmp;?></td>
												
												<!-- <td><?=ucfirst($approval[$schedule->approved]);?></td> -->
											</tr>
									
									<?php
									
											endforeach;
									?>	
									
							<tr>
								
							<tr>
								<td colspan="5"><?=get_phrase('account_total');?></td>
								<td style="text-align: right;font-weight: bold;"><?=number_format($this->finance_model->plans_per_account($fyr,$this->session->center_id,$expense->AccNo),2);?></td>
								<td colspan="2"></td>
											
								<td style="font-weight: bold; text-align: right;"><?=number_format($this->finance_model->plans_per_account_per_month($fyr,$this->session->center_id,$expense->AccNo,'1'),2);?></td>
								<td style="font-weight: bold; text-align: right;"><?=number_format($this->finance_model->plans_per_account_per_month($fyr,$this->session->center_id,$expense->AccNo,'2'),2);?></td>
								<td style="font-weight: bold; text-align: right;"><?=number_format($this->finance_model->plans_per_account_per_month($fyr,$this->session->center_id,$expense->AccNo,'3'),2);?></td>
								<td style="font-weight: bold; text-align: right;"><?=number_format($this->finance_model->plans_per_account_per_month($fyr,$this->session->center_id,$expense->AccNo,'4'),2);?></td>
								<td style="font-weight: bold; text-align: right;"><?=number_format($this->finance_model->plans_per_account_per_month($fyr,$this->session->center_id,$expense->AccNo,'5'),2);?></td>
								<td style="font-weight: bold; text-align: right;"><?=number_format($this->finance_model->plans_per_account_per_month($fyr,$this->session->center_id,$expense->AccNo,'6'),2);?></td>
								<td style="font-weight: bold; text-align: right;"><?=number_format($this->finance_model->plans_per_account_per_month($fyr,$this->session->center_id,$expense->AccNo,'7'),2);?></td>
								<td style="font-weight: bold; text-align: right;"><?=number_format($this->finance_model->plans_per_account_per_month($fyr,$this->session->center_id,$expense->AccNo,'8'),2);?></td>
								<td style="font-weight: bold; text-align: right;"><?=number_format($this->finance_model->plans_per_account_per_month($fyr,$this->session->center_id,$expense->AccNo,'9'),2);?></td>
								<td style="font-weight: bold; text-align: right;"><?=number_format($this->finance_model->plans_per_account_per_month($fyr,$this->session->center_id,$expense->AccNo,'10'),2);?></td>
								<td style="font-weight: bold; text-align: right;"><?=number_format($this->finance_model->plans_per_account_per_month($fyr,$this->session->center_id,$expense->AccNo,'11'),2);?></td>
								<td style="font-weight: bold; text-align: right;"><?=number_format($this->finance_model->plans_per_account_per_month($fyr,$this->session->center_id,$expense->AccNo,'12'),2);?></td>
								<td>&nbsp;</td>
							</tr>	
				
			<?php	
				}	
			?>
				
			</tbody>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('.tr-schedule').each(function(){

			var total = 0;	
			
			$(this).find('.schedule-amount').each(function(){
				total += parseFloat($(this).html());
			});
			
			if(parseFloat(total)===parseFloat($(this).find('.td-total-cost').html())){
				$(this).find('.validate').html('<span class="label label-success"><?=get_phrase('ok');?></span>');
			}else{
				$(this).find('.validate').html('<span class="label label-danger"><?=get_phrase('incorrect');?></span>');
			}
			
		});
	});
	
	$(".delete_btn").click(function(ev){
		var cnfrm = confirm("<?php echo get_phrase("Are_you_sure_you_want_to_perform_this_action?");?>");
		
		if(!cnfrm){
			alert("<?=get_phrase("aborted");?>");
		}else{
			var id = $(this).attr("id").split("_");
			var url = "<?=base_url();?>ifms.php/partner/budget_item_delete/"+id[1];
			
			$.ajax({
				url:url,
	  			beforeSend:function(){
	  				$('#schedules').html('<div style="text-align:center;"><img style="width:60px;height:60px;" src="<?php echo base_url();?>uploads/preloader4.gif" /></div>');
	  			},
	  			success:function(resp){
	  				$("#schedules").html(resp);
	  			},
	  			error:function(){
	  				
	  			}
			});
		}
		
	});
	
	$(".submit_btn").click(function(ev){
		var cnf = confirm("<?php echo get_phrase("Are_you_sure_you_want_to_perform_this_action?");?>");
		
		if(!cnf){
			alert("<?=get_phrase("aborted");?>");
		}else{
			var id = $(this).attr("id").split("_");
			var url = "<?=base_url();?>ifms.php/partner/budget_item_submit/"+id[1];
			//alert(id[1]);
			$.ajax({
				url:url,
	  			beforeSend:function(){
	  				$('#schedules').html('<div style="text-align:center;"><img style="width:60px;height:60px;" src="<?php echo base_url();?>uploads/preloader4.gif" /></div>');
	  			},
	  			success:function(resp){
	  				$("#schedules").html(resp);
	  			},
	  			error:function(){
	  				
	  			}
			});
		}
		
	});	
	
</script>

