	
	<link rel="stylesheet" href="<?php echo base_url();?>assets/js/select2/select2-bootstrap.css">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/js/select2/select2.css">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/js/selectboxit/jquery.selectBoxIt.css">

   	<!-- Bottom Scripts -->
	<script src="<?php echo base_url();?>assets/js/gsap/main-gsap.js"></script>
	<script src="<?php echo base_url();?>assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js"></script>
	<script src="<?php echo base_url();?>assets/js/joinable.js"></script>
	<script src="<?php echo base_url();?>assets/js/resizeable.js"></script>
	<script src="<?php echo base_url();?>assets/js/neon-api.js"></script>
	<script src="<?php echo base_url();?>assets/js/toastr.js"></script>
    <script src="<?php echo base_url();?>assets/js/jquery.validate.min.js"></script>
	<script src="<?php echo base_url();?>assets/js/fullcalendar/fullcalendar.min.js"></script>
    <script src="<?php echo base_url();?>assets/js/bootstrap-datepicker.js"></script>
    <script src="<?php echo base_url();?>assets/js/fileinput.js"></script>
   
    <script src="<?php echo base_url();?>assets/js/select2/select2.min.js"></script>
    <script src="<?php echo base_url();?>assets/js/selectboxit/jquery.selectBoxIt.min.js"></script>
    
   
    
	<script src="<?php echo base_url();?>assets/js/neon-calendar.js"></script>
	<script src="<?php echo base_url();?>assets/js/neon-chat.js"></script>
	<script src="<?php echo base_url();?>assets/js/neon-custom.js"></script>
	<script src="<?php echo base_url();?>assets/js/neon-demo.js"></script>
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/locale/en-gb.js"></script>
	

	
	<!--Font Awesome-->
	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
	
	<!-- Toggle Button -->
	
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
	
		<!-- Monkey Modal Dialog  CSS / JS-->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.35.2/css/bootstrap-dialog.min.css">
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.35.2/js/bootstrap-dialog.min.js"></script>
	
	
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css" rel="stylesheet">
	<!--<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">-->


		<!--Dropzone-->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.css">
	
<!-- Include Date Range Picker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />	

<!--Table Header Fixer-->
<script src="<?php echo base_url();?>assets/js/tableHeadFixer.js"></script>


<!--Morris Charts-->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>


<!-- SHOW TOASTR NOTIFIVATION -->
<?php if ($this->session->flashdata('flash_message') != ""):?>

<script type="text/javascript">
	toastr.success('<?php echo $this->session->flashdata("flash_message");?>');
</script>

<?php endif;?>


<!-----  DATA TABLE EXPORT CONFIGURATIONS ---->                      
<script type="text/javascript">

	jQuery(document).ready(function($)
	{
		

		//var datatable = $("#table_export").dataTable();
		
		//$(".dataTables_wrapper select").select2({
			//minimumResultsForSearch: -1
		//});
		
		$('.modal-dialog').draggable();
		
		$('.modal-content').resizable({
		    //alsoResize: ".modal-dialog",
		    minHeight: 300,
		    minWidth: 300
		});
		
	});
		
</script>