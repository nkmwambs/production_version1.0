<div class="row">
	<div class="col-sm-12">
	<div class="panel panel-primary " data-collapsed="0">
	   <div class="panel-heading">
	    	<div class="panel-title">
		        <i class="fa fa-folder"></i>
		            <?php echo get_phrase('reports');?>
		    </div>
		</div>
		        
		<div class="panel-body"  style="max-width:50; overflow: auto;">
			<?php 
				echo form_open(base_url() . 'admin.php/admin/reports/run/', array('id'=>'frm_report','class' => 'form-horizontal form-groups-bordered validate',"autocomplete"=>"off",'enctype' => 'multipart/form-data'));
						
			?>
						<div class="form-group">
							<label for="" class="col-xs-4 control-label"><?php echo get_phrase('record_type');?></label>
							<div class="col-xs-8">
								<select class="form-control" name="record_type_id" id="record_type">
									<option>Select</option>
									<?php 
										foreach($record_types as $row):
									?>
										<option value="<?=$row->relationships_id;?>"><?=$row->label;?></option>
									<?php 
										endforeach;
									?>
								</select>
							</div>
						</div>
						
						<div class="form-group">
							<label for="" class="col-xs-4 control-label"><?php echo get_phrase('filters');?></label>
							<div class="col-xs-8">
								<!-- <div id="filters">
									
								</div> -->
								<div class="cloned-row">
									<div class="form-group">
								
										<div class="col-sm-3">
											<select class="form-control fields clr" name="field[]">
												<option value="">Select Field</option>
												
											</select>
										</div>
										
										<div class="col-sm-3">
											<select class="form-control" class="operand" name="operand[]">
												<option value=" ">Select Operand</option>
												<option value="=" selected="selected">Equals to</option>
												<option value="<>">Not Equal to</option>
												<option value=">">Greater than</option>
												<option value="<">Less than</option>
												<option value="<=">Less than or Equal to</option>
												<option value=">=">Greater than or Equal to</option>
												<option value="LIKE">Contains</option>
												
											</select>
										</div>
										
										
										<div class="col-sm-3">
											<input type="text" class="form-control clr" name="value[]"/> 
										</div>
										
										<div class="col-sm-3">
											<div class="btn btn-success clone_filters"><i class="fa fa-plus-square"></i></div> 
											<div class="btn btn-danger remove_filters"><i class="fa fa-minus-square"></i></div>	
										</div>
										</div>
									</div>
							</div>
						</div>
						
						<div class="form-group">
							<label for="" class="col-xs-4 control-label"></label>
							<div class="col-xs-8">
								<button id="btn_run" class="btn btn-info">Run</button>
							</div>
						</div>
						
						<div class="form-group"><div id="progress"></div></div>
						
						<div class="form-group">
							<div class="col-sm-12">
								<textarea id="sql_area" class="form-control clr" cols="6" rows="5" readonly="readonly"></textarea>
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-sm-12">
								<div class="clr"  id="query_result"></div>
							</div>
						</div>
			</form>
		</div>
				
	</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('#btn_run').click(function(ev){
			$('#sql_area').html("");
			
				var data = $('#frm_report').serializeArray();//{'record_type_id':$('#record_type').val()}; 
				var url = "<?=base_url();?>reports.php/admin/developsql";
				$.ajax({
					url:url,
					data:data,
					type:"POST",
					beforeSend:function(){
						$('#query_result').html('<div style="text-align:center;"><img width="160" height="100" src="<?php echo base_url();?>uploads/preloader2.gif" /></div>');
					},
					success:function(response){
						var obj = JSON.parse(response);
						$('#sql_area').html(obj.sql.trim());
						$('#query_result').html(obj.result);
						//$('#sql_area').html(response);
					},
					error:function(jqXHR, textStatus, errorThrown){
						$('#query_result').html("<div class='well' style='text-align:center;'>"+jqXHR.status+' Error Occurred: '+errorThrown+"</div>");
					}
				});
			
			
			ev.preventDefault();
		});
		
		
		$('#record_type').change(function(ev){
			$('.fields').html("");
			$('.clr').html("");
			
			$('#progress').show();
			var data ={'record_type_id':$('#record_type').val()}; 
			var url = "<?=base_url();?>reports.php/admin/populate_fields";
			$.ajax({
				url:url,
				data:data,
				type:"POST",
				beforeSend:function(){
						$('#progress').html('<div style="text-align:center;"><img width="160" height="100" src="<?php echo base_url();?>uploads/preloader2.gif" /></div>');
				},
				success:function(response){
					$('#progress').hide();
					$('.fields').html(response);
					//$('#progress').html(response);
				}
			});
			
			ev.preventDefault();
		});		
		
		
		$(".clone_filters").click(function(ev){
			
			$(".cloned-row:first").clone(true).insertAfter(".cloned-row:last");
			
			ev.preventDefault();
				
		});
		
		$(".remove_filters").click(function(ev){
			
			if($('.cloned-row > .form-group').length>1){
				$(this).closest('.form-group').remove();
				//alert($('.cloned-row > .form-group').length);
			}
			
			ev.preventDefault();
		});
	});
</script>
