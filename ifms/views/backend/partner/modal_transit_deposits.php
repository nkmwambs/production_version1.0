<?php
//Deposit in Transit

$mfr_submitted = $this->finance_model->mfr_submitted($this->session->center_id,date('Y-m-d',strtotime($param2)));

$dep = $this->finance_model->deposit_transit($param2,$this->session->center_id);
?>

<div class="row">
	<div class="col-sm-12">
			
			<div class="panel panel-primary" data-collapsed="0">
        	<div class="panel-heading">
            	<div class="panel-title" >
            		<i class="entypo-plus-circled"></i>
					<?php echo get_phrase('deposits_in_transit');?>
            	</div>
            </div>
			<div class="panel-body"  style="max-width:50; overflow: auto;">	
			
			<div class="row">
					<div class="col-sm-12">
						<a href="#" class="fa fa-print" onclick="PrintElem('#depTable');"><?=get_phrase('print');?></a>
					</div>
				</div>	
						<table class="table table-hover" id="depTable">
							<thead>
								<tr>
									<th><?php echo get_phrase('action');?></th>
									<th><?php echo get_phrase('date');?></th>
									<th><?php echo get_phrase('details');?></th>
									<th><?php echo get_phrase('amount');?></th>									
								</tr>								
							</thead>
							<tbody>
									<?php
										$depTotal = 0;
										foreach($dep as $row):
									?>
								<tr>
								    <?php

									$check_if_chq_is_cancelled_or_reused = false;

									if ($row['voucher_reversal_to'] > 0 || $row['voucher_reversal_from'] > 0) {

										$check_if_chq_is_cancelled_or_reused = true;
									}

									?>

									<td><div <?php if($mfr_submitted==='1'){echo "style='display:none;'";};?>  class="btn btn-danger depClr" <?php if($check_if_chq_is_cancelled_or_reused){echo 'disabled';} ?> id="dep_<?php echo $row['hID'];?>"><?php if($check_if_chq_is_cancelled_or_reused){echo get_phrase('cancelled_cheque');} else{ echo get_phrase('clear');}?></div></td>
									<td><?php if($check_if_chq_is_cancelled_or_reused){echo "<i style='color:red;'>";} ?><?php echo $row['TDate']?></td>
									<td><?php if($check_if_chq_is_cancelled_or_reused){echo "<i style='color:red;'>";} ?><?php echo $row['TDescription']?></td>
									<td><?php if($check_if_chq_is_cancelled_or_reused){echo "<i style='color:red;'>";} ?><?php echo $row['totals']?></td>
								</tr>
								
								<?php
									$depTotal +=$row['totals'];
									endforeach;
								?>
							<tr><td><?php echo get_phrase('total');?></td><td id="depTotal" colspan="4" style="text-align: right;"><?php echo number_format($depTotal,2);?></td></tr>									
							</tbody>
						</table>									
			
			</div>
	</div>
			
	</div>
	
</div>

<script>
	$(document).ready(function(){
		
		$('.depClr').click(function(ev){
			
			var hid = this.id;

			BootstrapDialog.confirm('<?php echo get_phrase("Are_you_sure_you_want_to_clear_this_deposit?");?>', function(result){
            if(!result) {
		                 BootstrapDialog.show({
		                 	title:'Information',
				            message: '<?php echo get_phrase('process_aborted');?>'
				        });
	            }else{
			
					var url = '<?php echo base_url();?>ifms.php/partner/clear_effects/'+hid+'/<?php echo strtotime($param2);?>';
					$.ajax(
						{
							url:url,
							success:function(response){
								
								BootstrapDialog.show({
				                 	title:'Alert',
						            message: response
						        });
						        
								$('#'+hid).parent().parent().remove();
										var total = 0;
										  $("#depTable tr:gt(0)").each(function () {
										        var this_row = $(this);
										        var amt = $.trim(this_row.find('td:eq(3)').html())
												total +=Number(amt);
										    });
										$('#depTotal').html(total);					    
										$('#depTransit'). val(total);
							}
						}
					);
			
			}
			
		});
		
	});

});	

	    function PrintElem(elem)
    {
        $(elem).printThis({ 
		    debug: false,              
		    importCSS: true,             
		    importStyle: true,         
		    printContainer: false,       
		    loadCSS: "", 
		    pageTitle: "<?php echo get_phrase('payment_voucher');?>",             
		    removeInline: false,        
		    printDelay: 333,            
		    header: null,             
		    formValues: true          
		});
    }
</script>