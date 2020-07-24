<?php
$hID = $param2;

$record = $this->db->get_where('voucher_header',array("hID"=>$hID))->row(); 

$VNumber = $record->VNumber;
$icpNo = $record->icpNo;

?>

<div class="row">
	<div class="col-md-12">

				    	<?php
				    		if(empty($record)){
				    	?>
				    	<div class="well"><?=get_phrase('voucher_not_available');?></div>
				    	<?php
							}else{
								$cond_summary = "Month(TDate)='".date('m',strtotime($record->TDate))."' AND Year(TDate)='".date('Y',strtotime($record->TDate))."'";
					    	?>
		
		<div class="panel panel-primary" data-collapsed="0">
        	<div class="panel-heading">
            	<div class="panel-title" >
            		<i class="entypo-plus-circled"></i>
						Voucher
            	</div>
            </div>
			
			<div class="panel-body"  style="max-width:50; overflow: auto;">	
				
			
			<center>
			    <a onclick="PrintElem('#voucher_print')" class="btn btn-default btn-icon icon-left hidden-print pull-right">
			        Print Voucher
			        <i class="entypo-print"></i>
			    </a>
			</center>
			
			    <br><br>			    	
					   <div id="voucher_print"> 	
						<table  class="table table-striped datatable">
							<thead>
								<tr>
									<th colspan="6" style="text-align:center;"><?php echo $icpNo;?><br><?=get_phrase('transaction_voucher');?></th>
								</tr>
							</thead>
							<tbody>
								
								<tr>
									<td  colspan="3"><span style="font-weight: bold;">Date: </span> <?php echo $record->TDate;?></td>
									<td  colspan="3"><span style="font-weight: bold;">Number: </span> <?php echo $record->VNumber;?></td>
								</tr>
								
								<tr>
									<td colspan="6"><span style="font-weight: bold;">Vendor/Payee: </span> <?php echo $record->Payee;?></td>
								</tr>
								
								<tr>
									<td  colspan="3"><span style="font-weight: bold;">Address: </span> <?php echo $record->Address;?></td>
									<td  colspan="3"><span style="font-weight: bold;">Voucher Type: </span> <?php echo $record->VType;?></td>
								</tr>
								
								<tr>
									<td colspan="6"><span style="font-weight: bold;">Description: </span> <?php echo $record->TDescription;?></td>
								</tr>
								
								<tr style="font-weight: bold;">
									<td>Quantity</td>
									<td colspan="2">Items Purchased/ Service Received</td>
									<td>Unit Cost</td>
									<td>Cost</td>
									<td>Account</td>
								</tr>
								
								<?php
									
										$cond = "hID=".$record->hID;
										$body = $this->db->where($cond)->get('voucher_body')->result_array();
										$sum_cost = 0;
										foreach($body as $row):
								?>
									<tr>
										<td><?php echo $row['Qty'];?></td>
										<td colspan="2"><?php echo $row['Details'];?></td>
										<td><?php echo number_format($row['UnitCost'],2);?></td>
										<td><?php echo number_format($row['Cost'],2);?></td>
										<td><?php echo $this->db->get_where('accounts',array('AccNo'=>$row['AccNo']))->row()->AccText;?></td>
									</tr>
								<?php
									$sum_cost +=$row['Cost'];
									endforeach;
								?>
								<tr>
									<td colspan="4" style="font-weight: bold;">Totals</td>
									<td colspan="2"><?php echo number_format($sum_cost,2);?></td>
								</tr>
								
								<tr>
									<td><span style="font-weight: bold;">Raised By</span></td>
									<td colspan="2"><span style="font-weight: bold;">Name: </span>  </td>
									<td colspan="3"><span style="font-weight: bold;">Signature: </span></td>
								</tr>
								<tr>
									<td colspan="3"><span style="font-weight: bold;">Verified By</span></td>
									<td colspan="3"><span style="font-weight: bold;">Approved By</span></td>
								</tr>
								<tr>
									<td>Name: </td><td colspan="2">Signature: </td> <td>Name: </td><td colspan="2">Signature</td>
								</tr>
								<tr>
									<td>Name: </td><td colspan="2">Signature: </td> <td>Name: </td><td colspan="2">Signature</td>
								</tr>
								
								<tr>
									<td>Name: </td><td colspan="2">Signature: </td> <td>Name: </td><td colspan="2">Signature</td>
								</tr>
							</tbody>
						</table>
						<?php
						}
						?>
				</div>
			</div>		
           
        </div>
    </div>
</div>

<?php
	include VIEWPATH."backend/partner/dct_scripts.php";
?>

<script type="text/javascript">

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

	$(document).ready(function(){
		//alert('Hello');
		load_dct_data_to_view_voucher('<?=$param2;?>');
	});
</script>