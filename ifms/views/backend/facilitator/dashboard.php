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
		                        			<th><?=get_phrase('bank_reconciled');?></th>
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
		                        			$projects = $this->crud_model->project_per_cluster($this->session->cluster);
											
											foreach($projects as $row):
												
												$bank_reconcile_check = $this->finance_model->bank_reconciled($row->fname,date('Y-m-t',$tym));
											
										
		                        		?>
		                        			<tr>
		                        				<td><?=$row->fname;?></td>
		                        				<td>
		                        					<?php
		                        						
		                        						if(number_format($bank_reconcile_check)!== "0"){
		                        					?>
		                        							<span class="label label-danger"><?=number_format($bank_reconcile_check);?></span>
		                        					<?php
		                        						}else{
		                        					?>
		                        							<span class="label label-success"><?=number_format($bank_reconcile_check);?></span>
		                        					<?php		
		                        						}
		                        					
		                        					?>
		                        					
		                        				</td>
		                        				<td><?php
		                        					
		                        					if($this->db->get_where("opfundsbalheader",array('icpNo'=>$row->fname,"closureDate"=>date('Y-m-t',$tym)))->num_rows()===0){
		                        					?>
		                        						<button class="btn btn btn-info btn-icon disabled"><i class="fa fa-eye"></i><?= get_phrase('report_not_available');?></button>
		                        					<?php
		                        					}else{
		                        					
			                        					$validate = $this->db->get_where("opfundsbalheader",array('icpNo'=>$row->fname,"closureDate"=>date('Y-m-t',$tym)))->row()->allowEdit;
			                        					if($validate==='0'){
			                        					?>
			                    
			                    	    					<button class="btn btn btn-success btn-icon" onclick="confirm_action('<?=base_url();?>ifms.php/facilitator/validate_mfr/<?=$row->fname;?>/<?=date('Y-m-t',$tym);?>/<?=$validate;?>');"><i class="fa fa-check"></i><?= get_phrase('validated');?></button>
			                        					<?php
			                        					}else{
			                        					?>
			                        						<button class="btn btn btn-danger btn-icon"  onclick="confirm_action('<?=base_url();?>ifms.php/facilitator/validate_mfr/<?=$row->fname;?>/<?=date('Y-m-t',$tym);?>/<?=$validate;?>');"><i class="fa fa-times"></i><?= get_phrase('unvalidated');?></button>
			                        						
			                        					<?php
			                        					}
													}
		                        					?>
		                        				</td>
		                        				<td><?=number_format($this->finance_model->unapproved_budget_items($row->fname,date('Y-m-d',$tym),0),2);?></td>
		                        				<td><?=number_format($this->finance_model->unapproved_budget_items($row->fname,date('Y-m-d',$tym),1),2);?></td>
		                        				<td><?=number_format($this->finance_model->unapproved_budget_items($row->fname,date('Y-m-d',$tym),3),2);?></td>
		                        				<td><?=number_format($this->finance_model->unapproved_budget_items($row->fname,date('Y-m-d',$tym),4),2);?></td>
		                        				<td><?=number_format($this->finance_model->unapproved_budget_items($row->fname,date('Y-m-d',$tym),5),2);?></td>
		                        				<td>
		                        					<?php
		                        						$color = 'label-success';
		                        						if($this->finance_model->unapproved_budget_items($row->fname,date('Y-m-d',$tym))>0){
		                        							$color = 'label-danger';			
		                        						}
		                        					?>
		                        					<div class="label <?=$color;?>">
		                        						<?php
		                        							echo number_format($this->finance_model->unapproved_budget_items($row->fname,date('Y-m-d',$tym)),2);
		                        						?>		                        					
		                        					</div>
		                        				</td>	
		                        				<td>
		                        					<div class="btn-group">
						                    			<button id="" type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
						                        			<?php echo get_phrase('action');?> <span class="caret"></span>
						                    			</button>
						                    				<ul class="dropdown-menu dropdown-default pull-right" role="menu">
									                        	
																<li>
																	<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_outstanding_cheques/<?php echo date('Y-m-t',$tym);?>/<?=$row->fname;?>')"><?php echo get_phrase('outstanding_cheques');?></a>
																</li>
																
																<li class="divider"></li>
																
																<li>
																	<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_transit_deposits/<?php echo date('Y-m-t',$tym);?>/<?=$row->fname;?>')"><?php echo get_phrase('deposits_in_transit');?></a>
																</li>
															
																
																<li class="divider"></li>
																
																<li>
																	<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_bank_reconcile/<?php echo date('Y-m-t',$tym);?>/<?=$row->fname;?>')"><?php echo get_phrase('bank_reconciliation');?></a>
																</li>
																
																<li class="divider"></li>
																
																<li>
																	<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_variance_explanation/<?php echo date('Y-m-t',$tym);?>/<?=$row->fname;?>')"><?php echo get_phrase('variance_explanation');?></a>
																</li>
																
																<li class="divider"></li>
																
																<li>
																	<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_proof_of_cash/<?php echo date('Y-m-t',$tym);?>/<?=$row->fname;?>')"><?php echo get_phrase('proof_of_cash');?></a>
																	
																</li>
																
																<li class="divider"></li>
																
																<li>
																	<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_fund_balances/<?php echo date('Y-m-t',$tym);?>/<?=$row->fname;?>')"><?php echo get_phrase('fund_balance_report');?></a>
																	
																</li>
																
																<li class="divider"></li>
																
																<li>
																	<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_expense_report/<?php echo date('Y-m-t',$tym);?>/<?=$row->fname;?>')"><?php echo get_phrase('expense_report');?></a>
																	
																</li>
																
																<li class="divider"></li>
																
																<li>
																	<a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/modal_fund_ratios/<?php echo date('Y-m-t',$tym);?>/<?=$row->fname;?>')"><?php echo get_phrase('financial_ratios');?></a>
																	
																</li>
																
																								
																<li class="divider"></li>
																
																<li>
																	<a target='__blank' href="<?php echo base_url();?>ifms.php/facilitator/cash_journal/<?php echo $tym;?>/<?=$row->fname;?>"><?php echo get_phrase('cash_journal');?></a>
																</li>
																
																<li class="divider"></li>
																
																<li>
																	<a target='__blank' href="<?php echo base_url();?>ifms.php/facilitator/bank_statements/<?php echo $tym;?>/<?=$row->fname;?>"><?php echo get_phrase('bank_statements');?></a>
																</li>
	                        									
	                        									<li class="divider"></li>
																
																<li>
																	<a href="#" onclick="confirm_dialog('<?php echo base_url();?>ifms.php/facilitator/decline_mfr/<?=$tym;?>/<?=$row->fname;?>');"><?php echo get_phrase('decline_financial_report');?></a>
																</li>
																
																<li class="divider"></li>
																
																<li>
																	<a target='__blank' href="<?php echo base_url();?>ifms.php/facilitator/plans/<?=$tym;?>/<?=$row->fname;?>"><?php echo get_phrase('budget');?></a>
																</li>
																
																<li class="divider"></li>
						                     		</ul>
						                  	 </div>
		                        				</td>
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