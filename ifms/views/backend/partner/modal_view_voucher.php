<?php
//echo $param2;
$this->db->select(array(
	'voucher_header.TDate as voucher_date',
	'voucher_header.VNumber as voucher_number',
	'voucher_header.icpNo as fcp_id',
	'voucher_header.Payee as payee',
	'voucher_header.ChqNo as cheque_number',
	'voucher_header.Address as address',
	'voucher_header.VType as voucher_type',
	'voucher_header.TDescription as voucher_description',
	'voucher_body.Details as detail_description',
	'voucher_body.Qty as quantity',
	'voucher_body.UnitCost as unit_cost',
	'voucher_body.Cost as total_cost',
	'voucher_body.Details as detail_description',
	'accounts.AccText as account_code',
));
$this->db->join('voucher_header','voucher_header.hID=voucher_body.hID');
$this->db->join('accounts','accounts.AccNo=voucher_body.AccNo');
$record = $this->db->get_where('voucher_body',array("voucher_header.hID"=>$param2))->result_object(); 

$VNumber = $record[0]->voucher_number;
$icpNo = $record[0]->fcp_id;

//print_r($record);
?>

<div class="row">
	<div class="col-md-12">

				    	<?php
				    		if(empty($record)){
				    	?>
				    	<div class="well"><?=get_phrase('voucher_not_available');?></div>
				    	<?php
							}else{
								$cond_summary = "Month(TDate)='".date('m',strtotime($record[0]->voucher_date))."' AND Year(TDate)='".date('Y',strtotime($record[0]->voucher_date))."'";
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
									<td  colspan="3"><span style="font-weight: bold;">Date: </span> <?php echo $record[0]->voucher_date;?></td>
									<td  colspan="3"><span style="font-weight: bold;">Number: </span> <?php echo $record[0]->voucher_number;?></td>
								</tr>
								
								<tr>
									<td colspan="3"><span style="font-weight: bold;">Vendor/Payee: </span> <?php echo $record[0]->payee;?></td>
									<?php $chqNo = explode("-",$record[0]->cheque_number);?>
									<td  colspan="3"><span style="font-weight: bold;">Cheque Number: </span> <?php echo $chqNo[0];?></td>
								</tr>
								
								<tr>
									<td  colspan="3"><span style="font-weight: bold;">Address: </span> <?php echo $record[0]->address;?></td>
									<td  colspan="3"><span style="font-weight: bold;">Voucher Type: </span> <?php echo $record[0]->voucher_type;?></td>
								</tr>
								
								<tr>
									<td colspan="6"><span style="font-weight: bold;">Description: </span> <?php echo $record[0]->voucher_description;?></td>
								</tr>
								
								<tr style="font-weight: bold;" id="tr_header">
									<td><?=get_phrase('quantity');?></td>
									<td colspan="2"><?=get_phrase('items_Purchased_service_received');?></td>
									<td><?=get_phrase('unit_cost');?></td>
									<td><?=get_phrase('cost');?></td>
									<td><?=get_phrase('account');?></td>
								</tr>
								
								<?php
									
										// $cond = "hID=".$record->hID;
										// $body = $this->db->where($cond)->get('voucher_body')->result_array();
										$sum_cost = 0;
										foreach($record as $row):
								?>
									<tr>
										<td><?php echo $row->quantity;?></td>
										<td colspan="2"><?php echo $row->detail_description;?></td>
										<td><?php echo number_format($row->unit_cost,2);?></td>
										<td><?php echo number_format($row->total_cost,2);?></td>
										<td><?=$row->account_code;?></td>
									</tr>
								<?php
									$sum_cost +=$row->total_cost;
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
include "dct_scripts.php";
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
		load_dct_data_to_view_voucher('<?=$param2;?>');
	});

</script>


