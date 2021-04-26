<?php 
foreach($css_files as $file): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
 
<?php endforeach; ?>
<?php foreach($js_files as $file): ?>
 
    <script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>

<div class="row">
	<div class="col-sm-12">
		<?php 
			echo form_open(base_url() . 'exams.php/'.$this->session->login_type.'/kcpe/', array('id'=>'frm_user_add','class' => 'form-horizontal form-groups-bordered validate',"autocomplete"=>"off",'enctype' => 'multipart/form-data'));
		?>
						
			<div id="_acYr" class="form-group">
				<label for="" class="col-xs-4 control-label"><?php echo get_phrase('academic_year');?></label>
					<div class="col-xs-6">
						<select class="form-control" name="acYr" id="acYr">
							<option><?=get_phrase('select');?></option>
							<?php
								$yrs = $this->db->select('DISTINCT(acYr)')->order_by('acYr')->get_where('kcpe')->result_object();
								
								foreach($yrs as $row):
									if($row->acYr > 0){
							?>
								<option value="<?=$row->acYr;?>"><?=$row->acYr;?></option>
								
							<?php
									}
								endforeach;
							?>
								<option value="<?=date('Y')?>"><?=date('Y')?></option>
						</select>
					</div>
					<div class="col-xs-2">
						<button class="btn btn-primary"><?=get_phrase('search');?></button>
					</div>	
			</div>
		</form>	
						
	</div>
</div>
<hr />
<div class="row">
	<div class="col-sm-12">
		<?php echo $output; ?>
	</div>
</div>

<script>
	$('#field-cstName').change(function(){
		var cname  = $("#field-cstName option:selected").html();
		
		var url = '<?=base_url();?>exams.php/admin/get_projects/'+cname;
		
		$.ajax({
			url:url,
			success:function(selectValues){
				
				var selectValues = JSON.parse(result);
				
				var output = [];

				$.each(selectValues, function(key, value)
				{
				  output.push('<option value="'+ key +'">'+ value +'</option>');
				});
				
				$('#field-pNo').html(output.join(''));
			}
		});
	});
</script>
 

 