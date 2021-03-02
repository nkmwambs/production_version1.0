<div class="row">
	<div class="col-sm-12">
		
			<div class="panel panel-info">
								
				<div class="panel-heading">
					<div class="panel-title"><?=get_phrase('claim_for_');?> <?=$claim->childName;?> (<?=$claim->incidentID;?>)</div>						
				</div>
										
				<div class="panel-body">
					
				    <div class="col-sm-12">
				        <?php echo form_open(base_url() . 'claims.php/partner/upload_medical_receipts/claims/' , array('id'=>'','class' => 'form-horizontal form-groups-bordered validate', 'enctype' => 'multipart/form-data'));?>
				            <div class="form-group">
				                <label class="control-label"><?=get_phrase("claim_ID");?></label>
				                <input type="text" class="form-control" readonly="readonly" name="claim_id" value="<?=$claim->rec;?>"/>
				            </div>
				            
				            <div class="form-group">
				                <label class="control-label">Choose Files</label>
				                <input type="file" class="form-control" name="receipt[]" multiple/>
				            </div>
				            <div class="form-group">
				                <input class="form-control btn btn-primary" type="submit" name="fileSubmit" value="UPLOAD"/>
				            </div>
				        </form>
				 </div>
				 
				 <p></p>
				 
				    <div class="col-sm-12">
				        <ul class="gallery" style="list-style: none;">
							<?php 
								echo list_s3_uploaded_documents($this->medical_model->uploaded_claim_documents($claim->rec,'claims'));
							?>
				            
				        </ul>
				    </div>
				    
				  	<a href="<?=base_url();?>claims.php/partner/medical_claims" class="btn btn-red btn-icon"><i class="fa fa-arrow-left"></i><?=get_phrase('back');?></a>
					
					<!-- <a href="<?php echo base_url();?>claims.php/partner/ziparchive/claims/<?php echo $claim->rec;?>/receipt" class="btn btn-orange btn-icon"><i class="fa fa-cloud-download"></i>Download All</a> -->
					
				</div>
			</div>		
	</div>
</div>
<script>

	
</script>