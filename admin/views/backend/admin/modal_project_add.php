<div class="row">
		<div class="col-sm-12">
			<div class="panel panel-primary " data-collapsed="0">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <i class="entypo-plus-squared"></i>
                            <?php echo get_phrase('new_user');?>
                        </div>
                    </div>
                    <div class="panel-body">
                    <?php 
						echo form_open(base_url() . 'admin.php/admin/manage_profile/add_project/', array('id'=>'frm_user_add','class' => 'form-horizontal form-groups-bordered validate',"autocomplete"=>"off",'enctype' => 'multipart/form-data'));
						
						
					?>
						<div class="form-group">
							<label for="" class="col-xs-4 control-label"><?php echo get_phrase('project');?></label>
							<div class="col-xs-8">
								<input type="text" class="form-control" id="icpName" name="icpName" />
							</div>
						</div>
						
						<div class="form-group">
							<label for="" class="col-xs-4 control-label"><?php echo get_phrase('project_number');?></label>
							<div class="col-xs-8">
								<input type="text" class="form-control" maxlength='5' placeholder='FCP Number format: KE333 (Without leading Zero)' id="icpNo" name="icpNo" />
							</div>
						</div>
						
						<div class="form-group">
							<label for="" class="col-xs-4 control-label"><?php echo get_phrase('cluster');?></label>
							<div class="col-xs-8">
								<select class="form-control" id="cluster_id" name="cluster_id">
									<option value=""><?=get_phrase('select');?></option>
									<?php
										$clusters = $this->db->order_by('clusterName')->get('clusters')->result_object();
										
										foreach($clusters as $row):
									?>	
										<option value="<?=$row->clusters_id;?>"><?=$row->clusterName;?> </option>
									<?php
										endforeach;
									?>
								</select>
							</div>
						</div>
						
						<div class="form-group">
							<label for="" class="col-xs-4 control-label"><?php echo get_phrase('email');?></label>
							<div class="col-xs-8">
								<input type="email" class="form-control" id="email" name="email" />
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-xs-4 control-label"><?php echo get_phrase('project_start_date');?></label>
							<div class="col-xs-8">
								<input type="text" class="form-control datepicker" data-format='yyyy-mm-dd' readonly='readonly' id="system_start_date" name="system_start_date" />
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-xs-12">
								<button class="btn btn-primary btn-icon"><i class="fa fa-save"></i><?=get_phrase('create');?></button>
							</div>
						</div>	
						
					</form>
					</div>
				</div>
			</div>
		</div>			
		
<script>
	

		
</script>