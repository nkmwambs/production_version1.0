<div id="balance_summary">
									
	<table class="table table-bordered">
		<thead>
			<tr>
    			<th colspan="6" style="text-align: center;font-weight: bold;"><?php echo $this->session->userdata('center_id');?><br/><?=get_phrase('cash_journal');?></th>
			</tr>
	
    	</thead>

		<body>	
			<tr>
			    <td colspan="2" style="font-weight: bold;"><?=get_phrase('period');?></td>
				<td style="font-weight: bold;"><?=get_phrase('month');?></td>
				<td><?php echo date('M',$tym);?></td>
				<td style="font-weight: bold;"><?=get_phrase('year');?></td>
				<td><?php echo date('Y',$tym);?></td>
			</tr>

            <tr>
				<td style="font-weight: bold;"><?=get_phrase('balance_brought_forward');?></td>
				<td rowspan="4" style="font-weight: bold;"><?=get_phrase('bank');?></td>
				<td style="text-align: right;"><?php echo number_format($bank['balance_bf'],2);?></td>
				<td rowspan="4" style="font-weight: bold;"><?=get_phrase('petty_cash');?></td>
				<td colspan="2" style="text-align: right;"><?php echo number_format($cash['balance_bf'],2);?></td>
			</tr>

            <tr>
				<td style="font-weight: bold;"><?=get_phrase('deposit');?></td>
				<td style="text-align: right;"><?=number_format($bank['deposit'],2);?></td>
				<td colspan="2" style="text-align: right;"><?=number_format($cash['deposit'],2)?></td>
			</tr>

            <tr>
				<td style="font-weight: bold;"><?=get_phrase('payment');?></td>
				<td style="text-align: right;"><?=number_format($bank['payment'],2);?></td>
				<td colspan="2" style="text-align: right;"><?=number_format($cash['payment'],2)?></td>
			</tr>

            <tr>
				<td style="font-weight: bold;"><?=get_phrase('end_month_balance');?></td>
				<td style="text-align: right;"><?=number_format($bank['closing_balance'],2);?></td>
				<td colspan="2" style="text-align: right;"><?=number_format($cash['closing_balance'],2)?></td>
			</tr>

        </body>
    </table>
</div>