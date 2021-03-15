<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-primary" data-collapsed="0">
        	<div class="panel-heading">
            	<div class="panel-title" >
            		<i class="fa fa-vcard"></i>
					<?php echo get_phrase('budget_variance_explanation');?>
            	</div>
            </div>
			<div class="panel-body"  style="max-width:50; overflow: auto;">	
			
						
			<div class="row">
					<div class="col-sm-12">
						<a href="#" class="fa fa-print" onclick="PrintElem('#frm_expenses');"><?=get_phrase('print');?></a>
					</div>
			</div>	
			<div id="frm_expenses">		
				<?php echo form_open('', array('id'=>'frm_expense_accounts','class' => 'form-horizontal'));?>
			
					<div class="form-group">
						<label class="control-label col-sm-4"><?=get_phrase('revenue');?></label>
						<div class="col-sm-8">
							<select class="form-control" id="revenue_id" name="revenue_id">
								<option><?=get_phrase('select');?></option>
								<?php
									$rev = $this->finance_model->get_income_account_with_expenses($this->session->center_id,$param2);
									
									foreach($rev as $account_number => $row):
									
								?>
									<option value="<?=$account_number;?>"><?=$row['account_code'];?> - <?=$row['account_name'];?></option>
								<?php
								
									endforeach;
								?>
							</select>		
						</div>				
					</div>	
					
			</form>
				
			
			<div class="row">
				<div class="col-sm-12" id="expense_data">
					
				</div>
			</div>	
			
			</div>
			
			</div>
		</div>
	</div>
</div>


<script>
$(document).ready(function(){
	$('.expense_report').css('display','none');
});	

$('#revenue_id').change(function(ev){

	var rpt_id = $('#revenue_id').val();
	
	var url = '<?=base_url();?>ifms.php/partner/load_variance_explanation/<?=$this->session->center_id?>/'+rpt_id+'/<?=$param2?>';
	
	jQuery('#expense_data').html('<div style="text-align:center;margin-top:200px;"><img src="<?php echo base_url();?>uploads/preloader.gif" /></div>');
	
	$.ajax({
		url:url,
		success:function(response){
			$('#expense_data').html(response);
		}
	});
	
	ev.preventDefault();
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
</script>	