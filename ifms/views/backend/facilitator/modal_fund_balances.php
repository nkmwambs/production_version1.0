<?php
$end_period_date = $param2;
$fcp_id = $param3;
$start_period_date = date('Y-m-01',strtotime($end_period_date));

$fund_balance_report_grid = $this->finance_model->fund_balance_report_grid($fcp_id,$start_period_date,$end_period_date);

//print_r($fund_balance_report_grid);

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
							<th colspan="5">Fund Balance Report for the Month Ending <?=$end_period_date;?></th>
						</tr>
						<tr>
							<th style="font-weight:bold;"><?=get_phrase('account');?></th>
							<th style="text-align: right;font-weight:bold;"><?=get_phrase('opening_balance');?></th>
							<th style="text-align: right;font-weight:bold;"><?=get_phrase('month_income');?></th>
							<th style="text-align: right;font-weight:bold;"><?=get_phrase('month_expense');?></th>
							<th style="text-align: right;font-weight:bold;"><?=get_phrase('ending_balance');?></th>
						</tr>
					</thead>
					<tbody>
						<?php 
							foreach($fund_balance_report_grid['utilized_income_accounts'] as $account){
								$opening_balance = isset($fund_balance_report_grid[$account['account_number']]['opening_balance']) ? $fund_balance_report_grid[$account['account_number']]['opening_balance'] : 0;
								$month_income = isset($fund_balance_report_grid[$account['account_number']]['income']) ? $fund_balance_report_grid[$account['account_number']]['income'] : 0;
								$month_expense = isset($fund_balance_report_grid[$account['account_number']]['expense']) ? $fund_balance_report_grid[$account['account_number']]['expense'] : 0;
								$end_balance = $opening_balance + $month_income - $month_expense;

								//if($opening_balance && $month_income == 0 && $month_expense == 0) continue;
						?>
							<tr>
								<td><?=$account['account_code'].' - '.$account['account_name'];?></td>
								<td style="text-align: right;"><?=number_format($opening_balance,2);?></td>
								<td style="text-align: right;"><?=number_format($month_income,2);?></td>
								<td style="text-align: right;"><?=number_format($month_expense,2);?></td>
								<td style="text-align: right;"><?=number_format($end_balance,2);?></td>
							</tr>
						<?php 
														
							}
						?>
					</tbody>
					<tfoot>
							<?php 
								$total_opening_balance = array_sum(array_column($fund_balance_report_grid,'opening_balance'));
								$total_month_income = array_sum(array_column($fund_balance_report_grid,'income'));
								$total_month_expense = array_sum(array_column($fund_balance_report_grid,'expense'));
								$total_ending_balance = $total_opening_balance + $total_month_income - $total_month_expense;
							?>
							<tr>
								<td style="font-weight:bold;"><?=get_phrase('total');?></td>
								<td style="text-align: right;font-weight:bold;"><?=number_format($total_opening_balance,2);?></td>
								<td style="text-align: right;font-weight:bold;"><?=number_format($total_month_income,2);?></td>
								<td style="text-align: right;font-weight:bold;"><?=number_format($total_month_expense,2);?></td>
								<td style="text-align: right;font-weight:bold;"><?=number_format($total_ending_balance,2);?></td>
							</tr>
					</tfoot>
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