<?php
//echo abs(floor($this->finance_model->bank_reconciled($this->session->center_id,date('Y-m-t',strtotime($param2)))));
$mfr_submitted = $this->finance_model->mfr_submitted($this->session->center_id,date('Y-m-d',strtotime($param2)));

$statement = $this->finance_model->statement_balance($this->session->center_id,$param2);

?>

<div class="row">
	<div class="col-sm-12">
		<?php echo form_open(base_url() . 'ifms.php/partner/financial_reports/bank_reconcile/'.$param2.'/'.$this->session->center_id , array('class' => 'form-horizontal form-groups-bordered validate', 'enctype' => 'multipart/form-data'));?>	
			<div class="panel panel-primary" data-collapsed="0">
        	<div class="panel-heading">
            	<div class="panel-title" >
            		<i class="entypo-plus-circled"></i>
					<?php echo get_phrase('bank_reconciliation');?>
            	</div>
            </div>
			<div class="panel-body"  style="max-width:50; overflow: auto;">	
				<div class="row  col-sm-offset-1 col-sm-10">
						<div class="form-group">
							<label class="control-label"><?php echo get_phrase('bank_statement_date');?></label>
								<div class="input-group">
																				
										<input type="text" name="bsDate" id="bsDate" class="form-control datepicker rec" required="required"  
													data-format="yyyy-mm-dd" data-start-date="" 
														data-end-date="" readonly="readonly">
										
										<div class="input-group-addon">
											<a href="#"><i class="entypo-calendar"></i></a>
										</div>
								</div>							
						</div>
						
						<div class="form-group">
							<label class="control-label"><?php echo get_phrase('journal_bank_balance');?></label>
							<input class="form-control rec" type="text" id=''  value="<?=round($this->finance_model->bank_balance($param2,$this->session->center_id),2)?>" readonly/>
						</div>	
						
						<div class="form-group">
							<label class="control-label"><?php echo get_phrase('statement_bank_balance');?></label>
							<input <?php if($mfr_submitted==='1') echo "readonly='readonly'";?> class="form-control rec" type="text" name="bankBal" id='bankBal' value="<?php echo $statement->amount;?>" data-validate="number,minlength[1],required" data-message-required="<?php echo get_phrase('value_required');?>"/>
						</div>
						
						<div class="form-group">
							<label class="control-label"><?php echo get_phrase('plus_deposit_in_transit');?></label>
							<input class="form-control rec" type="text" id="depTransit" name="depTransit" value="<?php echo $this->finance_model->sum_deposit_transit($param2,$this->session->center_id);?>" readonly/>
						</div>
						<div class="form-group">
							<label class="control-label"><?php echo get_phrase('less_outstanding_cheques');?></label>
							<input class="form-control rec" type="text" id='osChq' name="osChq"  value="<?php echo $this->finance_model->sum_outstanding_cheques($param2,$this->session->center_id);?>" readonly/>
						</div>
						<div class="form-group">
							<label class="control-label"><?php echo get_phrase('adjusted_bank_balance');?></label>
							<input class="form-control rec" type="text" id='adjBal' name="adjBal"  value="0" readonly/>
						</div>
						
						<div class="form-group">
							<label class="control-label"><?php echo get_phrase('journal_bank_balance');?></label>
							<input class="form-control rec" type="text" id='' name=""  value="<?php echo $this->finance_model->bank_balance($param2,$this->session->center_id);?>" readonly/>
						</div>	
						
						<div class="form-group">
							<label class="control-label col-sm-4"><?php echo get_phrase('bank_reconciled');?></label>
							<div class="col-sm-8">	
								<?php
									if(floor($this->finance_model->bank_reconciled($this->session->center_id,date('Y-m-t',strtotime($param2))))>0){
								?>
									<div class="label label-danger"><?=get_phrase('bank_reconciliation_failure');?></div>
								<?php
									}else{
								?>
									<div class="label label-success"><?=get_phrase('bank_reconciled_successful');?></div>
								<?php
									}
								?>
							</div>
						</div>												
					
				</div>
			
		</div>
		<div class="panel panel-footer">
			
			<button id="save" class="btn btn-primary btn-icon"><i class="fa fa-save"></i><?=get_phrase('update');?></button>
		</div>
	</div>
	</form>		
	</div>
	
</div>

<script>
	$(document).ready(function(){
		
		$('.datepicker').datepicker({
			format: 'yyyy-mm-dd',
			startDate:'<?php echo date("Y-m-01",strtotime($param2));?>',
			endDate:'<?php echo date("Y-m-t",strtotime("+5 days",strtotime($param2)));?>'
		});
		
		var bs = $('#bankBal').val();
		var dep = $('#depTransit').val();
		var oc = $('#osChq').val();
		if(dep===""){
			dep = $('#depTransit').val("0");
		}
		var adj = parseFloat(bs)+parseFloat(dep)-parseFloat(oc);
			$('#adjBal').val(adj.toFixed(2));		
			
		
		if($('#bankBal').val()>0){
			var dep = $('#depTransit').val();
			var oc = $('#osChq').val();
			var adj = parseFloat($('#bankBal').val())+parseFloat(dep)-parseFloat(oc);
			$('#adjBal').val(adj.toFixed(2));
		}
		
		$('#bankBal').keyup(function(){
			$('#save').attr('style','display:block;')
			if($(this).val()===''){
				$(this).val() = 0;
			}
			var dep = $('#depTransit').val();
			var oc = $('#osChq').val();
			var adj = parseFloat($(this).val())+parseFloat(dep)-parseFloat(oc);
			$('#adjBal').val(adj.toFixed(2));
		});
		
		$("#save").click(function(ev){
			var cnt = 0;
			$(".rec").each(function(i,el){
				if($(el).val()===""){
					cnt++;
					$(el).css('border','1px red solid');
				}
			});
			
			if(cnt>0){
				BootstrapDialog.show({
				     title:'Information',
					 message: '<?php echo get_phrase('one_or_more_fields_are_empty');?>'
				});
				
				ev.preventDefault();
			}
		});
		
	});
	
</script>
