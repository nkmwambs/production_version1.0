<?php
//echo base_url();
?>

<div class="row">
	<div class="col-sm-12">
		
			<div class="panel panel-info">
								
				<div class="panel-heading">
					<div class="panel-title"><?=get_phrase('new_claim');?></div>						
				</div>
										
				<div class="panel-body">
					
					<div class="col-sm-12" id="progress"></div>
					<hr />
					<?php //echo form_open(base_url() . 'claims.php/partner/medical_claims/add/' , array('id'=>'frm_medical_claim','class' => 'form-horizontal form-groups-bordered validate', 'enctype' => 'multipart/form-data'));?>	
					<?php echo form_open('' , array('id'=>'frm_medical_claim','class' => 'form-horizontal form-groups-bordered validate', 'enctype' => 'multipart/form-data'));?>
					
					<div class="col-sm-6">
					
							<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('ke_no');?></label>
								<div class="col-sm-8">
									<input type="text" required="required" class="form-control" name="proNo" id="proNo" readonly="readonly" value="<?=$center;?>"/>
								</div>
							</div>
							
							<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('cluster');?></label>
								<div class="col-sm-8">
									<input type="text" required="required" class="form-control" name="cluster" id="cluster" readonly="readonly" value="<?=$cluster;?>"/>
								</div>
							</div>
							
							<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('incidentID');?></label>
								<div class="col-sm-8">
									<input type="number" required="required" class="form-control reset" name="incidentID" id="incidentID" value="" placeholder="<?=get_phrase('numeric_value_only');?>"/>
								</div>
							</div>
							
							<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('child_no');?></label> 
								<div class="col-sm-8">
									<input type="number" required="required" class="form-control reset" name="childNo" id="childNo" value="" placeholder="<?=get_phrase('Beneficiary ID without the ICP ID');?>"/>
								</div>
							</div>
							
							<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('child_name');?></label>
								<div class="col-sm-8">
									<input type="text" class="form-control reset" required="required" name="childName" id="childName" readonly="readonly"/>
								</div>
							</div>
							
							<!--<<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('incident_type');?></label>
								<div class="col-sm-8">
									<select class="form-control" name="incident_type" id="incident_type" onchange="get_incident_category(this);">
										<option value=""><?php echo get_phrase('select');?></option>
										<option value="illness"><?php echo get_phrase('illness');?></option>
										<option value="injury"><?php echo get_phrase('injury');?></option>
									</select>
								</div>
							</div>
							
							<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('incident_category');?></label>
								<div class="col-sm-8">

									<select class="form-control" name="incident_sub_category_id" id="incident_sub_category_id" readonly="readonly" onchange="get_incident_sub_category(this);">
										<option value=""><?php echo get_phrase('select');?></option>
										
									</select>
								</div>
							</div>
							
							<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('incident');?></label>
								<div class="col-sm-8">

									<select class="form-control" name="illness_id" id="illness_id" readonly="readonly">
										<option value=""><?php echo get_phrase('select');?></option>
										
									</select>
								</div>
							</div>-->
							
							
							<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('diagnosis');?></label>
								<div class="col-sm-8">
									<textarea class="form-control reset" required="required" name="diagnosis" id="diagnosis" placeholder="<?=get_phrase("condition_description");?>"></textarea>
								</div>
							</div>
							
							
							<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('N.H.I.F');?></label>
								<div class="col-sm-8">
									<input type="text" class="form-control" required="required" name="nhif" id="nhif" value="0"/>
								</div>
							</div>     
							
							<?php
								//Check voucher for E320 in the last 60 days
								$valid_last_date = date("Y-m-d",strtotime($this->config->item('valid_claiming_days')." days ago"));
								$cond = " icpNo = '".$this->session->center_id."' AND 
								AccNo = ".$this->config->item('beneficary_medical_reimbursement_num_account')." AND TDate >= '".$valid_last_date."' ";
								$vouchers = $this->db->where($cond)->group_by('VNumber')->get('voucher_body')->result_object();
								
							?>
							<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('voucher_number');?> </label>
								<div class="col-sm-8">
									<!-- <input type="text" class="form-control" name="vnum" id="vnum"/> -->
									<select class="form-control reset" name="vnum" id="vnum" required="required">
										<option value=""><?=get_phrase('select');?></option>
										<?php 
											
											foreach($vouchers as $voucher):
											
											$amount_claimed = $this->db->select_sum('amtReim')->get_where('claims',array('vnum'=>$voucher->VNumber,'proNo'=>$this->session->center_id))->row()->amtReim; 
											$total_in_voucher = $this->db->select_sum('Cost')->get_where('voucher_body',array('VNumber'=>$voucher->VNumber,'AccNo'=>$this->config->item('beneficary_medical_reimbursement_num_account'),'icpNo'=>$this->session->center_id))->row()->Cost;	
											$balance = $total_in_voucher-$amount_claimed;
											
											if($total_in_voucher>$amount_claimed){
										?>
											<option value="<?=$voucher->VNumber;?>"><?=get_phrase('voucher');?>: <?=$voucher->VNumber;?> - <?=get_phrase('total_amount');?>: <?=number_format($total_in_voucher);?>; <?=get_phrase('claimable_amount');?>: <?=number_format($balance);?></option>
										<?php 
										
											} 
										
											endforeach;
										?>	
									</select>
								</div>
							</div>
							
							<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('total_amount');?></label>
								<div class="col-sm-8">
									<input type="text" required="required" class="form-control reset" name="totAmt" id="totAmt" value="" readonly="readonly" placeholder="<?=get_phrase("choose_a_voucher_number_first");?>"/>
								</div>
							</div>
							
							
					
					</div>
					
					<div class="col-sm-6">
								
							
							<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('caregiver_contribution');?></label>
								<div class="col-sm-8">
									<input type="text" required="required" class="form-control reset" name="careContr" id="careContr" readonly="readonly" value="0"/>
								</div>
							</div>
							
							<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('amount_reimbursable');?></label>
								<div class="col-sm-8">
									<input type="text" required="required" class="form-control reset" name="amtReim" id="amtReim" readonly="readonly" value="0"/>
								</div>
							</div>
							
							
							<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('treatment_date');?></label>
								<div class="col-sm-8">
									<input type="text" required="required" class="datepicker form-control reset" readonly="readonly" name="treatDate" id="treatDate" data-format="yyyy-mm-dd" data-end-date="0d" data-start-date="-60d"/>
								</div>
							</div>
							
							<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('facility_name');?></label>
								<div class="col-sm-8">
									<input type="text" required="required" class="form-control reset" name="facName" id="facName" value=""/>
								</div>
							</div>
							
							<div class="form-group">
								<label class="control-label col-sm-4"><?php echo get_phrase('facility_type');?></label>
								<div class="col-sm-8">
									<select class="form-control reset" required="required" id="facClass" name="facClass">
										<option value=""><?php echo get_phrase('select');?></option>
										<option value="public"><?=get_phrase("public");?></option>
										<option value="missionary"><?=get_phrase("missionary");?></option>
										<option value="private"><?=get_phrase("private");?></option>
									</select>
								</div>
							</div>
							
							<div class="form-group hidden" id="receipt">
						     	<label class="control-label col-sm-4"><?php echo get_phrase('receipt');?></label>
						     	<div class="col-sm-8">
						        	<input type="file" id="upload_receipt" class="form-control reset upload" name="receipt[]" multiple />
						        </div>
						    </div>
						    
						    <div class="form-group hidden" id="approval">
						     	<label class="control-label col-sm-4"><?php echo get_phrase('approval_document');?></label>
						     	<div class="col-sm-8">
						        	<input type="file" id="upload_support_docs" class="form-control reset upload"  name="approval[]" multiple />
						        </div>
						    </div>
					</div>
					
					
					<div class="col-sm-12"><hr/>
							
							<div class="form-group">
								<div class="col-sm-3">
									<button type="submit" disabled="disabled" id="submit" class="btn btn-primary btn-icon submit"><i class="fa fa-plus"></i><?php echo get_phrase('create');?></button>
								</div>
								
								<div class="col-sm-3 hidden">
									<button type="submit" disabled="disabled" id="create_submit" class="btn btn-success btn-icon submit"><i class="fa fa-check"></i><?php echo get_phrase('create_&_submit');?></button>
								</div>
								
								<div class="col-sm-3">
									<button id="reset" class="btn btn-danger btn-icon"><i class="fa fa-minus"></i><?php echo get_phrase('reset');?></button>
								</div>
								
								<div class="col-sm-3">
									<a href="<?=base_url();?>claims.php/partner/medical_claims" class="btn btn-info btn-icon"><i class="fa fa-arrow-left"></i><?php echo get_phrase('back');?></a>
								</div>
								
							</div>				
					</div>
					
					</form>
					
					
					
				</div>
			</div>		
	</div>
</div>
<script>
	if($('.submit').attr('disabled')){
		$('.submit').popover({
		   'placement':'top',
		   'content':'Enter correct amount to enable it'
		}).popover('show');
	}
	
	$("#facClass").change(function(){
		if($(this).val()==="public") {
			
				//$('#progress').html($(this).val() + " : Receipt = "+ $('#receipt').attr('class') + " - Approval = " + $('#approval').attr('class'));
				
				if($('#approval').hasClass('show')) $('#approval').toggleClass('show hidden');
				
				if($('#receipt').hasClass('hidden')) $('#receipt').toggleClass('hidden show');
				
				//$('#progress').append("</br>" + $(this).val() + " : Receipt = "+ $('#receipt').attr('class') + " - Approval = " + $('#approval').attr('class'));
			
			} else {
				
				//$('#progress').html($(this).val() + " : Receipt = "+ $('#receipt').attr('class') + " - Approval = " + $('#approval').attr('class'));
				
				if($('#approval').hasClass('hidden')) $('#approval').toggleClass('hidden show');
				
				if($('#receipt').hasClass('hidden')) $('#receipt').toggleClass('hidden show'); 	
				
				//$('#progress').html("</br>" + $(this).val() + " : Receipt = "+ $('#receipt').attr('class') + " - Approval = " + $('#approval').attr('class'));				
		};
		
	});
	
	function get_incident_category(el){
		
		$('#incident_sub_category_id').find('option').remove();
		
		$('#incident_sub_category_id').removeAttr('readonly');
		
		var url = "<?php echo base_url();?>claims.php/partner/get_incident_sub_category/"+$(el).val();
		$.ajax({
			url:url,
			success:function(data){
				$('#incident_sub_category_id').append(data);
			}
		});
	}
	
	function get_incident_sub_category(el){
		$('#illness_id').find('option').remove();
		
		$('#illness_id').removeAttr('readonly');
		
		if($('#incident_sub_category_id').val()===""){
			alert('Please select an incident category');
			$('#illness_id').attr('readonly','readonly');
		}else{
		
			var url = "<?php echo base_url();?>claims.php/partner/get_incident/"+$('#incident_sub_category_id').val();
			$.ajax({
				url:url,
				success:function(data){
					$('#illness_id').append(data);
				}
			});
			
		}
	}
	
	$('#incidentID').change(function(){
		$(this).prop('type','text');
		var str = "I-";
		var input = $(this).val();
		$(this).val(str+input);
	});
	
	$('#incidentID').focus(function(){
		$(this).prop('type','number');
		$(this).val("");
	});
	
	$("#childNo").change(function(){
		var prefix = $("#proNo").val();
		var childid = $(this).val();
		$('#childName').css('border','1px gray solid');
		if(childid.length===1){
			childid = "-000"+childid;
		}else if(childid.length===2){
			childid = "-00"+childid;
		}else if(childid.length===3){
			childid = "-0"+childid;
		}else if(childid.length===4){
			childid = "-"+childid; 
		}
		$(this).prop('type','text');
		$(this).val(prefix+childid);	
		
		var url = "<?php echo base_url();?>claims.php/claims/get_child_name/"+$("#childNo").val();
		$.ajax({
			url:url,
			beforeSend:function(){
					jQuery('#progress').html('<div style="text-align:center;"><img width="160" height="100" src="<?php echo base_url();?>uploads/preloader2.gif" /></div>');
				},
			success:function(data, textStatus, jqXHR){
				alert(data);
				$("#childName").val($.trim(data));
				jQuery('#progress').html('');
			},
			error:function(error){
				alert("<?php echo get_phrase('error_occurred');?>");
				$("#childName").val("<?php echo get_phrase('name_not_found');?>");
				jQuery('#progress').html('');
			}
			
		});	
	});
	
	$('#childNo').focus(function(){
		$(this).val("");
		$("#childName").val("");
		$(this).prop('type','number');
	});
	
	$("#vnum").change(function(){
		$('#totAmt').val('');
		$(".submit").prop("disabled","disabled");
		if($(this).val().length<6){
			alert("<?php echo get_phrase('incorrect_string_length');?>");
			$(this).val("");
			$(this).focus();
			$('#totAmt').val('');
			$('#totAmt').prop('readonly','readonly');
		}else if(!$.isNumeric($(this).val())){
			alert("<?php echo get_phrase('numeric_only');?>");
			$(this).val("");
			$(this).focus();
			$('#totAmt').val('');
			$('#totAmt').prop('readonly','readonly');
		}else{
			if($('#totAmt').val()==="") $('#totAmt').removeAttr('readonly'); else $('#totAmt').prop('readonly','readonly');
		}
	});
	

	$("#totAmt").change(function(){	
		
		if($(this).val()<1100){
			alert("<?php echo get_phrase('wrong_limit');?>");
			$(this).val("");
			$(this).focus();
		}else if(!$.isNumeric($(this).val())){
			alert("<?php echo get_phrase('numeric_only');?>");
			$(this).val("");
			$(this).focus();
		}else{
			var vnum = $('#vnum').val();
			//alert(vnum);
			var url = "<?=base_url();?>claims.php/partner/get_claimed_amount/"+vnum;
			$.ajax({
				url:url,
				beforeSend:function(){
					$(".submit").prop("disabled","disabled");
					jQuery('#progress').html('<div style="text-align:center;"><img width="160" height="100" src="<?php echo base_url();?>uploads/preloader2.gif" /></div>');
				},
				success:function(data){ //alert(data);
					jQuery('#progress').html('');
					var dif = parseInt(data)-parseInt($("#amtReim").val());
					if(dif < 0){
						alert("You have exceeded the voucher amount by <?=get_phrase('Kes.');?> "+ Math.abs(dif));
						$("#totAmt").val('0');$("#totAmt").css("border","1px red solid");
						$("#amtReim").val('0');$("#totAmt").css("border","1px red solid");
						$(".submit").prop("disabled","disabled");
					}else{
						$("#totAmt").css("border","1px gray solid");
						$("#amtReim").css("border","1px gray solid");
						$(".submit").removeAttr("disabled");
						
						$('.submit').popover({
						   'placement':'top',
						   'content':'<?=get_phrase('enter_correct_amount_to_enable_it');?>'
						}).popover('destroy');
					}
				}
			});
		}
	});
	
	function require_numeric(id){
		if(!$.isNumeric($("#"+id).val())){
			alert("<?php echo get_phrase('numeric_only');?>");
			$("#"+id).val("");
			$("#"+id).focus();
		}
	}
	
	$("#nhif").change(function(){
		require_numeric("nhif");
	});

	$("#nhif,#totAmt").change(function(){
		var nhif = $("#nhif").val();
		var totAmt = $("#totAmt").val();
		var careContr = 0;
		var amtReim = totAmt;
		
		if(nhif.length<2 && totAmt.length>0){
			careContr = 0.1*totAmt;
			amtReim =  totAmt - careContr;
			
		}
		
		$("#careContr").val(careContr);
		$("#amtReim").val(amtReim);
		
	});
	
	$("#frm_medical_claim").on('submit',function(ev){
		
		if($('#childName').val() === 'Name Not Found') {
			
			alert("You can't submit a claim without name"); 
			$('#childName').css('border','1px red solid'); 
			return false;
		}
		
		var url = "<?=base_url();?>claims.php/partner/add_claim";
		var data = new FormData(this);

		$.ajax({
			url:url,
			data:data,
			type:"POST",
			contentType: false,
            cache: false,
            processData:false,
			beforeSend:function(){
				jQuery('#progress').html('<div style="text-align:center;"><img width="160" height="100" src="<?php echo base_url();?>uploads/preloader2.gif" /></div>');
			},
			success:function(response){
							
							//Check if a receipt has been attached
							var available_file_inputs = $("#receipt,#approval").not(".hidden").find('input:file').length;
							
							var approval_attached = 0;
							
							var receipt_attached = $("#receipt").not(".hidden").find('input:file').get(0).files.length;
							
							if($("#approval").not(".hidden").find('input:file').length > 0){
								approval_attached = $("#approval").not(".hidden").find('input:file').get(0).files.length;
							}
														
							var count_of_attachments = parseInt(receipt_attached) + parseInt(approval_attached);
							
							//alert(available_file_inputs);
							//alert(receipt_attached);
							
							var cnf = confirm("Do you want  to submit this claim?");
							
							if(!cnf || (receipt_attached == 0 || (available_file_inputs == 2 && count_of_attachments < 2 ))){
								jQuery('#progress').html('<div class="well" style="text-align:center;color:red">Claim created successfully but not submitted</div>');
								
								$(".reset").each(function(i,elem){
									$(elem).val("");
								});
									
								$("#nhif").val("0");
								
								$('.submit').prop('disabled','disabled');
								
							}else if((available_file_inputs == 1 && receipt_attached == 1 ) || (available_file_inputs == 2 && count_of_attachments == 2 )){
								var url2 = "<?php echo base_url();?>claims.php/partner/single_submit/"+response+"/ajax";
								
								$.ajax({
									url:url2,
									beforeSend:function(){
										jQuery('#progress').html('<div style="text-align:center;"><img width="160" height="100" src="<?php echo base_url();?>uploads/preloader4.gif" /></div>');
									},
									success:function(msg){
										jQuery('#progress').html('<div class="well" style="text-align:center;">'+msg+'</div>')
										
										$(".reset").each(function(i,elem){
											$(elem).val("");
										});
										
										$("#nhif").val("0");
										
										$('.submit').prop('disabled','disabled');
									},
									error:function(){
										jQuery('#progress').html('<div class="well" style="text-align:center;">Error occurred on submit!</div>')
									}
								});
							}
																											
							
			},
			error:function(){
				jQuery('#progress').html('<div class="well" style="text-align:center;">Error occurred on create!</div>')
			}
		});
		
		ev.preventDefault();
	});
	
	
	$("#reset").click(function(ev){
		
		$(".reset").each(function(i,elem){
			$(elem).val("");
		});
		$("#nhif").val("0");
		
		ev.preventDefault();
	});
	
</script>