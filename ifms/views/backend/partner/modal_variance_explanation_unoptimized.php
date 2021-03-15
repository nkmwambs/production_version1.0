<?php

$rev_acc = $this->finance_model->budgeted_revenue_accounts();

//echo $this->finance_model->months_budget_variance_percent_per_expense_account($this->session->center_id,get_fy($param2,$this->session->center_id),1,$param2);

//echo $this->finance_model->months_budget_variance_per_expense_account($this->session->center_id,get_fy($param2,$this->session->center_id),1,$param2);
?>


	<div class="form-group">
		<label class="col-sm-4 control-label">Select Revenue Account</label>
		<div class="col-sm-5">
			<select id="accounts" class="form-control">
				<option><?php echo get_phrase('select');?></option>
				
				<?php foreach($rev_acc as $acc):?>
					<option value="<?=$acc->AccNo;?>"><?php echo $acc->AccName." (".$acc->AccText.")";?></option>
				<?php endforeach;?>
			</select>
		</div>
		<div class="col-sm-3"><button id="account_btn" class="btn btn-primary btn-icon"><i class="entypo-search"></i>Search</button></div>
	</div>

<?php
if($param3>0){
	
	$account_name = $this->db->get_where('accounts',array('AccNo'=>$param3))->row()->AccName;
	
	$accNo = $this->db->get_where('accounts',array('AccNo'=>$param3))->row()->AccText;
	
	$parentAccID = $this->db->get_where('accounts',array('AccNo'=>$param3))->row()->accID;
	
	$exp = $this->db->get_where('accounts',array('parentAccID'=>$parentAccID))->result_object();
	
	//echo $param3;

?>

<div class="row">
	<div class="col-sm-12">
	
			<div class="panel panel-primary" data-collapsed="0">
        	<div class="panel-heading">
            	<div class="panel-title" >
            		<i class="entypo-plus-circled"></i>
					<?php echo get_phrase('variance_explanation').' - '.$account_name;?>
            	</div>
            </div>
			<div class="panel-body"  style="max-width:50; overflow: auto;">	
			
			<div class="row">
					<div class="col-sm-12">
						<a href="#" class="fa fa-print" onclick="PrintElem('#varTable');"><?=get_phrase('print');?></a>
					</div>
				</div>		
				
		<?php echo form_open(base_url() . 'ifms.php/partner/variance_explanation/' , array('id'=>'frm_variance','class' => 'form-horizontal'));?>			
				<table class="table table-hover" id="varTable">
							<thead>
								<tr>
									<td><?php echo get_phrase('account');?></td>
									<td><?php echo get_phrase('variance');?></td>
									<td><?php echo get_phrase('%_variance');?></td>
									<td><?php echo get_phrase('explanation');?></td>
								</tr>
							</thead>
							<tbody>
								<?php 
									$counter = 0;
									
									foreach($exp as $row):
									
									$per_var = $this->finance_model->months_budget_variance_percent_per_expense_account($this->session->center_id,get_fy($param2,$this->session->center_id),$row->AccNo,$param2);//$this->finance_model->percentage_budget_variance_per_account($param2,$row['AccNo']);
									$var = $this->finance_model->months_budget_variance_per_expense_account($this->session->center_id,get_fy($param2,$this->session->center_id),$row->AccNo,$param2);//$this->finance_model->budget_variance_per_account($param2,$row['AccNo']);
									
									if(($per_var>10||$per_var<-10)&& $var !==0){
								?>

										<tr>
											<td><?php echo $row->AccText." - ".$row->AccName;?></td>
											<input type="hidden" value="<?php echo $this->session->center_id;?>" name="rec[<?= $counter?>][icpNo]"/>
											<input type="hidden" value="<?php echo $row->AccNo;?>" name="rec[<?= $counter?>][AccNo]"/>
											<input type="hidden" value="<?php echo $param2;?>" name="rec[<?= $counter?>][reportMonth]"/>
											<td><?php echo number_format($var,2);?></td>
											<td><?php echo number_format($per_var,2)."%";?></td>
											<td>
												<div class="form-group">
													<?php
														$var_statement = "";//$this->db->get_where('varjustify',array('icpNo'=>$this->session->icp_no,'reportMonth'=>$param2,'AccNo'=>$row['AccNo']))->row()->Details;
														
														if($this->db->get_where('varjustify',array('icpNo'=>$this->session->center_id,'reportMonth'=>$param2,'AccNo'=>$row->AccNo))->num_rows()>0){
															$var_statement = $this->db->get_where('varjustify',array('icpNo'=>$this->session->center_id,'reportMonth'=>$param2,'AccNo'=>$row->AccNo))->row()->Details;
														}
														
													?>
													<textarea class="form-control detail" rows="5" id="" name="rec[<?= $counter?>][Details]"  data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>"><?=$var_statement;?></textarea>
												</div>
											</td>
										</tr>
									
								<?php 
									}else{
										
								?>
									<tr>
										<td><?php echo $row->AccText." - ".$row->AccName;?></td>
										<td><?php echo number_format($var,2);?></td>
										<td><?php echo number_format($per_var,2)."%";?></td>
										
										<td><div class="well well-primary">Variance is within the acceptable limit</div></td>
										
									</tr>
								<?php
											
								} 
									$counter++;
									endforeach;
								?>
							</tbody>
						</table>
					</form>										
			
			</div>
			
			<div class="panel panel-footer">
			<button class="btn btn-primary btn-icon" id="save"><i class="fa fa-save"></i>Save</button>
		</div>
	</div>
			
	</div>
	
</div>

<?php
}
?>

<script>
	$(document).ready(function(){
		
		$('#save').click(function(ev){

			var cnt = 0;
			
			$(".detail").each(function(i,el){

				
				if($(el).val()===""){
					cnt++;
					
					$(el).css('border','1px red solid');
				}
				
			});
			
		if(cnt>0){
					BootstrapDialog.show({
				        title:'Information',
						message: '<?php echo get_phrase('provide_all_explanations');?>'
					});
		}else{	
			var postData = $('#frm_variance').serializeArray();
		    var formURL = $('#frm_variance').attr("action");
		    $.ajaxSetup({ cache: false });
		    $.ajax(
		    {
		        url : formURL,
		        type: "POST",
		        data : postData,
		        success:function(data, textStatus, jqXHR) 
		        {
					
		            //alert(data);
		            
		            $('#modal_ajax').modal('toggle');
		            
		        },
		        error: function(jqXHR, textStatus, errorThrown) 
		        {
		            //if fails
		            alert(textStatus+' - '+errorThrown);     
		            
		        }
		    });
		    //e.preventDefault(); //STOP default action
		    //e.unbind(); //unbind. to stop multiple form submit.
		  }  
		   
		});
	
		$('#account_btn').click(function(){
			var rev_id = $('#accounts').val();
			showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_variance_explanation/<?php echo $param2;?>/'+rev_id)
		});
		
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