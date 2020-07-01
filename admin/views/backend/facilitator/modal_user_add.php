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
						echo form_open(base_url() . 'admin.php/facilitator/manage_profile/add_user/', array('id'=>'frm_user_add','class' => 'form-horizontal form-groups-bordered validate',"autocomplete"=>"off",'enctype' => 'multipart/form-data'));
						
						
					?>
						
						<div id="_userfirstname" class="form-group">
							<label for="" class="col-xs-4 control-label"><?php echo get_phrase('first_name');?></label>
							<div class="col-xs-8"><INPUT type="text" name="userfirstname" id="userfirstname" class="form-control" required="required"/></div>
						</div>
						
						<div id="_userlastname" class="form-group">
							<label for="" class="col-xs-4 control-label"><?php echo get_phrase('last_name');?></label>
							<div class="col-xs-8"><INPUT type="text" name="userlastname" id="userlastname" class="form-control" required="required"/></div>
						</div>
						
						<div id="_username" class="form-group">
							<label for="" class="col-xs-4 control-label"><?php echo get_phrase('username');?></label>
							<div class="col-xs-8"><INPUT type="text" name="username" id="username" class="form-control" required="required"/></div>
						</div>
						
						
						<div id="_email" class="form-group">
							<label for="" class="col-xs-4 control-label"><?php echo get_phrase('email');?></label>
							<div class="col-xs-8"><INPUT type="text" name="email" id="email" class="form-control" required="required"/></div>
						</div>
						
						<div id="_department" class="form-group">
							<label for="" class="col-xs-4 control-label"><?php echo get_phrase('designation');?></label>
							<div class="col-xs-8">
								<select class="form-control" id="department" name="department" required="required">
									<option value=""><?php echo get_phrase('select');?></option>
									<option value="15"><?=get_phrase("project_director");?></option>
									<option value="16"><?=get_phrase("project_accountant");?></option>
									<option value="17"><?=get_phrase("project_social_worker");?></option>
									<option value="18"><?=get_phrase("project_health_worker");?></option>
									<option value="19"><?=get_phrase("child_survival_implementer");?></option>
									<option value="20"><?=get_phrase("patron");?></option>
									<option value="21"><?=get_phrase("cpc_member");?></option>
								</select>
							</div>
						</div>

						<div id="_supervisorygroup" class="form-group">
							<label for="" class="col-xs-4 control-label"><?php echo get_phrase('cluster');?></label>
							<div class="col-xs-8"><INPUT type="text" name="cname" readonly="readonly" id="cname" class="form-control" value="<?=$this->session->cluster;?>" required="required"/></div>
						</div>
						
						<div id="_usergroup" class="form-group">
							<label for="" class="col-xs-4 control-label"><?php echo get_phrase('project');?></label>
							<div class="col-xs-8">
								<!--<INPUT type="text" name="fname" id="fname" class="form-control"/>-->
								<select required="required" class="form-control" name="fname" id="fname">
									<option value=""><?php echo get_phrase('select');?></option>
									
									<?php
										$projects = $this->crud_model->project_per_cluster($this->session->cluster);
										foreach($projects as $row):
									?>
											<option value="<?=$row->fname;?>"><?=$row->fname;?> (<?=$row->lname;?>)</option>
									<?php
										endforeach;
									?>
								</select>
							</div>
						</div>
						
						<div id="_password" class="form-group">
                                <label class="col-xs-4 control-label"><?php echo get_phrase('password');?></label>
                                <div class="col-xs-8">
                                    <input type="password" class="form-control" name="password" id="password" required="required"/>
                                </div>
                        </div>
                        
                        <div id="_result" class="form-group">
                                <label class="col-xs-4 control-label"><?php echo get_phrase('password_strength');?></label>
                                <div class="col-xs-8">
                                    <!--<input type="password" class="form-control" name="password" id="password"/>-->
                                    <div id="result"></div>
                                </div>
                        </div>
                        
                        
						
						<div class="col-offset-4 col-xs-4 col-offset-4">
							<button id="btn_user_add" type="submit" class="btn btn-primary btn-icon"><i class="entypo-plus"></i><?php echo get_phrase('add');?></button>
						</div>
						
					</form>
					</div>
				</div>
			</div>
		</div>			
		
<script>

$('#password').keyup(function()
	{
		$('#result').html(checkStrength($('#password').val()));
	});	
		
	function checkStrength(password)
	{
		var strength = 0
		
		if (password.length < 6) { 
			$('#result').removeClass()
			$('#result').addClass('short')
			return 'Too short' 
		}
		
		if (password.length > 7) strength += 1
		
		//If password contains both lower and uppercase characters, increase strength value.
		if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))  strength += 1
		
		//If it has numbers and characters, increase strength value.
		if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/))  strength += 1 
		
		//If it has one special character, increase strength value.
		if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/))  strength += 1
		
		//if it has two special characters, increase strength value.
		if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1
		
		
		//Calculated strength value, we can return messages
		
		
		
		//If value is less than 2
		
		if (strength < 2 )
		{
			$('#result').removeClass()
			$('#result').addClass('weak')
			return 'Weak'			
		}
		else if (strength == 2 )
		{
			$('#result').removeClass()
			$('#result').addClass('good')
			return 'Good'		
		}
		else
		{
			$('#result').removeClass()
			$('#result').addClass('strong')
			return 'Strong'
		}
	}
		
</script>