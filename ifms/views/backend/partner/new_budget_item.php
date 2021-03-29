<?php $fy_range = range(date('y') - 2, date('y') + 2);
$this_fy = get_fy(date("Y-m-d"), $this->session->center_id);
?>
<div class="row">
	<div class="col-sm-12">
		<?php echo form_open('', array('class' => 'form-horizontal form-groups-bordered validate')); ?>
		<div>
			<label for="" class="control-label col-sm-4">Choose FY: </label>
			<div class="col-sm-6">
				<select id="choose_yr" class="form-control" name="cur_fy">
					<option><?= get_phrase('select'); ?></option>
					<?php
					foreach ($fy_range as $fy) {
					?>
						<option value="<?= $fy; ?>">FY<?= $fy; ?></option>
					<?php
					}
					?>
				</select>
			</div>
			<div class="col-sm-2">
				<div class="badge badge-info"><?php echo "Current FY is FY" . $this_fy; ?></div>
			</div>
		</div>
		</form>
	</div>
</div>

<hr />


<div id="load_create_item" class="hidden">
	<?php 
	 include('modal_new_budget_item.php');
	?>
</div>


