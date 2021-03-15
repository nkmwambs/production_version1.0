<div class="row">
	<div class="col-md-12">
    	<div class="row">
            <!-- CALENDAR-->
            <div class="col-md-12 col-xs-12">    
                <div class="panel panel-primary " data-collapsed="0">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <i class="entypo-gauge"></i> 
                            <?php echo get_phrase('dashboard');?>
                        </div>
                    </div>
                    <div class="panel-body">

						<a class='btn btn-default' target='__blank' href="<?php echo base_url();?>ifms.php/facilitator/direct_cash_transfers_report/<?=$tym;?>"><?php echo get_phrase('direct_cash_transfers_report');?></a>

						<hr/>
                    	<div class="row">
                    		<div class="col-sm-12">
                    			<div class="btn btn-default pull-right col-sm-3"><a href="#" class="fa fa-backward scroll" id="prev"></a> <?=get_phrase('you_are_in');?> <?=date('F Y',$tym);?> <input type="text" class="form-control col-sm-1" id="cnt" placeholder="<?=get_phrase('enter_number_of_months');?>"/> <a href="#" class="fa fa-forward scroll" id="next"></a></div>
                    		</div>
                    	</div>
                    	
                    	<div class="row">
                    		<div class="col-sm-12">
		                        <table class="table table-striped datatable">
		                        	<thead>
		                        		<tr>
		                        			<th><?=get_phrase('project');?></th>
		                        			<th><?=get_phrase('mfr_submitted');?></th>
		                        			<th><?=get_phrase('report_validated');?></th>
		                        			<th><?=get_phrase('new');?></th>
		                        			<th><?=get_phrase('submitted');?></th>
		                        			<th><?=get_phrase('declined');?></th>
		                        			<th><?=get_phrase('reinstated');?></th>
		                        			<th><?=get_phrase('allow_edit');?></th>
		                        			<th><?=get_phrase('unapproved_budget');?></th>
		                        			<th><?=get_phrase('action');?></th>
		                        		</tr>
		                        	</thead>
		                        	<tbody>
		                        		<?php 
											//$projects = $this->crud_model->project_per_cluster($this->session->cluster);
										?>
		                        	</tbody>
		                        </table>
		                       </div>
		                  </div>  		
                    	
					</div>	
				</div>	
			</div>
		</div>
	</div>
</div>

  <div class="row">  
	<div class="col-md-12">
		<div class="row">
            <div class="col-md-3">
            
                <div class="tile-stats tile-red">
                    <div class="icon"><i class="fa fa-group"></i></div>
                    <div class="num" data-start="0" data-end="<?php echo $this->db->get_where('users',array('online'=>1))->num_rows();?>" 
                    		data-postfix="" data-duration="1500" data-delay="0">0</div>
                    
                    <h3><?php echo get_phrase('logged_users');?></h3>

                </div>
                
            </div>
            
    	</div>
    </div>
 </div>   

<script>
	$(document).ready(function(){
		
		var datatable = $('.table').DataTable({
			stateSave: true
		});
		
		
		$('.scroll').on('click',function(ev){
		
			var cnt = $('#cnt').val();
			
			if(cnt===""){
				cnt = "1";
			}
			
			var dt = '<?php echo $tym;?>';
			
			var flag = $(this).attr('id');
			
			var url = '<?php echo base_url();?>ifms.php/facilitator/dashboard/'+dt+'/'+cnt+'/'+flag;
			
			$(this).attr('href',url);
		});
	});
</script>				