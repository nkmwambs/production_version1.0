<?php

	// $start_period_date = '2018-03-01';
	// $end_period_date = '2018-03-31';

	// $this->db->select(array('voucher_header.hID as voucher_id','voucher_header.TDate as voucher_date','voucher_header.VNumber as voucher_number'));
	// $this->db->select(array('voucher_header.Payee as payee','voucher_header.VType as voucher_type'));
	// $this->db->select(array('voucher_header.ChqNo as cheque_number','voucher_header.ChqState as clear_state'));
	// $this->db->select(array('voucher_header.clrMonth as clear_month','voucher_header.editable as is_editable'));
	// $this->db->select(array('voucher_header.TDescription as description'));
	// $this->db->select(array('accounts.AccNo as account_number','accounts.AccText as account_code',
	// 'accounts.AccName as account_name','accounts.AccGrp as account_group'));
	// $this->db->select_sum('Cost');
	// $this->db->where(array('voucher_header.TDate>='=>$start_period_date,'voucher_header.TDate<='=>$end_period_date));
	// $this->db->where(array('voucher_header.icpNo'=>$this->session->center_id));
	// $this->db->join('voucher_header','voucher_header.hID=voucher_body.hID');
	// $this->db->join('accounts','accounts.AccNo=voucher_body.AccNo');
	// $this->db->group_by(array('voucher_header.VNumber','voucher_body.AccNo'));
	// $vouchers_obj = $this->db->get('voucher_body');

	// print_r($vouchers_obj->result_array());

//print_r($this->finance_model->journal_records_spread());

extract($cash_journal);


$rec_chk = "Ok";
$rec_color = "success";

if (!$is_bank_reconciled) {
	$rec_chk = "Error";
	$rec_color = "warning";
}


$proof_chk = "Ok";
$proof_color = "success";

if (!$is_proof_of_cash_correct) {
	$proof_chk = "Error";
	$proof_color = "warning";
}

$hide_status = 'display:block';
if ($is_mfr_submitted) {
	$hide_status = 'display:none';
}

$tym = strtotime($period);

if($this->finance_model->check_opening_balances($this->session->center_id)){ 

?>

<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title">
					<i class="fa fa-vcard"></i>
					<?php echo get_phrase('cash_journal'); ?>
				</div>
			</div>
			<div class="panel-body" style="max-width:50; overflow: auto;">
				<div id="load_journal">

					<div class="row">
						<div class="col-sm-12">
							<?php include "cash_journal_reports.php"; ?>
						</div>
					</div>

					<hr />

					<div class="row">
						<div class="col-sm-12" style="text-align: center;">
							<?php
							include "cash_journal_status_label.php";
							?>
						</div>
					</div>

					<hr />

					<div class="row">
						<div class="col-sm-12">
							<?php
							include "cash_journal_buttons.php";
							?>
						</div>
					</div>

					<hr />

					<?php echo form_open(base_url() . 'ifms.php/partner/multiple_vouchers/' . $tym, array('id' => 'cj_print_vouchers')); ?>
					<div class="row">
						<div class="col-sm-12">
							<?php
							include "cash_journal_summary.php";
							?>
						</div>
					</div>

					<hr />

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
<?php 
	}else{
		echo get_phrase('please_set_opening_balances');
	}
?>


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

	$(document).ready(function() {
		var datatable = $('.datatable').DataTable({
			lengthMenu: [
				[25, 50, 100, 150, -1],
				[25, 50, 100, 150, "All"]
			],
			pageLength: 25,
			dom: '<"row"l><Bf><"col-sm-12"rt><ip>',
			pagingType: "full_numbers",
			ordering: false,
			buttons: [
				'csv', 'excel', 'print'
			],
			stateSave: true,
			oLanguage: {
				sProcessing: "<img src='<?php echo base_url(); ?>uploads/preloader4.gif'>"
			},
			processing: true, //Feature control the processing indicator.
			"columnDefs": [{
				"targets": [0], //first column / numbering column
				"orderable": false, //set not orderable
				"class": "details-control"
			}]
		});

		datatable.columns('.spread').visible(false);

		$("#toggleColumns").click(function(ev) {

			$(this).toggleClass('btn-success', 'btn-warning');

			if ($(this).hasClass('btn-success')) {
				datatable.columns('.spread').visible(true);
				$(this).html('<?= get_phrase("hide_spread"); ?>');

			} else {
				datatable.columns('.spread').visible(false);
				$(this).html('<?= get_phrase("show_spread"); ?>');

			}

		});


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
							url: url,
							success:function(response){
								alert(response);
							}
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

	});

	function PrintElem(elem) {
		$(elem).printThis({
			debug: false,
			importCSS: true,
			importStyle: true,
			printContainer: false,
			loadCSS: "",
			pageTitle: "<?php echo get_phrase('payment_voucher'); ?>",
			removeInline: false,
			printDelay: 333,
			header: null,
			formValues: true
		});
	}
</script>