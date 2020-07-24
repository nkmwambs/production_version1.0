<style>
	td.details-control {
    background: url('<?php echo base_url();?>uploads/details_open.png') no-repeat center center;
    cursor: pointer;
}
tr.shown td.details-control {
    background: url('<?php echo base_url();?>uploads/details_close.png') no-repeat center center;
}
</style>

<div class="row">
	<div class="col-sm-12">	
			<div class="panel panel-primary" data-collapsed="0">
        	<div class="panel-heading">
            	<div class="panel-title" >
            		<i class="fa fa-vcard"></i>
					<?php echo get_phrase('cash_journal');?>
            	</div>
            </div>
			<div class="panel-body"  style="max-width:50; overflow: auto;">	
				<div id="load_journal">
					<?php
					/**Spread Incomes and Expenses **/
					$month_rev = $this->finance_model->months_income_accounts_utilized($this->session->center_id,$tym);
					
					$month_exp = $this->finance_model->months_expenses_accounts_utilized($this->session->center_id,$tym);
					
					/**Spread Incomes and Expenses **/
					
					$bank_reconcile_check = abs(floor($this->finance_model->bank_reconciled($this->session->center_id,date('Y-m-t',$tym))));
					$rec_chk = "Ok";
					$rec_color = "success";
					if($bank_reconcile_check>0){
						$rec_chk = "Error";
						$rec_color = "warning";
					}
					
					//echo $bank_reconcile_check; 
					
					$proof_of_cash_check = $this->finance_model->proof_of_cash($this->session->center_id,date('Y-m-t',$tym));//(round($this->db->select_sum('amount')->get_where('cashbal',array('project_id'=>$this->session->userdata('center_id'),'month'=>date('Y-m-t',$tym)))->row()->amount,2)-round($this->finance_model->total_cash(date('Y-m-01',$tym),$this->session->center_id),2));
					
					$proof_chk = "Ok";
					$proof_color = "success";
					if($proof_of_cash_check <> 0){
						$proof_chk = "Error";
						$proof_color = "warning";
					}
					
					$hide_status = 'display:block';
					if($this->finance_model->mfr_submitted($this->session->center_id,date('Y-m-t',$tym))=== '1'){
						$hide_status = 'display:none';
					}
					
					 if($this->finance_model->check_opening_balances($this->session->center_id)===true){ 
				?>
					
					<div class="row">
						<div class="col-sm-12">		
							<button style="display: none;margin-right: 10px;" class="btn btn-primary btn-icon pull-left" id="print_vouchers"><i class="fa fa-print"></i><?=get_phrase('print_vouchers');?></button>
							
							<!-- <button style="<?=$hide_status;?>" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_new_voucher');" class="btn btn-primary btn-icon pull-left"><i class="fa fa-plus-circle"></i><?php echo get_phrase('new_voucher');?></button> -->
		
							<div class="col-sm-offset-1 btn-group right-dropdown">
										<button type="button" id="" class="btn btn-blue"><?php echo get_phrase('financial_reports');?></button>
										<button type="button" id="" class="btn btn-blue dropdown-toggle" data-toggle="dropdown">
											<span class="caret"></span>
										</button>
										
										<ul class="dropdown-menu dropdown-blue" role="menu">
		
											<li  style="">
												<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_outstanding_cheques/<?php echo date('Y-m-t',$tym);?>')"><?php echo get_phrase('outstanding_cheques');?></a>
											</li>
											
											<li  style="" class="divider"></li>
											
											<li style="">
												<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_transit_deposits/<?php echo date('Y-m-t',$tym);?>')"><?php echo get_phrase('deposits_in_transit');?></a>
											</li>
											
											<li style="" class="divider"></li>
											
											<li  style="">
												<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_cleared_effects/<?php echo date('Y-m-t',$tym);?>')"><?php echo get_phrase('cleared_effects');?></a>
												
											</li>
											
											<li style="<" class="divider"></li>
											
											<li style="">
												<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_bank_reconcile/<?php echo date('Y-m-t',$tym);?>')"><?php echo get_phrase('bank_reconciliation');?> <span class="badge badge-<?=$rec_color;?>"><?=$rec_chk;?></span></a>
											</li>
											
											<li style="" class="divider"></li>
											
											<li>
												<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_variance_explanation/<?php echo date('Y-m-t',$tym);?>')"><?php echo get_phrase('variance_explanation');?></a>
											</li>
											
											<li class="divider"></li>
											
											<li style="">
												<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_proof_of_cash/<?php echo date('Y-m-t',$tym);?>')"><?php echo get_phrase('proof_of_cash');?> <span class="badge badge-<?=$proof_color;?>"><?=$proof_chk?></span></a>
												
											</li>
											
											<li style="" class="divider"></li>
											
											<li>
												<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_fund_balances/<?php echo date('Y-m-t',$tym);?>')"><?php echo get_phrase('fund_balance_report');?></a>
												
											</li>
											
											<li class="divider"></li>
											
											<li>
												<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_expense_report/<?php echo date('Y-m-t',$tym);?>')"><?php echo get_phrase('expense_report');?></a>
												
											</li>
											
											<li class="divider"></li>
											
											<li>
												<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_fund_ratios/<?php echo date('Y-m-t',$tym);?>')"><?php echo get_phrase('financial_ratios');?></a>
												
											</li>
											
																			
											<li class="divider"></li>
											
											<li style="">
												<a href="<?php echo base_url();?>ifms.php/partner/bank_statements/<?php echo date('Y-m-t',$tym);?>"><?php echo get_phrase('upload_bank_statements');?></a>
											</li>
											
											<li class="divider"></li>
											
											<!--<li class="divider"></li>-->
											
											<li style="<?=$hide_status;?>">
												<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_submit_mfr/<?php echo date('Y-m-t',$tym);?>');"><?php echo get_phrase('submit_financial_report');?></a>
												
											</li>
											
																						
										</ul>
									</div>
									
									<div class="btn btn-default pull-right col-sm-3"><a href="#" class="fa fa-backward scroll" id="prev"></a> <?=get_phrase('you_are_in');?> <?=date('F Y',$tym);?> <input type="text" class="form-control col-sm-1" id="cnt" placeholder="<?=get_phrase('enter_number_of_months');?>"/> <a href="#" class="fa fa-forward scroll" id="next"></a></div>
							</div>
					</div>
					
					<hr/>
					
					<div class="row">
						<div class="col-sm-12" style="text-align: center;">
									<?php
										$msg = get_phrase('status')." : ".date("F Y",$tym)." ".get_phrase('financial_report_not_submitted');
										$color = "style='color:red;'";
										if($this->finance_model-> mfr_submitted($this->session->center_id,date('Y-m-t',$tym))==='1'){
											$msg = get_phrase('status')." : ".date("F Y",$tym)." ".get_phrase('financial_report_submitted');
											$color = "style='color:green;'";
										}	
									?>
									<div class="col-sm-offset-3 col-sm-6 well well-sm"><h4 <?=$color;?>><?=$msg;?></h3></div>
									
						</div>
					</div>
					
					<hr/>
					
					<div class="row">
						<div class="col-sm-12">
							<a href="#" class="btn btn-default" onclick="PrintElem('#balance_summary');"><i class="fa fa-print"></i><?=get_phrase('print');?></a>
							
							<div class="btn btn-default" id="toggleColumns"><?=get_phrase('show_spread');?></div>
								
							<div class="btn btn-default not-selected" id="chkAll"><?=get_phrase('select_vouchers');?></div>
						</div>
					</div>
						
					
					<?php
							$max_col = 12;
							
							/** Journal Records **/
							$condition = "Month(`TDate`)='".date('m',$tym)."' AND Fy='".get_fy(date('Y-m-d',$tym),$this->session->center_id)."' AND icpNo='".$this->session->userdata('center_id')."'";
							$records = $this->db->where($condition)->get('voucher_header')->result_array();
							
							//print_r($records);
				
						
					 echo form_open(base_url() . 'ifms.php/partner/multiple_vouchers/'.$tym , array('id'=>'cj_print_vouchers'));
					
					?>
							
					<!-- Summary table -->
					
					<div class="row">
							<div class="col-sm-12">
								<div id="balance_summary">
									
									<table class="table table-bordered">
										<thead>
											<tr>
												<th colspan="6" style="text-align: center;font-weight: bold;"><?php echo $this->session->userdata('center_id');?><br/><?=get_phrase('cash_journal');?></th>
											</tr>
										</thead>
										<body id="">	
											<tr>
												<td colspan="2" style="font-weight: bold;"><?=get_phrase('period');?></td>
												<td style="font-weight: bold;"><?=get_phrase('month');?></td>
												<td><?php echo date('M',$tym);?></td>
												<td style="font-weight: bold;"><?=get_phrase('year');?></td>
												<td><?php echo date('Y',$tym);?></td>
											</tr>
											<tr>
												<?php
													$begin_bank = $this->finance_model->opening_bank_balance(date('Y-m-t',$tym),$this->session->center_id);										
													$begin_pc = $this->finance_model->opening_pc_balance(date('Y-m-t',$tym),$this->session->center_id);
												?>
												<td style="font-weight: bold;"><?=get_phrase('balance_brought_forward');?></td>
												<td rowspan="4" style="font-weight: bold;"><?=get_phrase('bank');?></td>
												<td style="text-align: right;"><?php echo number_format($begin_bank,2);?></td>
												<td rowspan="4" style="font-weight: bold;"><?=get_phrase('petty_cash');?></td>
												<td  colspan="2" style="text-align: right;"><?php echo number_format($begin_pc,2);?></td>
											</tr>
												<?php
													$sum_bank_income = 0;
													$sum_bank_payment = 0;
													$sum_bank_balance = 0;
													
													$sum_pc_income = 0;
													$sum_pc_payment = 0;
													$sum_pc_balance = 0;
													
													//SUM Bank Income
													$sum_bank_income = $this->finance_model->months_bank_income(date('Y-m-d',$tym),$this->session->center_id);
													
													
													//SUM Bank Payments
													$sum_bank_payment = $this->finance_model->months_bank_expense(date('Y-m-d',$tym),$this->session->center_id);
													
													//SUM Bank Balance
													$sum_bank_balance = $begin_bank+$sum_bank_income-$sum_bank_payment;						
													
													
												
													/** SUM ALL PC Income Vouchers **/

													$sum_pc_income = $this->finance_model->months_pc_income($this->session->center_id,date('Y-m-01',$tym));
													$sum_pc_payment = $this->finance_model->months_pc_expense($this->session->center_id,date('Y-m-01',$tym));;
													
													
													$sum_pc_balance = $begin_pc+$sum_pc_income-$sum_pc_payment;	
												?>
																			
											<tr>
												<td style="font-weight: bold;"><?=get_phrase('deposit');?></td>
												<td style="text-align: right;"><?=number_format($sum_bank_income,2);?></td>
												<td colspan="2" style="text-align: right;"><?=number_format($sum_pc_income,2)?></td>
												
											</tr>
											<tr>
												<td style="font-weight: bold;"><?=get_phrase('payment');?></td>
												<td style="text-align: right;"><?=number_format($sum_bank_payment,2);?></td>
												<td colspan="2" style="text-align: right;"><?=number_format($sum_pc_payment,2)?></td>
												
											</tr>
											<tr>
												<td style="font-weight: bold;"><?=get_phrase('end_month_balance');?></td>
												<td style="text-align: right;"><?=number_format($sum_bank_balance,2);?></td>
												<td colspan="2" style="text-align: right;"><?=number_format($sum_pc_balance,2)?></td>
											
											</tr>
										</body>	
			
									</table>
								</div>
							</div>
						</div>		
							
						<!-- End Summary table -->		
						
						<!-- Cash Journal --->
						
						<div class="row">
							<div class="col-sm-12">
								<table class="table table-bordered table-hover table-responsive datatable" id="table_export">
									<thead>
										
										<tr>
											<th><?php echo get_phrase('show_details');?></th>
											<th><?php echo get_phrase('clear');?></th>
											<th><?php echo get_phrase('date');?></th>
											<th><?php echo get_phrase('voucher_type');?></th>
											<th><?php echo get_phrase('payee_/_source');?></th>
											<th><?php echo get_phrase('voucher_no');?></th>
											<th><?php echo get_phrase('description_/_details');?></th>
											<th><?php echo get_phrase('chq_/_ref_no');?></th>
											<th><?php echo get_phrase('bank_deposits');?></th>
											<th><?php echo get_phrase('bank_payments');?></th>
											<th><?php echo get_phrase('bank_balance');?></th>
											<th><?php echo get_phrase('cash_deposits');?></th>
											<th><?php echo get_phrase('cash_payments');?></th>
											<th><?php echo get_phrase('cash_balance');?></th>
											
											<!--Spread starts here -->
											<?php
												foreach($month_rev as $utilized):
											?>
												<th class="spread"><?=$utilized->AccText;?></th>
											<?php
												endforeach;
											?>
											
											<?php
												foreach($month_exp as $utilized_exp):
											?>
												<th class="spread"><?=$utilized_exp->AccText;?></th>
											<?php
												endforeach;
											?>
											<!--Spread ends here -->
										</tr>
										
		
									</thead>
									
									<tbody>
											<?php
												
												$bank_balance = 0;
												$pc_balance = 0;
												
												foreach($records as $row):
											?>
												<tr>
													<td id="<?=$row['hID'];?>"></td>									
													<?php
														
														if($row['VType']==='CHQ' || $row['VType']==='BCHG' || $row['VType']==='CR' || $row['VType'] === 'UDCTB'){
															$chk = 'checked';
																if($row['ChqState']==='1'){
																	$chk = "";
																}
				
													?>
														<td> 
															<?php
															if($this->finance_model->editable($this->session->center_id,date('Y-m-t',$tym))===0){
															?>
																<div id="<?php echo $row['hID'];?>" class="make-switch switch-small clr" data-on-label="<?php echo $row['VType'];?>" data-off-label="<?php echo $row['VType'];?>">
																<input type="checkbox" <?=$chk;?> value="<?php echo $row['VNumber'];?>" disabled/></div>
															<?php
															}else{
															?>
																<div id="<?php echo $row['hID'];?>" class="make-switch switch-small clr" data-on-label="<?php echo $row['VType'];?>" data-off-label="<?php echo $row['VType'];?>">
																<input type="checkbox" <?=$chk;?> value="<?php echo $row['VNumber'];?>"/></div>
															<?php
															}
															?>	
																
															<?php 
															 //if($row['VType'] == 'CHQ'){
																?>
																<!-- <i id='' data-hid = '<?=$row['hID'];?>' data-voucher = '<?php echo $row['VNumber'];?>' class='fa fa-magic chq_dctb_vtype_change' style='font-size:18pt;cursor:pointer;'></i> -->
															<?php 
													//	}
														?>
														</td>	
													<?php
														}else{
													?>
														<td>
															<div class="make-switch switch-small" data-off-label="<?php echo $row['VType'];?>">
															    <input type="checkbox" disabled>
															</div>
															
														</td>
													<?php
														}
													?>
		
													<td class="tdate"><?php echo $row['TDate'];?></td>
													<td>
													
														<?php 
															$path = 'uploads/dct_documents/'.$this->session->center_id.'/'.date('Y-m',$tym).'/'.$row['VNumber'].'/';

															if(file_exists($path) && (new \FilesystemIterator($path))->valid() && ($row['VType'] == 'UDCTB' || $row['VType'] == 'UDCTC') ){?>
															
																<a href='<?php echo base_url();?>ifms.php/dct/dct_documents_download/<?= $this->session->center_id;?>/<?=$tym;?>/<?=$row['VNumber'];?>' ><?=$row['VType'];?></a>
															<?php }else{?>
																<span><?=$row['VType'];?></span>
														<?php }?>
													</td>												
													
													<td><?php echo $row['Payee'];?></td>
													<td>
																									
														<div id="" class="btn btn-green popover-primary popup-ajax" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?=get_phrase('click_on_the_button_to_view_a_voucher');?>" data-original-title="<?=get_phrase('tooltip');?>" >
															<input type="checkbox" class="chk_voucher" name="voucher_<?=$row['VNumber']?>" value="<?=$row['VNumber']?>" id="<?=$row['VNumber']?>"/>
															<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_view_voucher/<?php echo $row['hID'];?>');">
																<?php echo $row['VNumber'];?>
															</a>
															
														</div>
														
														
													</td>
													<td><?php echo $row['TDescription'];?></td>
													<?php
													    
														//$mixed_chq = explode("-", $row['ChqNo']);
													?>
													<td><?php 
													      //Modified by Onduso/karisa 28/5/2020 start
														   //echo $mixed_chq[0];
														//    if($row['VType']=='UDCTB') echo $mixed_chq[1].'-'.$mixed_chq[2];
														//    else $mixed_chq[0];//for non UDTCB

														   $mixed_chq= "";

															if($row['VType'] !== 'UDCTB'){
																$mixed_chq = explode('-',$row['ChqNo'])[0];
															}else{
																$arr = explode('-',$row['ChqNo']);
																array_pop($arr);

																$mixed_chq = implode('-',$arr);
															}

															echo $mixed_chq;

														   //Modified by Onduso/karisa 28/5/2020 end
														   
													    ?></td>
													
													<!-- Bank Income and Expenses -->
													
													<?php
														$cr = '0';
														$chq = '0';
														
														//if($row['VType']==='CR'||$row['VType']==='PCR') $cr = $row['totals'];
														//if($row['VType']==='CHQ' || $row['VType'] ==='BCHG') $chq = $row['totals'];
														if($row['VType']==='CR'||$row['VType']==='PCR') $cr = $this->db->select_sum('Cost')->get_where('voucher_body',array('hID'=>$row['hID']))->row()->Cost;
														if($row['VType']==='CHQ' || $row['VType'] ==='BCHG' || $row['VType'] === 'UDCTB') $chq = $this->db->select_sum('Cost')->get_where('voucher_body',array('hID'=>$row['hID']))->row()->Cost;
														
														$bank_balance += $cr-$chq; 
														
													?>
													<td><?php echo number_format($cr,2);?></td>
													<td><?php echo number_format($chq,2);?></td>
													<td><?php echo number_format($begin_bank+$bank_balance,2);?></td>
													
													<!-- Petty Cash Income and Expenses -->
													
													<?php
														
														$get_body = $this->db->select_sum('Cost')->select(array("AccNo","VType"))->get_where("voucher_body",array("hID"=>$row['hID']))->result_array();
														//print_r($get_body);
															$pcr = '0';
															$pc = '0';
														foreach($get_body as $rows):
															
															if($rows['AccNo']==='2000' || $rows['AccNo']==='2001') $pcr = $rows['Cost'];
															if($rows['VType']==='PC' || $rows['VType']==='PCR' || $rows['VType'] === 'UDCTC') $pc = $rows['Cost'];
															$pc_balance += $pcr-$pc; 
													?>
														<td><?php echo number_format($pcr,2);?></td>
														<td><?php echo number_format($pc,2);?></td>
														<td><?php echo number_format($begin_pc+$pc_balance,2);?></td>
													<?php		
														endforeach;
													?>
														
													<!--Spread starts here -->
													<?php foreach($month_rev as $util):?>	
															<td class="spread">
																<?php
																	$rev_amt = 0;
																	
																	if($this->db->get_where('voucher_body',array('hID'=>$row['hID'],'AccNo'=>$util->AccNo))->num_rows()>0){
																		$rev_amt = $this->db->select_sum('Cost')->get_where('voucher_body',array('hID'=>$row['hID'],'AccNo'=>$util->AccNo))->row()->Cost;
																	}
																	
																	echo number_format($rev_amt,2);
																?>
															</td>
													<?php endforeach;?>									
															
													
													<?php foreach($month_exp as $util_exp):?>	
															<td class="spread">
																<?php
																	$exp_amt = 0;
																	
																	if($this->db->get_where('voucher_body',array('hID'=>$row['hID'],'AccNo'=>$util_exp->AccNo))->num_rows()>0){
																		$exp_amt = $this->db->select_sum('Cost')->get_where('voucher_body',array('hID'=>$row['hID'],'AccNo'=>$util_exp->AccNo))->row()->Cost;
																	}
																	
																	echo number_format($exp_amt,2);
																?>
															</td>
													<?php endforeach;?>			
															
															
												<!--Spread ends here -->	
																								
													
												</tr>
											<?php
												endforeach;
											?>
									</tbody>
								</table>
							
						</div>
					</div>
					
					<!--End of cash journal-->				
					
					<?php echo form_close();?>
					
				<?php } else{ echo get_phrase('please_set_opening_balances');}?>
				</div>
			</div>
			
			</div>
		</div>
	</div>

		
<script>


	$('#chkAll').click(function(){
		$(this).toggleClass('selected','not-selected');
		
		if($(this).hasClass('selected')){
			$('.chk_voucher').prop('checked',true);
					var cnt = 0;
					$('.chk_voucher').each(function(){
						if($(this).is(':checked')){
							++cnt;
						}
					});
			$(this).html(cnt+' <?=get_phrase('vouchers_selected');?>');		
		}else{
			$('.chk_voucher').prop('checked',false);
			$(this).html('<?=get_phrase('select_vouchers');?>');
		}
		
		count_checked_boxes();
	});
	
	$('.chk_voucher').click(function(){
		count_checked_boxes();
		
	});

	function count_checked_boxes(){
		var cnt_checked = 0;
		$('.chk_voucher').each(function(){
			if($(this).is(':checked')){
				++cnt_checked;
			}
		});
		
		if(cnt_checked>0){
			$('#print_vouchers').css('display','block');
			$("#chkAll").html(cnt_checked+' <?=get_phrase('vouchers_selected');?>');	
		}else{
			$('#print_vouchers').css('display','none');	
			$("#chkAll").html('<?=get_phrase('select_vouchers');?>');
		}
	}

function format ( d ) {
	var obj = JSON.parse(d);
	var cost="";
	var account="";
	var sum = 0;
	
	$.each(obj,function(index,value){
		cost += "<td>"+accounting.formatMoney(value.Cost, { symbol: "",  format: "%v %s" })+"</td>";
		account += "<th>"+value.AccNo+"</th>";
		sum = parseFloat(sum) + parseFloat(value.Cost);
	});
	
	return 	"<table border='1' style='border-collapse:collapse;'>"+
			"<thead>"+
			"<tr>"+account+"<th><?=get_phrase('total');?></th></tr>"+
			"</thead>"+
			"<tbody>"+
			"<tr>"+cost+"<td>"+accounting.formatMoney(sum, { symbol: "",  format: "%v %s" })+"</td></tr>"+
			"</tbody>"+
			"</table>";
}

	$(document).ready(function(){
		var datatable = $('.datatable').DataTable({
			lengthMenu: [[25,50, 100, 150,-1], [25,50 ,100,150 ,"All"]],
			pageLength: 25,
			dom: '<"row"l><Bf><"col-sm-12"rt><ip>',
		    pagingType: "full_numbers",
		    ordering:false,
		    buttons: [
		         'csv', 'excel', 'print'
		    ],	      
		    stateSave: true,
		    oLanguage: {
			        sProcessing: "<img src='<?php echo base_url();?>uploads/preloader4.gif'>"
			    },
			processing: true, //Feature control the processing indicator.
			"columnDefs": [
			       	{ 
			           "targets": [0], //first column / numbering column
			           "orderable": false, //set not orderable
			           "class": "details-control"
			       	}
			  ]    	
		});
		
		datatable.columns( '.spread' ).visible( false );
		
		$("#toggleColumns").click(function(ev){
			
			$(this).toggleClass('btn-success','btn-warning');
			
			if($(this).hasClass('btn-success')){
				datatable.columns( '.spread' ).visible( true );
				$(this).html('<?=get_phrase("hide_spread");?>');

			}else{
				datatable.columns( '.spread' ).visible( false );
				$(this).html('<?=get_phrase("show_spread");?>');

			}
			
		});
		
		var detailRows = [];
		
		  $('#table_export tbody').on( 'click', 'tr td.details-control', function () {
	        var tr = $(this).closest('tr');
	        var row = datatable.row( tr );
	        var td_id = $(this).attr('id');
	        var idx = $.inArray( tr.attr('id'), detailRows );
	 		//alert(td_id);
	        if ( row.child.isShown() ) {
	            tr.removeClass( 'details' );
	            row.child.hide();
	            tr.removeClass('shown')
	 
	            // Remove from the 'open' array
	            detailRows.splice( idx, 1 );
	            
	            //Change fa-plus to fa-minus
	            //row.child.hide();
	        }
	        else {
	        	//alert(td_id);
	            
	            	
	            	$.ajax({
						url:'<?php echo base_url();?>ifms.php/partner/get_ajax_voucher/'+td_id,
						success:function(data){
							
							tr.addClass( 'details' );
							row.child( format( data )).show();
				 			tr.addClass('shown');
				            // Add to the 'open' array
				            if ( idx === -1 ) {
				                detailRows.push( tr.attr('id') );
				            }
				            
						}
					});
	            
	        }
	    } );
	 
	    // On each draw, loop over the `detailRows` array and show any child rows
	    datatable.on( 'draw', function () {
	        $.each( detailRows, function ( i, id ) {
	            $('#'+id+' td.details-control').trigger( 'click' );
	        } );
	    } );
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
    
    //Clear records
	$('.clr').on('switch-change', function(e, data) {
			var el = $(this);
	       	var hID = el.attr('id');
			//alert(data.value);
			
		        var state = 1;
		        if(data.value==true){
		        	state = 0;
		        }
		        
		        	var url = '<?php echo base_url();?>ifms.php/partner/clear_bank_transactions/'+hID+'/'+state+'/<?php echo date('Y-m-t',$tym);?>';	
					$.ajax({
							url: url
						});	
	        
	   });
	   
$('.scroll').click(function(ev){
	
	var cnt = $('#cnt').val();
	
	if(cnt===""){
		cnt = "1";
	}
	
	var dt = '<?php echo $tym;?>';
	
	var flag = $(this).attr('id');
	
	var url = '<?php echo base_url();?>ifms.php/partner/scroll_cash_journal/'+dt+'/'+cnt+'/'+flag;
	
	$(this).attr('href',url);
});


$('#print_vouchers').click(function(){
	//alert("Under Construction");
	
	$('#cj_print_vouchers').submit();
	
});

$('.chq_dctb_vtype_change').on('click',function(){
	var voucher_number = $(this).data('voucher');
	var hid = $(this).data('hid');
	var cnfrm = prompt('If you are sure you want to change voucher ' + voucher_number + ' to UDCTB, please enter the cheque number below. This process is not reversible.');

	var url = "<?=base_url();?>ifms.php/partner/chq_dctb_vtype_change";
	var post_data = {'voucher_number':voucher_number,'hid':hid,'cheque_number':cnfrm};
	
	$.post(url,post_data,function(response){
		alert(response);
		window.location.reload();
	});
	
});
	
</script>				