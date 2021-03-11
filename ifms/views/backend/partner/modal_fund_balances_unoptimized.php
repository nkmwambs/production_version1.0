<?php
$month =$param2;
include "include_report_queries.php";
?>
				

<div class="row">
	<div class="col-sm-12">
			<div class="panel panel-primary" data-collapsed="0">
        	<div class="panel-heading">
            	<div class="panel-title" >
            		<i class="fa fa-vcard"></i>
					<?php echo get_phrase('fund_balance_report');?>
            	</div>
            </div>
			<div class="panel-body"  style="max-width:50; overflow: auto;">	
			<div class="row">
					<div class="col-sm-6">
						<a href="#" class="fa fa-print" onclick="PrintElem('#tbl_fund_balance');"><?=get_phrase('print');?></a>
					</div>
					
				</div>
	
				<div id="tbl_fund_balance">
				<table class="table table-striped">
					<thead>
						<tr>
							<th colspan="5">Fund Balance Report for the Month Ending <?=$month_end;?></th>
						</tr>
						<tr>
							<th>Account</th>
							<th style="text-align: right;">Opening Balance</th>
							<th style="text-align: right;">Month Income</th>
							<th style="text-align: right;">Month Expense</th>
							<th style="text-align: right;">Ending Balance</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							foreach($removed_empty_rows_in_grid as $row_key=>$row_value){
								$row = $revenue_accounts_with_acc_id_keys[$row_key];
						?>
							<tr>
								<td><?=$row['AccText'].' - '.$row['AccName'];?></td>
								<?php
									
									foreach($row_value as $col){
								?>
									<td style="text-align: right;"><?=number_format($col,2);?></td>
								<?php
									}
								?>
							</tr>
						<?php
							}
						?>
						<tfoot>
							<tr>
								<th>Total:</th>
								<th style="text-align: right;"><?=number_format(array_sum(array_column($removed_empty_rows_in_grid,"ob")),2);?></th>
								<th style="text-align: right;"><?=number_format(array_sum(array_column($removed_empty_rows_in_grid,"mi")),2);?></th>
								<th style="text-align: right;"><?=number_format(array_sum(array_column($removed_empty_rows_in_grid,"me")),2);?></th>
								<th style="text-align: right;"><?=number_format(array_sum(array_column($removed_empty_rows_in_grid,"cb")),2);?></th>
							</tr>
						</tfoot>
					</tbody>
				</table>
		
				</div>
			
			</div>
		
	</div>
</div>		

<script>
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
</div>