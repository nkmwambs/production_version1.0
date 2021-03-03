<div class="row">
	<div class="col-sm-12">
		
			<div class="panel panel-primary" data-collapsed="0">
        	<div class="panel-heading">
            	<div class="panel-title" >
            		<i class="entypo-plus-circled"></i>
					<?php echo get_phrase('submit_mfr');?>
            	</div>
            </div>
			<div class="panel-body"  style="max-width:50; overflow: auto;">	
				<span style="color: red;font-weight: bold;">Please make sure all the following are provided before submitting the financial report, failure to which the report will not submit.
						<ol>
							<li>Proof of cash is successful</li>
							<li>Bank reconciliation is successful</li>
							<li>Bank statement is uploaded</li>
						</ol>
					</span>
					<?php 
	
						echo form_open(base_url() . 'ifms.php/partner/submit_mfr/'.$param2.'/'.$this->session->center_id , array('class' => 'form-wizard validate form-horizontal form-groups-bordered validate'));
					?>
						<div class="form-group">
						<label class="control-label col-sm-4"><?php echo get_phrase('cash_at_bank');?></label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="BC" id='BC' value="<?php echo number_format($this->finance_model->bank_balance(date('Y-m-01',strtotime($param2)),$this->session->center_id),2);?>" readonly="readonly" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>"/>
							</div>
						</div>
						
						<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('petty_cash');?></label>
								<div class="col-sm-8">
									<input class="form-control" type="text" id="PC" name="PC" value="<?php echo number_format($this->finance_model->petty_cash_balance(date('Y-m-01',strtotime($param2)),$this->session->center_id),2);?>" readonly/>
								</div>
						</div>
						
						<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('fund_balance');?></label>
								<div class="col-sm-8">
									<input class="form-control" type="text" id="" name="" value="<?=number_format($this->finance_model->total_months_closing_balance($this->session->center_id,$param2),2);?>" readonly/>
								</div>
						</div>
						
						
						
						<div class="form-group">
							<label class="control-label col-sm-4"><?php echo get_phrase('proof_of_cash');?></label>
							<div class="col-sm-8">	
								<?php
									if($this->finance_model->proof_of_cash($this->session->center_id,date('Y-m-01',strtotime($param2)))<>0){
								?>
									<div class="label label-danger"><?=get_phrase('proof_of_cash_failure');?></div>
								<?php
									}else{
								?>
									<div class="label label-success"><?=get_phrase('proof_of_cash_successful');?></div>
								<?php
									}
								?>
							</div>
					</div>
					
					<div class="form-group">
							<label class="control-label col-sm-4"><?php echo get_phrase('adjusted_bank_balance');?></label>
							<div class="col-sm-8">
								<input class="form-control" type="text" id='adjBal' name="adjBal"  value="<?=number_format($this->finance_model->adjusted_bank_balance($param2,$this->session->center_id),2);?>" readonly/>
							</div>
					</div>
						
					<div class="form-group">
							<label class="control-label col-sm-4"><?php echo get_phrase('journal_bank_balance');?></label>
							<div class="col-sm-8">
								<input class="form-control" type="text" id='' name=""  value="<?php echo number_format($this->finance_model->bank_balance($param2,$this->session->center_id),2);?>" readonly/>
							</div>
					</div>	
					
					<div class="form-group">
							<label class="control-label col-sm-4"><?php echo get_phrase('bank_reconciled');?></label>
							<div class="col-sm-8">	
								<?php
									if(abs(floor($this->finance_model->bank_reconciled($this->session->center_id,date('Y-m-t',strtotime($param2)))))>0){
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
					
					<div class="form-group">
						<div class="col-sm-12">
							<?php
								echo list_s3_uploaded_documents($this->finance_model->uploaded_bank_statements($this->session->center_id,strtotime($param2)));
							?>
						</div>
					</div>
					
					
					<div class="form-group">
							<label class="control-label col-sm-4"><?php echo get_phrase('bank_statement');?></label>
							<div class="col-sm-8">	
								<?php
									if($this->finance_model->check_bank_statement($this->session->center_id,date('Y-m-t',strtotime($param2)))<>0){
								?>
									<div class="label label-success"><?=get_phrase('bank_statement_uploaded');?></div>
								<?php
									}else{
								?>
									<div class="label label-danger"><?=get_phrase('bank_statement_not_uploaded');?></div>
								<?php
									}
								?>
							</div>
					</div>
					
					
					<div class="form-group">
						<div class="col-sm12">
							<div class="btn btn-primary" onclick="confirm_dialog('<?php echo base_url();?>ifms.php/partner/submit_mfr/<?=$this->session->center_id;?>/<?php echo strtotime($param2);?>',true);"><?=get_phrase('submit');?></div>
						</div>
					</div>	
					
					</form>
			</div>
			
			</div>
		</div>
	</div>
		
	
<script>

		
</script>