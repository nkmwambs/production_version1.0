<?php
/**Spread Incomes and Expenses **/
$month_rev = $this->finance_model->months_income_accounts_utilized($project,$tym);

$month_exp = $this->finance_model->months_expenses_accounts_utilized($project,$tym);

//print_r($month_exp);

/**Spread Incomes and Expenses **/

$bank_reconcile_check = abs(floor($this->finance_model->bank_reconciled($project,date('Y-m-t',$tym))));
$rec_chk = "Ok";
$rec_color = "success";
if($bank_reconcile_check<>0){
	$rec_chk = "Error";
	$rec_color = "warning";
}

//echo $bank_reconcile_check; 

$proof_of_cash_check = $this->finance_model->proof_of_cash($project,date('Y-m-t',$tym));//(round($this->db->select_sum('amount')->get_where('cashbal',array('project_id'=>$this->session->userdata('center_id'),'month'=>date('Y-m-t',$tym)))->row()->amount,2)-round($this->finance_model->total_cash(date('Y-m-01',$tym),$project),2));

$proof_chk = "Ok";
$proof_color = "success";
if($proof_of_cash_check <> 0){
	$proof_chk = "Error";
	$proof_color = "warning";
}

//$hide_status = 'display:block';
//if($this->finance_model->mfr_submitted($project,date('Y-m-t',$tym))=== '1'){
	$hide_status = 'display:none';
//}

?>

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
			<!--cash journal-->
			<?php if($this->finance_model->check_opening_balances($project)===true){ ?>	
					
					<button class="btn btn-primary btn-icon col-sm-offset-1" id="print_vouchers"><i class="fa fa-print"></i><?=get_phrase('print_vouchers');?></button>
					
					<a href="<?=base_url();?>ifms.php/accountant/dashboard/<?=$tym;?>" class="btn btn-primary btn-icon pull-left"><i class="fa fa-tachometer"></i><?php echo get_phrase('dashboard');?></a>

					<div class="col-sm-offset-1 btn-group right-dropdown">
								<button type="button" id="" class="btn btn-blue"><?php echo get_phrase('financial_reports');?></button>
								<button type="button" id="" class="btn btn-blue dropdown-toggle" data-toggle="dropdown">
									<span class="caret"></span>
								</button>
								
								<ul class="dropdown-menu dropdown-blue" role="menu">

									<li  style="">
										<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_outstanding_cheques/<?php echo date('Y-m-t',$tym);?>/<?=$project;?>')"><?php echo get_phrase('outstanding_cheques');?></a>
									</li>
									
									<li  style="" class="divider"></li>
									
									<li style="">
										<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_transit_deposits/<?php echo date('Y-m-t',$tym);?>/<?=$project;?>')"><?php echo get_phrase('deposits_in_transit');?></a>
									</li>
									
									<li style="" class="divider"></li>
									
									<li  style="">
										<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_cleared_effects/<?php echo date('Y-m-t',$tym);?>/<?=$project;?>')"><?php echo get_phrase('cleared_effects');?></a>
										
									</li>
									
									<li style="<" class="divider"></li>
									
									<li style="">
										<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_bank_reconcile/<?php echo date('Y-m-t',$tym);?>/<?=$project;?>')"><?php echo get_phrase('bank_reconciliation');?> <span class="badge badge-<?=$rec_color;?>"><?=$rec_chk;?></span></a>
									</li>
									
									<li style="" class="divider"></li>
									
									<li>
										<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_variance_explanation/<?php echo date('Y-m-t',$tym);?>/<?=$project;?>')"><?php echo get_phrase('variance_explanation');?></a>
									</li>
									
									<li class="divider"></li>
									
									<li style="">
										<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_proof_of_cash/<?php echo date('Y-m-t',$tym);?>/<?=$project;?>')"><?php echo get_phrase('proof_of_cash');?> <span class="badge badge-<?=$proof_color;?>"><?=$proof_chk?></span></a>
										
									</li>
									
									<li style="" class="divider"></li>
									
									<li>
										<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_fund_balances/<?php echo date('Y-m-t',$tym);?>/<?=$project;?>')"><?php echo get_phrase('fund_balance_report');?></a>
										
									</li>
									
									<li class="divider"></li>
									
									<li>
										<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_expense_report/<?php echo date('Y-m-t',$tym);?>/<?=$project;?>')"><?php echo get_phrase('expense_report');?></a>
										
									</li>
									
									<li class="divider"></li>
									
									<li>
										<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_fund_ratios/<?php echo date('Y-m-t',$tym);?>/<?=$project;?>')"><?php echo get_phrase('financial_ratios');?></a>
										
									</li>
									
																	
									<!--<li class="divider"></li>
									
									<li style="">
										<a href="<?php echo base_url();?>ifms.php/partner/bank_statements/<?php echo date('Y-m-t',$tym);?>"><?php echo get_phrase('upload_bank_statements');?></a>
									</li>
									
									<li style="<?=$hide_status;?>" class="divider"></li>
									
									<li class="divider"></li>-->
									
									<!--<li style="<?=$hide_status;?>">
										<a href="#" onclick="return function submit_mfr();"><?php echo get_phrase('submit_financial_report');?></a>
										
									</li>-->
									
									<!--<li>
										<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_financial_report/<?php echo date('Y-m-t',$tym);?>')"><?php echo get_phrase('view_report');?></a>
									</li>-->
									<!--
									<li class="divider"></li>
									
									<li>
										<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_report_validate/<?php echo date('Y-m-t',$tym);?>')"><?=get_phrase('Submit_MFR');?></a>
									</li>
									-->
									
								</ul>
							</div>
							
							<div class="btn btn-default pull-right col-sm-3"><a href="#" class="fa fa-backward scroll" id="prev"></a> <?=get_phrase('you_are_in');?> <?=date('F Y',$tym);?> <input type="text" class="form-control col-sm-1" id="cnt" placeholder="<?=get_phrase('enter_number_of_months');?>"/> <a href="#" class="fa fa-forward scroll" id="next"></a></div>
				
				<br/><br/><hr/>
				

						<?php
							$max_col = 12;
							
							/** Journal Records **/
							$condition = "Month(`TDate`)='".date('m',$tym)."' AND Fy='".get_fy(date('d-m-Y',$tym),$project)."' AND icpNo='".$project."'";
							$records = $this->db->where($condition)->get('voucher_header')->result_array();
							
							//print_r($records);
						?>
						
						<?php echo form_open(base_url() . 'ifms.php/facilitator/multiple_vouchers/'.$tym."/".$project , array('id'=>'cj_print_vouchers'));?>
						
						<!-- Summary table -->
						<a href="#" class="fa fa-print" onclick="PrintElem('#balance_summary');"><?=get_phrase('print');?></a>
						
						<div id="balance_summary">
							
						<table class="table table-bordered">
							<thead>
								<tr>
									<th colspan="6" style="text-align: center;font-weight: bold;"><?php echo $project;?><br/><?=get_phrase('cash_journal');?></th>
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
										$begin_bank = $this->finance_model->opening_bank_balance(date('Y-m-t',$tym),$project);										
										$begin_pc = $this->finance_model->opening_pc_balance(date('Y-m-t',$tym),$project);
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
										$condition_bank_income = $condition." AND (VType='CR' OR VType='PCR')";
										$sum_bank_income = $this->db->where($condition_bank_income)->select_sum('totals')->get('voucher_header')->row()->totals;
										
										//SUM Bank Payments
										$condition_bank_payment = $condition." AND (VType='CHQ' OR VType='BCHG')";
										$sum_bank_payment = $this->db->where($condition_bank_payment)->select_sum('totals')->get('voucher_header')->row()->totals;
										
										//SUM Bank Balance
										$sum_bank_balance = $begin_bank+$sum_bank_income-$sum_bank_payment;						
										
										
									
										/** SUM ALL PC Income Vouchers **/
										$sum_pc_income = 0;
										$sum_pc_payment = 0;
										foreach($records as $rw):
											$cond_inc = "(AccNo='2000' OR AccNo='2001')  AND hID=".$rw['hID'];
											$sum_pc_income+=$this->db->select_sum('Cost')->where($cond_inc)->get('voucher_body')->row()->Cost;
											
											$cond_pay = "(VType='PC' OR VType='PCR') AND hID=".$rw['hID'];
											$sum_pc_payment+=$this->db->select_sum('Cost')->where($cond_pay)->get('voucher_body')->row()->Cost;
											
										endforeach;
										
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
						<!-- End Summary table -->
						
						<div class="btn btn-warning" id="toggleColumns"><?=get_phrase('show_spread');?></div>
						<hr />
						<!-- Cash Journal --->
						
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
									<th><?php echo get_phrase('chq_no');?></th>
									<th><?php echo get_phrase('bank_deposits');?></th>
									<th><?php echo get_phrase('bank_payments');?></th>
									<th><?php echo get_phrase('bank_balance');?></th>
									<th><?php echo get_phrase('cash_deposits');?></th>
									<th><?php echo get_phrase('cash_payments');?></th>
									<th><?php echo get_phrase('cash_balance');?></th>
									<!--<th><?php echo get_phrase('action');?></th>-->
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
												
												if($row['VType']==='CHQ' || $row['VType']==='BCHG' || $row['VType']==='CR'){
													$chk = 'checked';
														if($row['ChqState']==='1'){
															$chk = "";
														}
		
											?>
												<td>
													<?php
													if($this->finance_model->editable($project,date('Y-m-t',$tym))===0){
													?>
														<div id="<?php echo $row['hID'];?>" class="make-switch switch-small clr" data-on-label="<?php echo $row['VType'];?>" data-off-label="<?php echo $row['VType'];?>">
														<input type="checkbox" <?=$chk;?> value="<?php echo $row['VNumber'];?>" disabled/>
													<?php
													}else{
													?>
														<div id="<?php echo $row['hID'];?>" class="make-switch switch-small clr" data-on-label="<?php echo $row['VType'];?>" data-off-label="<?php echo $row['VType'];?>">
														<input type="checkbox" <?=$chk;?> value="<?php echo $row['VNumber'];?>"/>
													<?php
													}
													?>	
														
													</div>
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
											<td><?=$row['VType'];?></td>												
											
											<td><?php echo $row['Payee'];?></td>
											<td>
																							
												<div id="" class="btn btn-green popover-primary popup-ajax" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?=get_phrase('click_on_the_button_to_view_a_voucher');?>" data-original-title="<?=get_phrase('tooltip');?>" >
													<input type="checkbox" class="chk_voucher" name="voucher_<?=$row['VNumber']?>" value="<?=$row['VNumber']?>" id="<?=$row['VNumber']?>"/>
													<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_view_voucher/<?php echo $row['hID'];?>/<?=$row['icpNo'];?>');">
														<?php echo $row['VNumber'];?>
													</a>
													
												</div>
												
												
											</td>
											<td><?php echo $row['TDescription'];?></td>
											<?php
												$mixed_chq = explode("-", $row['ChqNo']);
											?>
											<td><?php echo $mixed_chq[0];?></td>
											
											<!-- Bank Income and Expenses -->
											
											<?php
												$cr = '0';
												$chq = '0';
												
												if($row['VType']==='CR'||$row['VType']==='PCR') $cr = $this->db->select_sum('Cost')->get_where('voucher_body',array('hID'=>$row['hID']))->row()->Cost;
												if($row['VType']==='CHQ' || $row['VType'] ==='BCHG') $chq = $this->db->select_sum('Cost')->get_where('voucher_body',array('hID'=>$row['hID']))->row()->Cost;;
												
												$bank_balance += $cr-$chq; 
												
											?>
											<td><?php echo number_format($cr,2);?></td>
											<td><?php echo number_format($chq,2);?></td>
											<td><?php echo number_format($begin_bank+$bank_balance,2);?></td>
											
											<!-- Petty Cash Income and Expenses -->
											
											<?php
														
														$this->db->where_not_in('AccNo',['2000','2001']);
														$pc_get_body = $this->db->select_sum('Cost')->select(array("AccNo","VType"))	
														->get_where("voucher_body",array("hID"=>$row['hID']))->row_array();
														
														$this->db->where_in('AccNo',['2000','2001']);
														$pcr_get_body = $this->db->select_sum('Cost')->select(array("AccNo","VType"))
														->get_where("voucher_body",array("hID"=>$row['hID']))->row_array();
														
															$pcr = '0';
															$pc = '0';
															
														//foreach($get_body as $rows):
															
															if($pcr_get_body['AccNo']==='2000' || $pcr_get_body['AccNo']==='2001') $pcr = $pcr_get_body['Cost'];
															if($pc_get_body['VType']==='PC' || $pc_get_body['VType']==='PCR' || $pc_get_body['VType'] === 'UDCTC') $pc = $pc_get_body['Cost'];
															
															$pc_balance += $pcr-$pc; 
													?>
														<td><?php echo number_format($pcr,2);?></td>
														<td><?php echo number_format($pc,2);?></td>
														<td><?php echo number_format($begin_pc+$pc_balance,2);?></td>
													<?php		
														//endforeach;
													?>
												
											<!--<td>
												<div class="btn-group">
								                    <button id="" type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
								                        Action <span class="caret"></span>
								                    </button>
								                    <ul class="dropdown-menu dropdown-default pull-right" role="menu">
								                        <!-- Delete a Voucher 
								                      
								                        <li>
								                        	<a href="#" id="" onclick="delete_voucher('<?=$project;?>','<?=$row['hID'];?>');">
								                            	<i class="entypo-cancel"></i>
																	<?php echo get_phrase('delete_this_voucher');?>
								                               	</a>
								                        </li>
								                        <li class="divider"></li>
								                        
								                        <li>
								                        	<a href="#" id="" onclick="delete_all_vouchers('<?=$project;?>','<?=$row['hID'];?>');">
								                            	<i class="entypo-trash"></i>
																	<?php echo get_phrase('delete_this_and_above');?>
								                               	</a>
								                        </li>
								                        <li class="divider"></li>
								                     </ul>
								                   </div>     
											</td>	-->
											
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
						
						</form>
											
					</div>
			
			
			<!--End of cash journal-->
			<?php } else{ echo get_phrase('please_set_opening_balances');}?>
			</div>
		</div>
	</div>
</div>
		
<script>

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
	
	var url = '<?php echo base_url();?>ifms.php/facilitator/scroll_cash_journal/<?=$project;?>/'+dt+'/'+cnt+'/'+flag;
	
	$(this).attr('href',url);
});


$('#print_vouchers').click(function(){
	//alert("Under Construction");
	
	$('#cj_print_vouchers').submit();
	
});

function delete_all_vouchers(project,hID){
	var result = window.confirm('<?php echo get_phrase("Are_you_sure_you_want_to_delete_this_voucher_and_above?");?>');
        
            if(!result) {
		                 BootstrapDialog.show({
		                 	title:'Information',
				            message: '<?php echo get_phrase('process_aborted');?>'
				        });
	            }else{
					var url = '<?=base_url();?>ifms.php/facilitator/delete_all_vouchers/'+project+'/'+hID+'/<?=$tym;?>';
					
					$.ajax({
						url:url,
						success:function(response){
							BootstrapDialog.show({
								title:'Alert',
								message:response
							});
							
							location.reload();
						}
						
					});
				}	
}

function delete_voucher(project,hID){
	
			var result = window.confirm('<?php echo get_phrase("Are_you_sure_you_want_to_delete_this_voucher?");?>');
        
            if(!result) {
		                 BootstrapDialog.show({
		                 	title:'Information',
				            message: '<?php echo get_phrase('process_aborted');?>'
				        });
	            }else{
					var url = '<?=base_url();?>ifms.php/facilitator/delete_voucher/'+project+'/'+hID+'/<?=$tym;?>';
					
					$.ajax({
						url:url,
						success:function(response){
							BootstrapDialog.show({
								title:'Alert',
								message:response
							});
							
							location.reload();
						}
					});
				}	
				
}



</script>				