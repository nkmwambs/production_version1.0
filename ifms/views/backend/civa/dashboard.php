 <?php

// Open CIVs
 $this->db->select(array('civa.civaID as civaID', 'civa.accID as accID','civa.AccNoCIVA as AccNoCIVA','civa.AccTextCIVA as AccTextCIVA','civa.allocate as allocate','civa.closureDate as closureDate'));
 //$this->db->select(array('voucher_item_type_name'));
 $this->db->join('accounts','accounts.accID=civa.accID', 'LEFT');
 
 //$this->db->join('voucher_items_with_civa', 'voucher_items_with_civa.fk_civa_id=civa.civaID','LEFT');
 //$this->db->join('voucher_item_type','voucher_item_type.voucher_item_type_id=voucher_items_with_civa.fk_voucher_item_type_id','LEFT');
 $this->db->where(array('accounts.AccGrp'=>1, 'civa.open'=>1));
 $this->db->order_by('civa.closureDate');
 $open_civs=$this->db->get('civa')->result_object();

 //print_r($open_civs);

 
 //Closed CIVs
 
  $closed_civs = $this->db->get_where("civa",array('open'=>'0'))->result_object();
 
 ?>
 
							<div class="row">
							     <div class="col-sm-12">
							     	<div class="row">
							            <div class="col-md-3">
							     			<div class="tile-stats tile-red">
							     				<div class="icon"><i class="fa fa-book"></i></div>
							                    <div class="num" data-start="0" data-end="<?=$this->db->get_where("civa",array('open'=>'1'))->num_rows();?>" 
							                    		data-postfix="" data-duration="1500" data-delay="0">0</div>
							                    
							                    <h3><?=get_phrase('open_intervention');?></h3>
							                   
							        		</div>
							        	</div>
							        	
							        	 <div class="col-md-3">
							     			<div class="tile-stats tile-red">
							     				<div class="icon"><i class="fa fa-book"></i></div>
							                    <div class="num" data-start="0" data-end="<?=$this->db->get_where("civa",array('open'=>'0'))->num_rows();;?>" 
							                    		data-postfix="" data-duration="1500" data-delay="0">0</div>
							                    
							                    <h3><?=get_phrase('overdue_interventions');?></h3>
							                   
							        		</div>
							        	</div>
							        	
							        </div>
							     </div>
							</div> 

<hr />

<div class="row">
	<div class="col-sm-12">
		<button onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_new_intervention/<?=$page_name?>');" class="btn btn-primary"><?=get_phrase('new_account');?></button>
		<a href="<?=base_url();?>ifms.php/civa/closed_interventions"  class="btn btn-info"><?=get_phrase('closed_accounts');?></a>
		<a href="<?=base_url();?>ifms.php/civa/civ_report" class="btn btn-success"><?=get_phrase('civ_report');?></a>
		<button class="btn btn-danger"><?=get_phrase('close_overdue');?></button>
	</div>
</div>

<hr />	

<div class="row">
								<div class="col-md-12">
							    	<div class="row">
							            <!-- CALENDAR-->
							            <div class="col-md-12 col-xs-12">    
							                <div class="panel panel-primary " data-collapsed="0">
							                    <div class="panel-heading">
							                        <div class="panel-title">
							                            <i class="entypo-gauge"></i> 
							                            <?php echo get_phrase('open_interventions');?>
							                        </div>
							                    </div>
							                    <div class="panel-body" style="padding:0px;overflow: auto;">
							                    	
							                    	<table class="table table striped datatable">
													      
							                    		<thead>
														  
							                    			<tr>
							                    				<th><?=get_phrase('action');?></th>
							                    				<th><?=get_phrase('intervention_code');?></th>
							                    				<th><?=get_phrase('projects_allocated');?></th>
																<?php 
																 if($this->config->item('use_dct_detail_row')){?>
																	<th><?=get_phrase('recipient');?></th> 
																	<th><?=get_phrase('support_mode');?></th> 
																<?php } ?>
							                    				<th><?=get_phrase('closure_date');?></th>
							                    			</tr>
							                    		</thead>
							                    		<tbody>
							                    			<?php
							                    				foreach($open_civs as $row):
							                    				
																$color = "btn-default";
																
																if(strtotime($row->closureDate)<strtotime(date('Y-m-d'))){  
																	$color = "btn-danger";
																}
							                    			?>
							                    				<tr>
							                    					<td class="col-sm-1">
							                    						<div class="btn-group">
													                    	<button id="" type="button" class="btn <?=$color;?> btn-sm dropdown-toggle" data-toggle="dropdown">
													                        	<?php echo get_phrase('action');?> <span class="caret"></span>
													                    	</button>
													                    		<ul class="dropdown-menu dropdown-default pull-left" role="menu">
													                      
																                    <li>
																                        <a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_edit_intervention/<?php echo $row->civaID;?>');">
																                           	<i class="fa fa-pencil"></i>
																								<?php echo get_phrase('edit');?>
																                        </a>
																                    </li>
																							
																					<li  style="" class="divider"></li>
																					
																					<li>
																                        <a href="#" onclick="confirm_action('<?php echo base_url();?>ifms.php/civa/interventions/close/dashboard/<?php echo $row->AccNoCIVA;?>');">
																                           	<i class="fa fa-times"></i>
																								<?php echo get_phrase('close');?>
																                        </a>
																                    </li>
																							
																					<li  style="" class="divider"></li>
																			</ul>
																		</div>			
							                    					</td>
							                    					<td class="col-sm-1"><?=$row->AccNoCIVA;?></td>
							                    					<td class="col-sm-8"><?=$row->allocate;?></td>

																	<?php 
																 if($this->config->item('use_dct_detail_row')){?>

																     <td class="col-sm-8"><?=''?></td>
																	 <td class="col-sm-8"><?=''?></td>

																 <?php } ?>

							                    					<td class="col-sm-2"><?=$row->closureDate;?></td>
							                    				</tr>
							                    			<?php
							                    				endforeach;
							                    			?>
							                    		</tbody>
							                    	</table>
							                    	
							                    	
							                    </div>
							                </div>
							            </div>
							        </div>
							    </div>							
 


    <script>
	$(document).ready(function(){
		var datatable = $('.table').DataTable({
			stateSave: true,
			"bSort" : false ,
			lengthMenu: [[25,50, 100, 150,-1], [25,50 ,100,150 ,"All"]],
			pageLength: 25,
			dom: '<"row"l><Bf><"col-sm-12"rt><ip>',
		       //sDom:'r',
		    pagingType: "full_numbers",
		    buttons: [
		           'csv', 'excel', 'print'
		       ]
		});
		
		
		    if (location.hash) {
			        $("a[href='" + location.hash + "']").tab("show");
			    }
			    $(document.body).on("click", "a[data-toggle]", function(event) {
			        location.hash = this.getAttribute("href");
			    });
			});
			
			$(window).on("popstate", function() {
			    var anchor = location.hash || $("a[data-toggle='tab']").first().attr("href");
			    $("a[href='" + anchor + "']").tab("show");
		
	});
	

  </script>

  
