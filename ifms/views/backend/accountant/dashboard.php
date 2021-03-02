<style>
.success_parameter{
	background-color: green;
	color:white;
}

.fail_parameter{
	background-color: red;
	color: white;
}
</style>

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
		
		<table  class='table datatable'>
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
				   	 <td><?=fcp_reports_dropdown($fcp_id,$month);?></td>
					<td><i class='fa fa-envelope' style='cursor:pointer;' onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_dashboard_messaging/<?php echo date('Y-m-t',$month);?>/<?=$fcp_id;?>')"></i>
						<?=$value['message_sent'] == "Yes"?'<i class="badge badge-secondary">Yes</i>':'';?>
					</td>
					
				   	 <td><?=$value['risk'];?></td>
				   	 
				   	 <?php
				   	  foreach (($value['params']) as $param) {
							echo parameter_cell($param);
						 }
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