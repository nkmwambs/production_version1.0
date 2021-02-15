<?php 

$grid_array = $this->dashboard_model->display_dashboard(date('Y-m-t',$month));

$none_requested_params = isset($grid_array['parameters']['no']) ? $grid_array['parameters']['no'] : array();

$requested_params = isset($grid_array['parameters']['yes']) ? $grid_array['parameters']['yes'] : array();
?>

<hr/>

<div class="row">
	
		<div class="col-xs-4">
		
			<a href="#" id='btn_last_month' class='btn btn-success pull-left'><i class='fa fa-angle-left'></i> Previous Month</a>
			
		</div>
		
	   <div class="col-xs-4" style="text-align: center;">
			<span><h4><?php echo date('F Y', $month); ?></h4> </span>
			
	   </div>
	   	<div class="col-xs-4">
			
			<a id='btn_next_month' href="#" class='btn btn-success pull-right'>Next Month <i class='fa fa-angle-right'></i></a>
			
		</div>
	
	
</div>
<?php 

$sum_params = count($none_requested_params) + count($requested_params);

if(empty($none_requested_params) && empty($requested_params)){
 	?>
 	<div class='row'>
 		<div class='col-xs-12'>
 			<div class='well' style="text-align: center;">No Parameters and kindly contact system admin to populate  parameters </div>
 		</div>
 		
 	</div>
 	<?php //break;
		}elseif(empty($grid_array['fcps_with_risks'])){
	?>
		<div class='row'>
 			<div class='col-xs-12'>
 				<div class='well' style="text-align: center;">Dashboard scheduled task has not been run. Please contact the administrator</div>
 			</div>
 		
 		</div>
	<?php		
		}else{	
	?>

<hr/>

<div class='row'>
	<div class='col-xs-12'>
		
		<table>
			<thead>
				
				<tr>
					<th rowspan="2">FCP ID</th>
					<th rowspan="2">Comment</th>
					<th rowspan="2">Risk</th>
					<?php if(!empty($none_requested_params)){?>
					<th colspan="<?= count($none_requested_params); ?>">Non Requested Parameters</th>
					<?php } ?>
					<?php if(!empty($requested_params)){?>
					<th colspan="<?= count($requested_params); ?>">Requested Parameters</th>
					<?php } ?>
				</tr>
				<tr>
				
				<?php 
				
				if(!empty($none_requested_params)){
				 foreach ($none_requested_params as $none_requested_param) {
				 ?>
				     
				     <th><?= $none_requested_param; ?></th>
				 <?php }
						}
				?>
				<!--Requested Parameters-->
				
				<?php 
				if(!empty($requested_params)){
				 foreach ($requested_params as $requested_param) {
				 ?>
				     
				     <th><?= $requested_param; ?></th>
				     
				 <?php }
						}
				?>
				
				</tr>
			</thead>
			
			<tbody>
				<?php 
				 foreach ($grid_array['fcps_with_risks'] as $fcp_id => $value) { 
					if(isset($value['params']) && count($value['params']) == $sum_params){
				?>
				   <tr>
				   	 <td>
				   	 	
				   	 	<div class="btn-group">
							<button id="btn-<?= $fcp_id;?>" type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
						    	<?= $fcp_id;?> <span class="caret"></span>
						    </button>
						    	<ul class="dropdown-menu dropdown-default pull-left" role="menu"> 
										
																
										<li>
											<a target='__blank' href="<?php echo base_url();?>ifms.php/accountant/bank_statements/<?php echo $month;?>/<?=$fcp_id;?>"><?php echo get_phrase('bank_statements');?></a>
										</li>
									<li class="divider"></li>							

									<li>
										<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_outstanding_cheques/<?php echo date('Y-m-01',$month);?>/<?=$fcp_id;?>')"><?php echo get_phrase('outstanding_cheques');?></a>
									</li>
																
									<li class="divider"></li>
																
									<li>
										<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_transit_deposits/<?php echo date('Y-m-t',$month);?>/<?=$fcp_id;?>')"><?php echo get_phrase('deposits_in_transit');?></a>
									</li>
												
									<li class="divider"></li>
									
									<li>
										<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_bank_reconcile/<?php echo date('Y-m-t',$month);?>/<?=$fcp_id;?>')"><?php echo get_phrase('bank_reconciliation');?></a>
									</li>
																
									<li class="divider"></li>
																
									<li>
										<a href="#>" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_variance_explanation/<?php echo date('Y-m-t',$month);?>/<?=$fcp_id;?>')"><?php echo get_phrase('variance_explanation');?></a>
									</li>
																
									<li class="divider"></li>
																
									<li>
										<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_proof_of_cash/<?php echo date('Y-m-t',$month);?>/<?=$fcp_id;?>')"><?php echo get_phrase('proof_of_cash');?></a>
																	
									</li>
																
									<li class="divider"></li>
																
									<li>
										<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_fund_balances/<?php echo date('Y-m-t',$month);?>/<?=$fcp_id;?>')"><?php echo get_phrase('fund_balance_report');?></a>
																	
									</li>
																
									<li class="divider"></li>
																
									<li>
										<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_expense_report/<?php echo date('Y-m-t',$month);?>/<?=$fcp_id;?>')"><?php echo get_phrase('expense_report');?></a>
																	
									</li>
																
									<li class="divider"></li>
																
									<li>
										<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_fund_ratios/<?php echo date('Y-m-t',$month);?>/<?=$fcp_id;?>')"><?php echo get_phrase('financial_ratios');?></a>
																	
									</li>
																
																								
									<li class="divider"></li>
																
										<li>
											<a href="<?php echo base_url();?>ifms.php/accountant/cash_journal/<?php echo $month;?>/<?=$fcp_id;?>"><?php echo get_phrase('cash_journal');?></a>
										</li>
																
									<li class="divider"></li>
																
										<li>
											<a href="<?php echo base_url();?>ifms.php/accountant/plans/<?=$month;?>/<?=$fcp_id;?>"><?php echo get_phrase('budget');?></a>
										</li>
																
									<li class="divider"></li>
						        </ul>
						    </div>	
				   	 </td>
					<td><i class='fa fa-envelope' style='cursor:pointer;' onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_dashboard_messaging/<?php echo date('Y-m-t',$month);?>/<?=$fcp_id;?>')"></i>
						<?=$value['message_sent'] == "Yes"?'<i class="badge badge-secondary">Yes</i>':'';?>
					</td>
				   	 <?php if($value['risk']=='Low'){ ?>
				   	 
				   	 <td style="background-color: green; color: white;"><?= $value['risk']; ?></td>
				   	 
				   	 <?php }elseif($value['risk']=='High'){?>
				   	 <td style="background-color: red; color: white;"><?= $value['risk']; ?></td>
				   	 <?php }else{?>
				   	 	<td style="background-color: orange; color: white;"><?= $value['risk']; ?></td>
				   	<?php }?>
				   	 
				   	 <?php
				   	// if(isset($value['params']) && count($value['params']) == $sum_params){
				   	  foreach (($value['params']) as $param) {
				   	  	if(substr($param,0,3) == "Yes" || $param == 1 ) {
				   	 ?>
				   	   <td style="background-color: green; color: white;"><?=substr($param,0,3) == "Yes"?$param:"Yes";?></td>
				   	   <?php }else if(substr($param,0,3) == 'No' || $param == 0){?>
				   	   	
				   	   	<td style="background-color: red; color: white;"><?= substr($param,0,3) == 'No'?$param:"No";?></td>
				   	   	
				   	   <?php }elseif(strrchr($param,'Yes')){?>
				   	   	 <td style="background-color: green; color: white;"><?= strrchr($param,'Yes')?$param:"Yes";?></td>
				   	   	<?php } elseif(strrchr($param,'No')){?>
				   	   	  	 <td style="background-color: red; color: white;"><?= strrchr($param,'No')?$param:"No";?></td>
				   	   	<?php }elseif(is_numeric($param)){?>
				   	   	
				   	   	 <td><?= number_format($param,2);?></td>
				   	   	 
				   	   	<?php }else{?>
							<td><?= $param;?></td>
						 <?php }
						 }
							//}
				   	  ?>
				   </tr>
				<?php } }?>
			</tbody>
			
		</table>

		

	</div>

</div>
<?php } ?>

<script type="text/javascript">
	jQuery(document).ready(function($) {

		var datatable = $(".datatable").dataTable({
			dom : 'lBfrtip',
			buttons : ['pdf', 'csv', 'excel', 'copy'],
			lengthMenu: [[25,50, 100, 150,-1], [25,50 ,100,150 ,"All"]],
			pageLength: 25
		});

		$(".dataTables_wrapper select").select2({
			minimumResultsForSearch : -1
		});
	});

$('#btn_last_month ,#btn_next_month').on('click',function(ev){
	
	if($(this).attr('id')=='btn_last_month')
	{
		 var href='<?=base_url();?>ifms.php/accountant/dashboard/<?=strtotime('last day of previous month',$month);?>';
		 
		 window.location.href=href;
	}
	else
	{
		var href='<?=base_url();?>ifms.php/accountant/dashboard/<?=strtotime('last day of next month',$month);?>';
		 
		 window.location.href=href;
	}
	
	ev.preventDefault();
	
});

function modify_td_background_color(){
 	
}

</script>