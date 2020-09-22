<style>
	@media screen{
		.print-only{
       		 display: none;
    	}
	}
    
	@media print {
		
		 @page {size: A1 landscape;max-height:100%; max-width:100%}
		
	    div.break 
	    	{
	    		page-break-after: always;
	    	},
    	.hidden-print 
    		{
		    	display: none !important;
		  	},
		 .print-only
		 	{
            	display: block;
        	}
	    
	}
	.popover{ min-width: 750px; }
</style>
<?php
//echo date($fyr)."<br/>";
//echo $project."<br/>";

//echo $this->finance_model->fy_start_date(date('Y-m-d'));

?>
<div class="row">
	<div class="col-sm-12" style="text-align: center;">
		<div class="col-sm-offset-3 col-sm-6 well well-sm"><h4><?=$project;?> <?=get_phrase('financial_year');?> <?=$fyr?> <?=get_phrase('budget');?></h3></div>
							
	</div>
</div>

<div class="row">
<div class="col-sm-12">
	<div class="panel panel-success">
						
		<div class="panel-heading">
			<!--<div class="panel-title"><?=get_phrase('project_budget');?></div>	-->					
				<div class="panel-options">
					<ul class="nav nav-tabs">	
						<li class=""><a href="#budget-limit" data-toggle="tab"><?=get_phrase('budget_limit');?></a></li>				
						<li class=""><a href="#budget-summary" data-toggle="tab"><?=get_phrase('budget_summary');?></a></li>
						<li class="active"><a href="#budget-schedules" data-toggle="tab"><?=get_phrase('budget_schedules');?></a></li>												
					</ul>
				</div>
		</div>
								
		<div class="panel-body" style="overflow-y: auto;overflow-x: auto;">

			<button onclick="PrintElem('#tab-content');" class="btn btn-success btn-icon pull-left"><i class="fa fa-print"></i><?=get_phrase('print');?></button>
			
			<a href="<?=base_url();?>ifms.php/facilitator/dashboard/<?=$tym;?>" class="btn btn-primary btn-icon pull-right"><i class="fa fa-arrow-left"></i><?=get_phrase('back');?></a>
			
					
			<br/><hr/>
			
			<div class="tab-content" id="tab-content">
			<?php
				if($this->finance_model->budget_exists($project,$fyr)==='yes'){
			?>	
			
			<div class="tab-pane" id="budget-limit">
					<div class="print-only" style="margin-top: 25%;">
						 <h1 style="text-align:center;font-weight: bolder;font-size: 60pt;"><?=$project;?> - <?=$this->db->get_where('users',array('fname'=>$project))->row()->fname;?></h1> <br/>
						 
						 <h2 style="text-align:center;font-weight: bolder;font-size: 60pt;"><?php echo get_phrase('annual_budget');?></h2><br/>
						 
						 <h2 style="text-align:center;font-weight: bolder;font-size: 60pt;"><?=get_phrase('for_the_fiscal_year')." ".$fyr;?></h2>
				
					</div>
					<!--<button onclick="PrintElem('#limit');" class="btn btn-info btn-icon"><i class="fa fa-print"></i><?=get_phrase('print');?></button>-->
					
					<div class="break"></div>
					
					<div class="print-only">
						<div class="row col-sm-8">
						<span style="font-size: 25pt;">
							<?php
								echo $this->db->get_where('settings',array('type'=>'budget_commitment'))->row()->description;
							?>
						</span>
						
						<span style="font-weight: bold;font-size: 25pt;"><?=get_phrase('signed_herein_below_by_us,_the_project_representatives:');?></span>
					
							<table class="table table-striped" style="width: 100%;font-size: 25pt;">
								<thead>
									<tr>
										<th style="width: 20%;"><?=get_phrase('role');?></th>
										<th><?=get_phrase('name');?></th>
										<th style="width: 20%;"><?=get_phrase('date');?></th>
										<th style="width: 20%;"><?=get_phrase('signature');?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									for($i=0;$i<3;$i++):
										$id = $i+1;
									?>
										<tr>
											<td><?=get_phrase('project_manager')." ".$id;?></td>
											<td>&nbsp;</td>
											<td><?=date('d-m-Y');?></td>
											<td>&nbsp;</td>
										</tr>
									<?php
									endfor;
									?>
									
								</tbody>
							</table>
					</div>
					</div>
					
					<div class="break"></div>
					
						<div class="pull-left hidden-print">
							<button onclick="new_budget_limit();" class="btn btn-primary btn-icon"><i class="fa fa-plus-square"></i><?=get_phrase('new_budget_limit');?></button>
							<button onclick="new_budget_limit();" class="btn btn-warning btn-icon"><i class="fa fa-plus-square"></i><?=get_phrase('new_budget_tag');?></button>
						</div>
						
						<br/><hr/>
					
					<div id="limit">
					
					<span style="font-weight: bold;font-size: 15pt;"><?=get_phrase('budget_limits');?></span>
					
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
								$limits = $this->db->get_where('plans_limits',array('fy'=>$fyr,'icpNo'=>$project))->result_object();
								//print_r($limits);
								foreach($limits as $limit):
									
									$rev_rec = $this->db->get_where('accounts',array('accID'=>$limit->revenue_id))->row();
									
							?>
								<tr>
									<td><?=$rev_rec->AccText;?> - <?=$rev_rec->AccName;?></td>
									<td><?=$limit->fy;?></td>
									<td style="text-align: right;"><?=number_format($limit->amount,2);?></td>
									<?php
										$chk_limit = $this->finance_model->limits_status_check($limit->revenue_id,$project,$limit->fy);
									?>
									<td style="text-align: right;"><?=number_format($this->finance_model->plans_annual_totals($limit->fy,$project,$limit->revenue_id,0),2);?></td>
									<td style="text-align: right;"><?=number_format($this->finance_model->plans_annual_totals($limit->fy,$project,$limit->revenue_id,1),2);?></td>
									<td style="text-align: right;"><?=number_format($this->finance_model->plans_annual_totals($limit->fy,$project,$limit->revenue_id,2),2);?></td>
									<td style="text-align: right;"><?=number_format($this->finance_model->plans_annual_totals($limit->fy,$project,$limit->revenue_id,3),2);?></td>
									<td style="text-align: right;"><?=number_format($this->finance_model->plans_annual_totals($limit->fy,$project,$limit->revenue_id),2);?></td>
									<td><span class="label label-<?=$chk_limit->color?>"><?=$chk_limit->msg.' - ['.number_format($chk_limit->dif,2).']';?></span></td>
								</tr>
							<?php
								endforeach;
							?>
						</tbody>
					</table>
					</div>
				</div>
				
				
				
				<div class="break"></div>
				
				<div class="tab-pane" id="budget-summary">
						<!--<button onclick="PrintElem('#summary')" class="btn btn-info btn-icon"><i class="fa fa-print"></i><?=get_phrase('print');?></button>-->
						
						<div id="summary">
						
						<?php
							$rev_accs = $this->finance_model->budgeted_revenue_accounts();
							//print_r($rev_accs);
							foreach($rev_accs as $rev):
						?>
						<span style="font-weight: bold;font-size: 15pt;"><?=$rev->AccText;?> - <?=$rev->AccName;?> <?=get_phrase('budget_summary');?></span>
						
						<table class="table table-striped table-bordered display">
				
							<thead>
								<tr>
									<th><?=get_phrase('revenue_account');?></th>
									<th><?=get_phrase('annual_total');?></th>
									<?php
										$months = $this->finance_model->months_in_year($project);
										
										$cnt_months = range(1, 12);
										
										foreach($cnt_months as $cnt):
									?>
										<th style="text-align: center;"><?=$months['month_'.$cnt."_amount"];?></th>												
										
									<?php
										endforeach;
									?>
						
								</tr>
							</thead>
							<tbody>
								<?php
									$exp_accs = $this->finance_model->expense_accounts($rev->accID);
									
									foreach($exp_accs as $exp):
										if($this->finance_model->plans_per_account($fyr,$project,$exp->AccNo)>0){
								?>
									<tr>
										<td><?=$exp->AccText;?></td>
										<td style="font-weight: bold;text-align: right;"><?=number_format($this->finance_model->plans_per_account($fyr,$project,$exp->AccNo),2)?></td>
										
								<?php
									foreach($cnt_months as $spread):
								?>
									<td><?=number_format($this->finance_model->plans_per_account_per_month($fyr,$project,$exp->AccNo,$spread),2);?></td>								
								<?php
									endforeach;
								?>		
										
									</tr>
								<?php
									}
									endforeach;	
								?>
								
							</tbody>
							<tfoot>
								<tr>
									<td style="font-weight: bold;"><?=get_phrase('total');?></td>
									<td style="text-align: right;font-weight: bold;"><?=number_format($this->finance_model->plans_annual_totals($fyr,$project,$rev->accID),2);?></td>
									
									<?php
										foreach($cnt_months as $spread_footer):
									?>
										<td><?=number_format($this->finance_model->plans_per_month($fyr,$project,$rev->accID,$spread_footer),2);?></td>
									<?php
										endforeach;
									?>
									
								</tr>
							</tfoot>
							
						</table>
						<?php
							endforeach;
						?>
					</div>
				</div>
				
				
				
				<div class="tab-pane active" id="budget-schedules">
					
				
				<div id="schedules">
					<?php
						$budgeted_revenue_accounts = $this->finance_model->budgeted_revenue_accounts();
						
						foreach($budgeted_revenue_accounts as $budgeted):
					?>
					<span style="font-weight: bold;font-size: 15pt;"><?=$budgeted->AccText;?> - <?=$budgeted->AccName;?> <?=get_phrase('budget_schedules');?></span>
					
					<table class="table table-striped table-bordered display" id="tbl_plans" style="width: 100%;">

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
											$months = $this->finance_model->months_in_year(date('Y-m-d',$tym),true);
												
											foreach($months as $month):
										?>
											<th><?=$month;?></th>
										<?php
											endforeach;
										?>
									<th><?=get_phrase('last_action_date');?></th>
									<th><?=get_phrase('status');?></th>
								</tr>
							</thead>
							<tbody>
								
								<?php
										$expense_accounts = $this->finance_model->expense_accounts($budgeted->accID);
										//print_r($expense_accounts);
											
										foreach($expense_accounts as $expense):
											if($this->finance_model->plans_per_account($fyr,$project,$expense->AccNo)>0){
								?>
									<tr class="table-active"><td class="schedule_header" colspan="21"><?=$expense->AccText.' - '.$expense->AccName;?></td></tr>
									
									<?php
											$schedules = $this->db->join('planheader','planheader.planHeaderID=plansschedule.planHeaderID')->get_where('plansschedule',array('AccNo'=>$expense->AccNo,'fy'=>$fyr,'icpNo'=>$project))->result_object();
											
											foreach($schedules as $schedule):
												
												$color_code = "";
												
												switch($schedule->approved):
												
													case 0:
														
														$color_code = "btn-primary";
														break;
														
													case 1:
														
														$color_code = "btn-info";
														break;
													
													case 2:	
														
														$color_code = "btn-success";
														break;
													
													case 3:	
														
														$color_code = "btn-danger";
														break;	
													
													case 4:	
														
														$color_code = "btn-warning";
														break;	
														
													case 5:	
														
														$color_code = "btn-default";
														break;			
														
													default:
													
													$color_code = "";		
												
												endswitch;
												
									?>
											
											<tr class="tr-schedule" id="schedule_<?=$schedule->scheduleID;?>">
												<td nowrap="nowrap" id="td-<?=$schedule->scheduleID;?>">
										
													<div class="btn-group">
									                    <button id="btn-<?=$schedule->scheduleID;?>" type="button" class="btn btn-sm dropdown-toggle <?=$color_code;?>" data-toggle="dropdown">
									                        <?php echo get_phrase('action');?> <span class="caret"></span>
									                    </button>
									                    <ul class="dropdown-menu dropdown-default pull-left" role="menu">
									                   		<?php
									                   			if($schedule->approved==='1' || $schedule->approved==='4'){
									                   		?>
									                   		
									                   		<li>
									                        	<!--<a href="#" onclick="confirm_action('<?php echo base_url();?>ifms.php/facilitator/plans/<?=$tym;?>/<?=$project;?>/approve/<?php echo $schedule->scheduleID;?>');">-->
									                            <a href="#" id="app-<?=$schedule->scheduleID;?>" class="action_item">
									                            	<i class="fa fa-check"></i>
																		<?php echo get_phrase('approve');?>
									                            </a>
									                        </li>
									                        
									                        <li class="divider"></li>
									                      
									                   		
									                        <li>
									                        	<!--<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_decline_budget_item/<?=$tym;?>/<?=$project;?>/<?php echo $schedule->scheduleID;?>');">-->
									                        	<a href="#" id="dec-<?=$schedule->scheduleID;?>" class="action_item">	
									                            	<i class="fa fa-times"></i>
																		<?php echo get_phrase('decline');?>
									                               	</a>
									                        </li>
									                        
									                        
									                   		<li class="divider"></li>
									                   		<?php
																}elseif($schedule->approved==='2'){
															?>
															<li>
									                        	<a href="#" id="dec-<?=$schedule->scheduleID;?>" class="action_item">
									                            	<i class="fa fa-pencil"></i>
																		<?php echo get_phrase('allow_review');?>
									                               	</a>
									                        </li>
									                        
									                   		<li class="divider"></li>
															<?php		
																}
									                   		?>
									                   											                      
									                   		
									                        <li>
									                        	<!--<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_unacceptable_budget_item/<?=$tym;?>/<?=$project;?>/<?php echo $schedule->scheduleID;?>');">-->
									                            <a href="#" id="unc-<?=$schedule->scheduleID;?>" class="action_item">
									                            	<i class="fa fa-trash-o"></i>
																		<?php echo get_phrase('unacceptable');?>
									                               	</a>
									                        </li>
									                        
									                       														
															<li class="divider"></li>
									                   	
									                    </ul>
									                  </div> 
									                 		
									                  <a href="#" onclick="return false;" id="com-<?=$schedule->scheduleID;?>" class="btn btn-white action_popup">
									                    <i class="fa fa-arrow-circle-o-right"></i>
																<?php echo get_phrase('comments');?>
									                  </a>
									    
									                 <?php if($schedule->notes) {?>
																<i style="color:green;cursor:pointer;" id="not-<?=$schedule->scheduleID;?>" class="fa fa-book notes"></i>
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
												<?php
													$approval = array('new','submitted','approved','declined/allow review','reinstated','unacceptable');
												?>
												<td><?=ucfirst($approval[$schedule->approved]);?></td>
											</tr>
											<tr>
												<td colspan="22" class="print-only"><?=$schedule->notes;?></td>
											</tr>
									
									<?php
									
											endforeach;
									?>	
								
								<?php
								
											}
										endforeach;	
								?>
									
							
							</tbody>
						</table>	
					
					<?php
						endforeach;
					?>
					
				</div>	
			
				
				</div>	
				
			
			<?php
				}else{
					echo get_phrase('no_budget_items_available');				
				}
			?>
			
			</div>			
		</div>
	</div>

</div>
</div>

<script>


function new_budget_item(elem){
	showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_new_budget_item/'+$('#scrolled_fy').val());
	
}
	$('.scroll-fy').click(function(){
		var fy = $(this).siblings('input').val();
		
		if($(this).attr('id')==='next_fy'){
			$(this).siblings('input').val(parseInt(fy)+1); 
		}else{
			$(this).siblings('input').val(parseInt(fy)-1);
		}
		
		
		$(this).attr('href','<?php echo base_url();?>ifms.php/partner/scroll_plan/'+$(this).siblings('input').val());
		
	});

    function PrintElem(elem)
    {
        $(elem).printThis({ 
		    debug: false,              
		    importCSS: true,             
		    importStyle: true,         
		    printContainer: false,       
		    loadCSS: "", 
		    pageTitle: "<?php echo get_phrase('budget_schedules');?>",             
		    removeInline: false,        
		    printDelay: 333,            
		    header: null,             
		    formValues: true          
		});
    }

$('a').click(function(){
	//alert('Test');
	location.hash = $(this).closest('tr').attr('id');
	//alert($(this).closest('tr').attr('id'));
});

$(document).ready(function(){
	
	$('th,schedule_header').css('font-weight','bold');
	
	
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
	
/**
	$('table.display').DataTable({
			dom: '<Bf><"col-sm-12"rt><ip>',
		    pagingType: "full_numbers",
		    buttons: [
		         'csv', 'excel', 'print'
		    ],
		    stateSave: true
		});
**/		
	
	
});

    function PrintElem(elem)
    {
        $(elem).printThis({ 
		    debug: false,              
		    importCSS: true,             
		    importStyle: true,         
		    printContainer: false,       
		    loadCSS: "", 
		    pageTitle: ".",             
		    removeInline: false,        
		    printDelay: 333,            
		   	header:null,            
		    formValues: true          
		});
    }
    
  $('.action_item').click(function(ev){
  	
  	var recid = $(this).attr('id');
	var url = '<?=base_url();?>ifms.php/facilitator/action_item/'+recid;
	var item_id = recid.substring(4);

	$.ajax({
		url:url,
		beforeSend:function(){
			$('#td-'+item_id).html('<img width="90" src="<?php echo base_url();?>uploads/preloader2.gif" />');
		},
		success:function(response){
			//alert(response);
			var obj = JSON.parse(response);
			var msg = obj.msg;
			var view = obj.view;
			var status = obj.status;

			$('#td-'+item_id).html(view);
			$('#td-'+item_id).parent().find('td:last').html(status);
		
			alert(msg);
		},
		error:function(){
			
		}
	});
	  	
  	ev.preventDefault();
  });  
  
  
  	
	$(document).ready(function(){ 

		$('a.action_popup').popover({  
		      title:'Comments',  
		      content:fetchData,
		      html:true,  
		      placement:'right'  
		}); 
	
            
           
           
           function fetchData(){  
                var fetch_data = '';  
                var element = $(this);  
                var id = element.attr("id");  
                
                $.ajax({  
                     url:"<?=base_url();?>ifms.php/facilitator/budget_comments",  
                     method:"POST",  
                     async:false,  
                     data:{id:id},  
                     success:function(data){  
                          fetch_data = data;  
                     }  
                });  
                return fetch_data;  
           } 
           
           
           
           
       $('.notes').popover({  
		      title:'Notes',  
		      content:fetchNotes,
		      html:true,  
		      placement:'bottom'  
		}); 
	
            
           
           
           function fetchNotes(){  
                var fetch_data = '';  
                var element = $(this);  
                var id = element.attr("id");  
                
                $.ajax({  
                     url:"<?=base_url();?>ifms.php/facilitator/budget_notes",  
                     method:"POST",  
                     async:false,  
                     data:{id:id},  
                     success:function(data){  
                          fetch_data = data;  
                     }  
                });  
                return fetch_data;  
           }  
      });  
	 
	

</script>
		