<?php 


// $tym = date('Y-m-t',$tym);

// $pc = $this->finance_model->petty_cash_balance($tym,$this->session->center_id);

// $bank = $this->finance_model->bank_balance($tym,$this->session->center_id);

// $fund_balance = $this->finance_model->sum_months_opening_revenues($this->session->center_id,$tym);

// $unapproved_budget = $this->finance_model->unapproved_budget_items($this->session->center_id,$tym);

?>
<hr />
<div class="row">
	<div class="col-sm-6">
		<div class="form-group">
			<label class="control-label col-sm-3">Search a Voucher</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="VNumber" placeholder="Enter a voucher number" />
			</div>
			<div class="col-sm-2">
				<div id="go_btn" class="btn btn-primary">Go</div>
			</div>
		</div>
	</div>
</div>

<hr />

<div class="row">
  <div class="col-sm-12">
    <div class="well">
      <h1><?= date('F, d Y') ?></h1>
      <h3>Welcome to the site <strong><?= $this->session->userfirstname; ?></strong></h3>
    </div>
  </div>
</div>
                    	
 <!-- <div class="row">
  <div class="col-md-4">
    <div class="tile-stats tile-red">
		<div class="icon"><i class="fa fa-group"></i></div>
			<div class="num" data-start="0" data-end="<?php echo $pc;?>" 
				data-postfix="" data-duration="1500" data-delay="0">0</div>
				                    
			<h3><?php echo get_phrase('petty_cash');?></h3>
			<p><?=get_phrase('balance');?></p>
	</div>
  </div>
  
   <div class="col-md-4">
    <div class="tile-stats tile-red">
		<div class="icon"><i class="fa fa-group"></i></div>
			<div class="num" data-start="0" data-end="<?php echo $bank;?>" 
				data-postfix="" data-duration="1500" data-delay="0">0</div>
				                    
			<h3><?php echo get_phrase('bank');?></h3>
			<p><?=get_phrase('balance');?></p>
	</div>
  </div>

   <div class="col-md-4">
    <div class="tile-stats tile-red">
		<div class="icon"><i class="fa fa-group"></i></div>
			<div class="num" data-start="0" data-end="<?php echo $fund_balance;?>" 
				data-postfix="" data-duration="1500" data-delay="0">0</div>
				                    
			<h3><?php echo get_phrase('fund');?></h3>
			<p><?=get_phrase('balance');?></p>
	</div>
  </div>
    		  
</div>
                        
<div class="row">
     <div class="col-md-4">
	    <div class="tile-stats tile-red">
			<div class="icon"><i class="fa fa-group"></i></div>
				<div class="num" data-start="0" data-end="<?php echo $unapproved_budget;?>" 
					data-postfix="" data-duration="1500" data-delay="0">0</div>
					                    
				<h3><?php echo get_phrase('unapproved');?></h3>
				<p><?=get_phrase('budget');?></p>
		</div>
  </div>
</div>  	                        -->
                 

<script>
$(document).ready(function() {
	
	$("#go_btn").click(function(){
		var VNum = $("#VNumber").val();
		
		showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_search_voucher/'+VNum);
	});
	
	  
});
</script>

  
