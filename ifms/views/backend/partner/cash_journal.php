<?php 

print_r($cash_journal);

extract($cash_journal);


$rec_chk = "Ok";
$rec_color = "success";

if(!$is_bank_reconciled){
		$rec_chk = "Error";
		$rec_color = "warning";
}


$proof_chk = "Ok";
$proof_color = "success";

if(!$is_proof_of_cash_correct){
	$proof_chk = "Error";
	$proof_color = "warning";
}

$hide_status = 'display:block';
if($is_mfr_submitted){
	$hide_status = 'display:none';
}

$tym = strtotime($period);

?>

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

					<div class="row">
						<div class="col-sm-12">
							<?php include "cash_journal_reports.php";?>
						</div>
					</div>

					<hr/>

					<div class="row">
						<div class="col-sm-12" style="text-align: center;">
							<?php 
								include "cash_journal_status_label.php";
							?>	
						</div>
					</div>

					<hr/>

					<div class="row">
						<div class="col-sm-12">
							<?php 
								include "cash_journal_buttons.php";
							?>
						</div>
					</div>

					<hr/>

					<?php  echo form_open(base_url() . 'ifms.php/partner/multiple_vouchers/'.$tym , array('id'=>'cj_print_vouchers'));?>
						<div class="row">
							<div class="col-sm-12">
								<?php 
									include "cash_journal_summary.php";
								?>
							</div>
						</div>

						<hr/>

						<div class="row">
							<div class="col-sm-12">
								<?php 
									include "cash_journal_view.php";
								?>
							</div>
						</div>

					</form>
				</div>
			</div>
			</div>	
	</div>	
</div>			