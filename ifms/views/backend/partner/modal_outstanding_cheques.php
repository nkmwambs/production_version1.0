<?php
//Outstanding Cheques

$mfr_submitted = $this->finance_model->mfr_submitted($this->session->center_id,date('Y-m-d',strtotime($param2)));

$oc=$this->finance_model->outstanding_cheques($param2,$this->session->center_id);

//print_r($oc);
?>

<div class="row">
	<div class="col-sm-12">
			
			<div class="panel panel-primary" data-collapsed="0">
        	<div class="panel-heading">
            	<div class="panel-title" >
            		<i class="entypo-plus-circled"></i>
					<?php echo get_phrase('outstanding_cheques');?>
            	</div>
            </div>
			<div class="panel-body"  style="max-width:50; overflow: auto;">	
			
			<div class="row">
					<div class="col-sm-12">
						<a href="#" class="fa fa-print" onclick="PrintElem('#ocTable');"><?=get_phrase('print');?></a>
					</div>
				</div>
	
				
						<table class="table table-hover" id="ocTable">
							<thead>
								<tr>
									<th><?php echo get_phrase('action');?></th>
									<th><?php echo get_phrase('date');?></th>
									<th><?php echo get_phrase('cheque_number');?></th>
									<th><?php echo get_phrase('details');?></th>
									<th><?php echo get_phrase('amount');?></th>
								</tr>
							</thead>
							<body>
								<?php
									$oc_total = 0;
									foreach($oc as $row):
										$chq=explode('-',$row['ChqNo']);
								?>
									<tr>
										<td><div <?php if($mfr_submitted==='1'){echo "style='display:none;'";};?> class="btn btn-danger chqClr" <?php if($row['voucher_reversal_to']>0||$row['voucher_reversal_from']>0){echo 'disabled';}?> id='oc_<?php echo $row['hID'];?>'><?php echo get_phrase('clear');?></div></td>
										<td><?php echo $row['TDate']?></td>
										<td><?php  echo $chq[0];?></td>
										<td><?php echo $row['TDescription']?></td>
										<td><?php echo $this->db->select_sum('Cost')->get_where('voucher_body',array('hID'=>$row['hID']))->row()->Cost?></td>
									</tr>
								<?php
									$oc_total+=$this->db->select_sum('Cost')->get_where('voucher_body',array('hID'=>$row['hID']))->row()->Cost;
									endforeach;
								?>
								<tr><td><?php echo get_phrase('total');?></td><td id="ocTotal" colspan="4" style="text-align: right;"><?php echo number_format($oc_total,2);?></td></tr>
							</body>
						</table>										
			
			</div>
	</div>
			
	</div>
	
</div>

<script>
	$(document).ready(function(){	
		
		//Clear Oustanding Cheques
		$('.chqClr').click(function(ev){
			var hid = this.id;
			//alert(hid);
			BootstrapDialog.confirm('<?php echo get_phrase("Are_you_sure_you_want_to_clear_this_cheque?");?>', function(result){
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
										  $("#ocTable tr:gt(0)").each(function () {
										        var this_row = $(this);
										        var amt = $.trim(this_row.find('td:eq(4)').html())
												total +=Number(amt);
										    });
										$('#ocTotal').html(total);						    
										$('#osChq'). val(total);
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
