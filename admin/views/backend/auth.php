<!DOCTYPE html>
<html lang="en">
<head>
	    <?php
	        $system_name = $this->db->get_where('settings', array('type' => 'system_name'))->row()->description;
	        $system_title = $this->db->get_where('settings', array('type' => 'system_title'))->row()->description;
        ?>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="description" content="Neon Admin Panel" />
	<meta name="author" content="" />

	<link rel="icon" href="assets/images/favicon.ico">

	<title><?= $system_name?> | Authentication screen</title>

	<link rel="stylesheet" href="<?php echo base_url();?>assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css">
	<!-- <link rel="stylesheet" href="<?php echo base_url();?>assets/css/font-icons/entypo/css/entypo.css"> -->
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/bootstrap.css">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/neon-core.css">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/neon-theme.css">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/neon-forms.css">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/custom.css">

	<script src="<?php echo base_url();?>assets/js/jquery-1.11.0.min.js"></script>

	<!--[if lt IE 9]><script src="assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
	
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->


</head>
<body class="page-body login-page is-lockscreen login-form-fall" data-url="http://neon.dev">
	
<?php
//$secret = $this->db->get_where('users',array('username'=>$this->session->name))->row()->secret_code;
//print($secret);
?>	
	
        <!-- This is needed when you send requests via Ajax -->
        <script type="text/javascript">
            var baseurl = '<?php echo base_url(); ?>';
        </script>	

<div class="login-container">
	
	<div class="login-header">
		
		<div class="login-content">
			
			<a href="#" class="logo">
				 <img src="<?php echo base_url('uploads/logo.png');?>"  style="max-height:60px;"/>
			</a>
			
			<p class="description">Dear <?= $this->session->firstname;?>, enter your token to complete log in</p>
			
			<!-- progress bar indicator -->
			<div class="login-progressbar-indicator">
				<h3>0%</h3>
				<span>logging in...</span>
			</div>
		</div>
		
	</div>
	
	<div class="login-form">
		
		<div class="login-content">
			
			        <div class="form-login-error" id="log_error">
                        <h3>Invalid Token</h3>
                        <p>Please enter the correct token!</p>
                    </div>
			
			<?php echo form_open(base_url() . 'login/verify_token' , array('id'=>'form_token','class' => 'form-wizard validate'));?>
				
				<div class="form-group lockscreen-input">
					
					<div class="lockscreen-thumb">
						<img src="<?php echo $this->crud_model->get_image_url('icp',$this->session->login_user_id);?>" class="img-circle" width="120"/>
						
						<div class="lockscreen-progress-indicator">0%</div>
					</div>
					
					<div class="lockscreen-details">
						<h4><?= $this->session->firstname;?></h4>
						<span data-login-text="logging in..."></span>
					</div>
					
				</div>
				
				<div class="form-group">
					
					<div class="input-group">
						<div class="input-group-addon">
							<i class="entypo-key"></i>
						</div>
						
						<input type="text" class="form-control" name="token" id="token" placeholder="Token" autocomplete="off" />
					</div>
				
				</div>
				
				<div class="form-group">
					<button type="submit" class="btn btn-primary btn-block btn-login">
						<i class="entypo-login"></i>
						Verify
					</button>
				</div>
				
			</form>
			
			<div class="form-group">
					<a href="<?php echo base_url(); ?>login/logout"  class="btn btn-default btn-block btn-login">
						<i class="entypo-logout"></i>
						Login Out
					</a>
				</div>
			
			
			<div class="login-bottom-links">
				<a href="<?php echo base_url();?>login/register_token" class="link">
					<?php echo get_phrase('register_token');?>
				</a>
			</div>
			
		</div>
		
	</div>
	
</div>


	<!-- Bottom scripts (common) -->
	<script src="<?php echo base_url();?>assets/js/gsap/TweenMax.min.js"></script>
	<script src="<?php echo base_url();?>assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js"></script>
	<script src="<?php echo base_url();?>assets/js/bootstrap.js"></script>
	<script src="<?php echo base_url();?>assets/js/joinable.js"></script>
	<script src="<?php echo base_url();?>assets/js/resizeable.js"></script>
	<script src="<?php echo base_url();?>assets/js/neon-api.js"></script>
	<script src="<?php echo base_url();?>assets/js/jquery.validate.min.js"></script>
	<script src="<?php echo base_url();?>assets/js/neon-login.js"></script>


	<!-- JavaScripts initializations and stuff -->
	<script src="assets/js/neon-custom.js"></script>


	<!-- Demo Settings -->
	<script src="assets/js/neon-demo.js"></script>
	
        <script>
			$('#form_token').submit(function(e){
			
						var postData = $(this).serializeArray();
		    			var formURL = $(this).attr("action");
						    $.ajax(
						    {
						        url : formURL,
						        type: "POST",
						        data : postData,
						       	success:function(response) 
		        				{
									if(parseInt(response)===1)
									{
										
										window.location.href = baseurl+'login';
										
									}else{
										//window.location.href = baseurl+'login/auth'
										
										//$(".lockscreen-input").html();
										$(".lockscreen-input").css("display", "none");
										$(".form-login-error").css("display", "block");
										return false;
									}
						            	
						            
						        },
						        error: function(jqXHR, textStatus, errorThrown) 
						        {
						            //if fails
						            alert(textStatus);      
						        }
		
						    });
						    e.preventDefault(); //STOP default action
						    e.unbind(); //unbind. to stop multiple form submit.		
			});
		</script>

</body>
</html>