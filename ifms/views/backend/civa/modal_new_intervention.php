<script type="text/javascript" src="<?php echo base_url();?>assets/js/select2/select2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/selectboxit/jquery.selectBoxIt.min.js"></script>

<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-primary " data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title">
					<i class="entypo-plus-squared"></i>
					<?php echo get_phrase('new_intervention_account'); ?>
				</div>
			</div>
			<div class="panel-body">
				<?php
				echo form_open(base_url() . 'ifms.php/civa/interventions/add/' . $param2, array('id' => 'frm_add', 'class' => 'form-horizontal form-groups-bordered validate', "autocomplete" => "off", 'enctype' => 'multipart/form-data'));


				?>

				<div id="" class="form-group">
					<label for="" class="col-xs-4 control-label"><?php echo get_phrase('CIV_code'); ?></label>
					<div class="col-xs-8"><INPUT type="text" name="AccNoCIVA" id="AccNoCIVA" class="form-control" required="required" /></div>
				</div>

				<!-- <div id="" class="form-group">
					<label for="" class="col-xs-4 control-label"><?php echo get_phrase('is_dct_intervention'); ?></label>
					<div class="col-xs-8">
						<select class='col-xs-8 form-control' name='is_direct_cash_transfer' id='is_direct_cash_transfer'>
							<option value=''><?= get_phrase('select_option'); ?></option>
							<option value='0'><?= get_phrase('no'); ?></option>
							<option value='1'><?= get_phrase('yes'); ?></option>
						</select>
					</div>
				</div> -->

				<?php
				$rev_acc = $this->db->get_where('accounts', array('AccGrp' => '1', 'Active' => '0'))->result_object();
				
				?>

				<div id="" class="form-group">
					<label for="" class="col-xs-4 control-label"><?php echo get_phrase('account_code'); ?></label>
					<div class="col-xs-8">
						<select class="form-control" id="accID" name="accID" required="required">
							<option value=""><?php echo get_phrase('select'); ?></option>
							<?php
							foreach ($rev_acc as $row) :
							?>
								<option value="<?= $row->accID ?>"><?= $row->AccText; ?> - <?= $row->AccName; ?></option>
							<?php
							endforeach;
							?>
						</select>
					</div>
				</div>

				<div id="" class="form-group">
					<label for="" class="col-xs-4 control-label"><?php echo get_phrase('allocate_ICPs'); ?></label>
					<div class="col-xs-8">
						<textarea id="allocate" name="allocate" class="form-control" placeholder="Enter each ICP on a new line as KEXXX (No Zero After KE)"></textarea>
					</div>
				</div>
				<!-- Voucher Items Select-->

				<?php 
					if($this->config->item('use_dct_detail_row')){

					//Recipients
					$recipients=$this->db->select(array('voucher_item_type_id','voucher_item_type_name'))->get_where('voucher_item_type',array('voucher_type_item_is_active'=>1))->result_array();

					//Support modes
					$support_modes=$this->db->select(array('support_mode_id','support_mode_name'))->get_where('support_mode',array('support_mode_is_active'=>1))->result_array();
					
				?>

				<div id="" class="form-group">
					<label for="" class="col-xs-4 control-label"><?php echo get_phrase('recipient'); ?></label>
					<div class="col-xs-8">
						
						<select class='form-control select2_element' name='recipient[]' multiple>
						  
						  <?php foreach($recipients as $recipient){?>

                           <option value='<?=$recipient['voucher_item_type_id']?>'><?= $recipient['voucher_item_type_name']?></option>

						  <?php }?>
						</select>
					</div>
				</div>


				<!-- Support mode Select-->

				<div id="" class="form-group">
					<label for="" class="col-xs-4 control-label"><?php echo get_phrase('support_modes'); ?></label>
					<div class="col-xs-8">
						
						<select class='form-control select2_element' name='support_mode[]' multiple>
						  
						  <?php foreach($support_modes as $support_mode){?>

                           <option value='<?=$support_mode['support_mode_id']?>'><?= $support_mode['support_mode_name']?></option>

						  <?php }?>
						</select>
					</div>
				</div>

				<?php 
					}
				?>			

				<div id="" class="form-group">
					<label for="" class="col-xs-4 control-label"><?php echo get_phrase('closure_date'); ?></label>
					<div class="col-xs-8">
						<div class="input-group">
							<input type="text" name="closureDate" id="closureDate" class="form-control datepicker" data-validate="required" data-message-required="<?php echo get_phrase('value_required'); ?>" data-format="yyyy-mm-dd" data-start-date="" data-end-date="" readonly="readonly">

							<div class="input-group-addon">
								<a href="#"><i class="entypo-calendar"></i></a>
							</div>
						</div>
					</div>

				</div>

				<div class="col-offset-4 col-xs-4 col-offset-4">
					<button id="btn_civ_add" type="submit" class="btn btn-primary btn-icon"><i class="entypo-plus"></i><?php echo get_phrase('add'); ?></button>
				</div>

				</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {

		$('.select2_element').select2();

		$('.datepicker').datepicker({
			format: 'yyyy-mm-dd'
		});

	});


</script>