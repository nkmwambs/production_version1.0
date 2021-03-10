<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-primary">
						
		<div class="panel-heading">
			<div class="panel-title"><?=get_phrase('budget_summary')?></div>						
		</div>
								
		<div class="panel-body" style="overflow-y: auto;">
		
		<div class="row">
			<div class="col-sm-12">
				<button onclick="PrintElem('#summary-chart');" class="btn btn-success btn-icon pull-right"><i class="fa fa-print"></i><?=get_phrase('print');?></button>
			</div>
			
			<div class="col-sm-12">
					<form>
						 <div class="form-group">
						 	<label class="control-label col-sm-3"><?=get_phrase('financial_year');?></label> 
						  	<div class="col-sm-4">
							  <div class="input-group col-sm-5 col-sm-offset-1">
							    <a href="" id="prev_fy" class="input-group-addon scroll-fy"><i class="glyphicon glyphicon-minus"></i></a>
							    <input id="scrolled_fy" value="<?=$fyr;?>" type="text" class="form-control text-center" name="scrolled_fy" placeholder="FY" readonly="readonly">
							    <a href="" id="next_fy" class="input-group-addon scroll-fy"><i class="glyphicon glyphicon-plus"></i></a>
							  </div>
							</div>
						</div>  
					</form>
			</div>
		</div>

		<hr/>
					<div id="summary-chart">
						<?php
							$rev_accs = $this->finance_model->budgeted_revenue_accounts();
							//print_r($rev_accs);
							foreach($rev_accs as $rev):
						?>
						<span style="font-weight: bold;font-size: 15pt;"><?=$rev->AccText;?> - <?=$rev->AccName;?> <?=get_phrase('budget_summary');?></span>
						
						<table class="table table-striped table-bordered display">
				
							<thead>
								<tr>
									<th><?=get_phrase('revenue_account');?></th>
									<th><?=get_phrase('annual_total');?></th>
									<?php
										$months = $this->finance_model->months_in_year($this->session->center_id);
										
										$cnt_months = range(1, 12);
										
										foreach($cnt_months as $cnt):
									?>
										<th style="text-align: center;"><?=$months['month_'.$cnt."_amount"];?></th>												
										
									<?php
										endforeach;
									?>
						
								</tr>
							</thead>
							<tbody>
								<?php
									$exp_accs = $this->finance_model->expense_accounts($rev->accID);
									
									foreach($exp_accs as $exp):
										if($this->finance_model->plans_per_account($fyr,$this->session->center_id,$exp->AccNo)>0){
								?>
									<tr>
										<td><?=$exp->AccText;?></td>
										<td style="font-weight: bold;text-align: right;"><?=number_format($this->finance_model->plans_per_account($fyr,$this->session->center_id,$exp->AccNo),2)?></td>
										
								<?php
									foreach($cnt_months as $spread):
								?>
									<td><?=number_format($this->finance_model->plans_per_account_per_month($fyr,$this->session->center_id,$exp->AccNo,$spread),2);?></td>								
								<?php
									endforeach;
								?>		
										
									</tr>
								<?php
									}
									endforeach;	
								?>
								
							</tbody>
							<tfoot>
								<tr>
									<td style="font-weight: bold;"><?=get_phrase('total');?></td>
									<td style="text-align: right;font-weight: bold;"><?=number_format($this->finance_model->plans_annual_totals($fyr,$this->session->center_id,$rev->accID),2);?></td>
									
									<?php
										foreach($cnt_months as $spread_footer):
									?>
										<td><?=number_format($this->finance_model->plans_per_month($fyr,$this->session->center_id,$rev->accID,$spread_footer),2);?></td>
									<?php
										endforeach;
									?>
									
								</tr>
							</tfoot>
							
						</table>
						<?php
							endforeach;
						?>
					</div>
					</div>
				</div>
		</div>
	</div>
	
<script>

	$('.scroll-fy').click(function(){
		var fy = $(this).siblings('input').val();
		
		if($(this).attr('id')==='next_fy'){
			$(this).siblings('input').val(parseInt(fy)+1); 
		}else{
			$(this).siblings('input').val(parseInt(fy)-1);
		}
		
		
		$(this).attr('href','<?php echo base_url();?>ifms.php/partner/scroll_budget_summary/'+$(this).siblings('input').val());
		
	});
	
function PrintElem(elem)
    {
        $(elem).printThis({ 
		    debug: false,              
		    importCSS: true,             
		    importStyle: true,         
		    printContainer: false,       
		    loadCSS: "", 
		    pageTitle: ".",             
		    removeInline: false,        
		    printDelay: 333,            
		   	header:null,            
		    formValues: true          
		});
    }
</script>	
					