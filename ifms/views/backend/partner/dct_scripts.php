<script>

	function check_if_dct_upload_empty() {

		var upload_count_inputs= $('.check_upload_count');
        var upload_is_empty=true;
		$.each(upload_count_inputs,function(i, el){

			if($(el).val()==0){
				upload_is_empty=false;
				return false;
			}
		});

		return upload_is_empty;


	}

	function add_dct_detail_row_and_header(elem) {

		// Change detail th row values
		$('#th_detail_table').html(toggle_voucher_detail_header_row());

		// Load DCT detail rows
		toggle_voucher_detail_row();
	}

	function toggle_voucher_detail_header_row() {

		var obj = [
			"<th><?php echo get_phrase('delete_row'); ?></th>",
			"<th><?php echo get_phrase('quantity'); ?></th>",
			"<th><?php echo get_phrase('Recipient'); ?></th>",
			"<th><?php echo get_phrase('account'); ?></th>",
			"<th><?php echo get_phrase('support_mode'); ?></th>",
			"<th><?php echo get_phrase('details_/_particulars'); ?></th>",
			"<th><?php echo get_phrase('unit_cost'); ?></th>",
			"<th><?php echo get_phrase('cost'); ?></th>",
			"<th><?php echo get_phrase('civ_code'); ?></th>",

		];

		var th_row = "";

		$(obj).each(function(i, el) {
			th_row += el;
		});

		return th_row;
	}

	function toggle_voucher_detail_row() {

		var vtype = $("#VTypeMain").val();

		var url = '<?php echo base_url(); ?>ifms.php/partner/voucher_accounts/' + vtype;

		$.ajax({
			url: url,
			success: function(response) {
				create_voucher_row(response, vtype);

			}
		});
	}
	

	function retrieve_size_of_files_in_row(file_size){
		//alert('Hello size:' + file_size);
		$("#compute_upload_size").val(file_size);
	}

	function create_uploaded_files_count_input(support_mode_select) {
		var support_mode = $(support_mode_select);

		var support_mode_id = support_mode.val();

		var parent_td_support_mode = support_mode.closest('td');

		var upload_count_inputs = parent_td_support_mode.find('input.check_upload_count');

		var url = '<?= base_url() ?>ifms.php/dct/check_if_support_requires_upload/' + support_mode_id;
		$.get(url, function(response) {

			if (response==1 && upload_count_inputs.length == 0) {

				parent_td_support_mode.append('<input type="hidden" value="0" class="check_upload_count">');

			}
			else if(response==0 && upload_count_inputs.length >0 ){

				parent_td_support_mode.find('input.check_upload_count').remove();

			}

		});




	}

	function create_voucher_row(response, vtype) {

		var obj_voucher_item_type = response.item_types;
		var obj = response.acc;
		var voucher_type_effect = response.voucher_type_effect;
		var obj_support_mode = response.support_modes;

		//alert(obj[0].AccText);

		var current_selected_mode_id = 0;

		var show_voucher_item_type = voucher_type_effect == 'expense' && obj_support_mode.length > 0 ? true : false;

		var table = '';
		//Modified by Onduso on 29/6/2020

		table = document.getElementById('bodyTable').children[1];
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);


		//Delete row cell added by onduso on 5/22/2020

		var cell0 = row.insertCell(0);
		var element0 = document.createElement("a");
		element0.type = "a";
		if (rowCount != 0) { //only provide delete btn if only rows >1
			element0.className = "btn btn-default glyphicon glyphicon-trash form-control";
		}
		//element0.className = "btn btn-default glyphicon glyphicon-trash form-control";
		cell0.appendChild(element0);

		//Quantity Column
		var cell1 = row.insertCell(1);
		var element1 = document.createElement("input");
		element1.type = "number";
		element1.step = "0.01"
		element1.name = "qty[]";
		element1.className = "qty accNos form-control";
		element1.id = "qty" + rowCount;
		element1.setAttribute('required', 'required');
		element1.onkeyup = function() {
			var x = this.value;
			var y = document.getElementById('unit' + rowCount).value;
			document.getElementById('cost' + rowCount).value = x * y;

			var sum = 0;
			$('.cost').each(function() {
				sum += parseFloat(this.value);
			});
			document.getElementById('totals').value = accounting.formatMoney(sum, {
				symbol: "<?php echo get_phrase('Kes.'); ?>",
				format: "%v %s"
			});

		};
		cell1.appendChild(element1);


		// Voucher Item Type/Recipient
		var cell2 = row.insertCell(2);
		cell2.className = 'td_voucher_item_type';
		var x = document.createElement("select");
		x.name = "voucher_item_type[]";
		x.setAttribute('required', 'required');
		show_voucher_item_type ? '' : x.setAttribute('disabled', 'disabled');
		x.className = 'form-control voucher_item_type';
		var option1 = document.createElement("option");
		option1.text = "Select ...";
		option1.value = "";
		x.add(option1, x[0]);

		for (i = 0; i < obj_voucher_item_type.length; i++) {
			var option = document.createElement("option");

			option.text = obj_voucher_item_type[i].voucher_item_type_name;
			option.value = obj_voucher_item_type[i].voucher_item_type_id;
			x.add(option, x[i]);

		}
		x.onchange = function() {

			populate_accounts(this);

		};

		cell2.appendChild(x);



		//Accounts Column
		var cell3 = row.insertCell(3);
		cell3.className = 'td_accounts';
		var x = document.createElement("select");
		x.name = "acc[]";
		x.setAttribute('required', 'required');
		x.setAttribute('disabled', 'disabled')
		x.className = 'form-control accNos acSelect';
		var option1 = document.createElement("option");
		option1.text = "Select ...";
		option1.value = "";
		x.add(option1, x[0]);
		
		for (i = 0; i < obj.length; i++) {
			var option = document.createElement("option");
			if (obj[i].AccTextCIVA !== null && obj[i].open === "1") {
				option.text = obj[i].AccNoCIVA;
				option.value = obj[i].AccNo;
			} else {
				option.text = obj[i].AccText + ' - ' + obj[i].AccName;
				option.value = obj[i].AccNo;
			}

			x.add(option, x[i]);

		}
		x.onchange = function() {
			//alert(obj.length);  
			//document.getElementById("civaCode" + rowCount).value = obj[2].civaID;
			//check_pc_other_ac_mix(this);
			//build_support_mode_list(this);
		};
		x.setAttribute('required', 'required');
		cell3.appendChild(x);


		//Support Modes Column
		var cell4 = row.insertCell(4);
		cell4.className = 'td_support_mode';
		var x = document.createElement("select");
		x.name = "support_mode[]";
		x.setAttribute('required', 'required');
		x.setAttribute('disabled', 'disabled');
		x.className = 'form-control accNos support_mode';
		var option1 = document.createElement("option");
		option1.text = "Select ...";
		option1.value = "";
		x.add(option1, x[0]);
		x.onclick = function(){
		
			current_selected_mode_id = $(this).val();
		},
		x.onchange = function() {
			

			remove_voucher_row_dct_files_in_temp(this,current_selected_mode_id);

			create_uploaded_files_count_input(this);

			
		};
		x.setAttribute('required', 'required');
		cell4.appendChild(x);

		var dct_uploads = document.createElement("i");
		dct_uploads.className = 'badge badge-primary dct_uploads_count_label ';
		dct_uploads.innerHTML = 0 + " files";
		dct_uploads.onclick = function() {
			//show_upload_area($(this).parent().find('.support_mode'),$(this).parent().find('.dct_ref_number').val());
			show_upload_area($(this).parent().find('.support_mode'));
		};
		dct_uploads.setAttribute('style', 'cursor:pointer;');
		cell4.appendChild(dct_uploads);

		// var check_upload_size = document.createElement("input");
		// check_upload_size.className = 'check_upload_size';
		// check_upload_size.value = 0;
		// check_upload_size.id='check_upload_size';
		// check_upload_size.name='check_upload_size[]';
		// cell4.appendChild(check_upload_size);




		//Details Column
		var cell5 = row.insertCell(5);
		var element5 = document.createElement("input");
		element5.type = "text";
		element5.name = "desc[]";
		element5.className = "desc accNos form-control";
		element5.id = "desc" + rowCount;
		element5.setAttribute('required', 'required');
		cell5.appendChild(element5);

		//Unit Cost Column
		var cell6 = row.insertCell(6);
		var element6 = document.createElement("input");
		element6.type = "number";
		element6.step = "0.01"
		element6.name = "unit[]";
		element6.className = "unit accNos form-control";
		element6.id = "unit" + rowCount;
		element6.onkeyup = function() {
			var x = this.value;
			var y = document.getElementById('qty' + rowCount).value;
			document.getElementById('cost' + rowCount).value = x * y;

			var sum = 0;
			$('.cost').each(function() {
				sum += parseFloat(this.value);
			});
			document.getElementById('totals').value = accounting.formatMoney(sum, {
				symbol: "<?php echo get_phrase('Kes.'); ?>",
				format: "%v %s"
			});

		};
		element6.setAttribute('required', 'required');
		cell6.appendChild(element6);

		//Cost Column
		var cell7 = row.insertCell(7);
		var element7 = document.createElement("input");
		element7.type = "number";
		element7.step = "0.01"
		element7.name = "cost[]";
		element7.setAttribute('readonly', 'readonly');
		element7.className = "cost accNos form-control";
		element7.id = "cost" + rowCount;
		element7.setAttribute('required', 'required');
		cell7.appendChild(element7);


		//CIV Code Column
		var cell8 = row.insertCell(8);
		cell8.className = 'td_civacode';
		var element8 = document.createElement("input");
		element8.type = "text";
		element8.name = "civaCode[]";
		element8.setAttribute('readonly', 'readonly');
		element8.className = "civaCode form-control";
		element8.id = "civaCode" + rowCount;
		cell8.appendChild(element8);

		//}
	}


	$(document).on('change','.acSelect',function(){
		var selectedIndex = parseInt($(this).prop('selectedIndex')) - 1;

		//Find the closest td with accounts dropdown
		var voucher_item_type_value =$(this).closest('tr').find('.td_voucher_item_type').length>0? $(this).closest('tr').find('.td_voucher_item_type').find('select').val():0;
		var input_civa_code = $(this).closest('tr').find('.td_civacode').find('input');
		var vtype = $("#VTypeMain").val();
		var acSelect = $(this);

		//Get the accounts form server
		var url = '<?= base_url() ?>ifms.php/partner/voucher_accounts/' + vtype + '/' + voucher_item_type_value;
		var civa_id = 0;

		$.get(url,function(response){
			if(response.acc[selectedIndex].civaID){
				civa_id = response.acc[selectedIndex].civaID;
				input_civa_code.val(civa_id);
			}else{
				input_civa_code.val(0);
			}

			build_support_mode_list(acSelect, civa_id);
			
		});

	});



	function populate_accounts(recipient_select) {
		//Find the closest td with accounts dropdown
		var accounts_dropdown = $(recipient_select).closest('tr').find('.td_accounts').find('select');
		//var civa_code = $(recipient_select).closest('tr').('input.civaCode')
		var voucher_item_type_value = $(recipient_select).val();
		var vtype = $("#VTypeMain").val();

		//Get the accounts form server
		var url = '<?= base_url() ?>ifms.php/partner/voucher_accounts/' + vtype + '/' + voucher_item_type_value;

		$.get(url, function(response_object) {

			//var response_object = JSON.parse(response);
			var obj = response_object['acc'];
			var options = "<option value='0'><?= get_phrase('select_account'); ?></option>";
			//Redraw the account dropdown with options
			
			if (obj.length > 0) {

					accounts_dropdown.removeAttr('disabled');

						for (i = 0; i < obj.length; i++) {
							
							if (obj[i].AccTextCIVA !== null && obj[i].open === "1") {
								options += "<option value='" + obj[i].AccNo + "'>" + obj[i].AccTextCIVA + "</option>";
							} else {
								options += "<option value='" + obj[i].AccNo + "'>" + obj[i].AccText + ' - ' + obj[i].AccName + "</option>";
							}

						}
						
						
						accounts_dropdown.html(options);

			} else {
				accounts_dropdown.prop('disabled', 'disabled');
			}
			accounts_dropdown.html(options);

		});


	}

	//END Onduso Added code



	function build_support_mode_list(acSelect, civa_id = 0) {

		//alert(civa_id);

		var sibling_support_mode_select = $(acSelect).closest('tr').find('.td_support_mode').find('select');
		var accno = $(acSelect).val();
		var voucher_type_abbrev = $('#VTypeMain').val();

		//alert(accno);

		var url = "<?= base_url(); ?>ifms.php/dct/get_support_modes";
		var data = {
			'accno': accno,
			'voucher_type_abbrev': voucher_type_abbrev,
			'civa_id':civa_id,
		};

		$.post(url, data, function(response) {
			var obj = JSON.parse(response);

			var options = "<option value='0'><?= get_phrase('select_support_mode'); ?></option>";

			if (obj.length > 0) {
				sibling_support_mode_select.removeAttr('disabled');

				for (var i = 0; i < obj.length; i++) {
					options += "<option value='" + obj[i].support_mode_id + "'>" + obj[i].support_mode_name + "</option>";
				}

				sibling_support_mode_select.html(options);
			} else {
				sibling_support_mode_select.prop('disabled', 'disabled');
			}

			sibling_support_mode_select.html(options);

		});

	}

	function enable_disabled_voucher_item_type(modes_select) {
		var sibling_voucher_item_type_select = $(modes_select).closest('tr').find('.td_voucher_item_type').find('select');

		var options = "<option value='0'><?= get_phrase('select_item_type'); ?></option>";

		if ($(modes_select).val() > 0) {

			var url = "<?= base_url(); ?>ifms.php/partner/get_voucher_item_types";

			$.get(url, function(response) {
				sibling_voucher_item_type_select.removeAttr('disabled');

				var obj = JSON.parse(response);

				for (var i = 0; i < obj.length; i++) {
					options += "<option value='" + obj[i].voucher_item_type_id + "'>" + obj[i].voucher_item_type_name + "</option>";
				}
				sibling_voucher_item_type_select.html(options);
			});
		} else {
			sibling_voucher_item_type_select.prop('disabled', 'disabled');
			sibling_voucher_item_type_select.html(options);
		}

	}

	function show_upload_area(modes_select) {
		var support_mode_id = $(modes_select).val();
		var voucher_detail_row_number = parseInt($(modes_select).closest('tr').index()) + 1;
		var voucher_number = $("#Generated_VNumber").val();

		var url = "<?= base_url(); ?>ifms.php/dct/check_if_mode_is_dct/" + support_mode_id;

		$.get(url, function(response) {
			if (response == 1) {
				$(modes_select).prop('onclick', showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_upload_dct_documents/' + voucher_number + '/' + voucher_detail_row_number + '/' + support_mode_id));
			}

		});
	}

	$(document).on('click', "#btn_save_uploads", function() {

		var voucher_detail_row_index = parseInt($(this).data('row_id'));

		var dct_uploads_count_label = $("#bodyTable tr").eq(voucher_detail_row_index).find('td.td_support_mode').find('i.dct_uploads_count_label');

		var voucher_number = $('#Generated_VNumber').val();

		var support_mode_id = $("#bodyTable tr").eq(voucher_detail_row_index).find('td.td_support_mode').find('select').val();


		
		//alert(support_mode_id);

		//alert(dct_uploads_count_label.hasClass('badge'));

		var url = "<?= base_url(); ?>ifms.php?/dct/count_files_in_temp_dir_for_ajax_use/" + voucher_detail_row_index + "/" + voucher_number + "/" + support_mode_id;

		$.get(url, function(response) {
			//alert(response);
			dct_uploads_count_label.html(response + " files [Click here to Update]");

			dct_uploads_count_label.siblings('input.check_upload_count').val(response);
            //var file_size=10;
			//retrieve_size_of_uploaded_files(file_size,voucher_detail_row_index);

		});


	});

	function remove_voucher_row_dct_files_in_temp(modes_select, initial_support_mode_id = 0) {

		var support_mode_id = $(modes_select).val();
		var voucher_detail_row_index = parseInt($(modes_select).closest('tr').index()) + 1;
		var voucher_number = $("#Generated_VNumber").val();

		var url = "<?= base_url() ?>ifms.php/dct/remove_voucher_row_dct_files_in_temp/" + voucher_number + "/" + voucher_detail_row_index + "/" + initial_support_mode_id;
		
		if(initial_support_mode_id > 0){
			var cfrm = confirm('Are you sure you want to change the support mode? You will loose file already uploaded for this row if accepted!');

			if(cfrm){
				$.get(url, function(response) {
					if (response == 0) {
						alert('All files are removed');
					}
					show_upload_area(modes_select);
				});
			}
			
		}else{
			show_upload_area(modes_select);
		}

	}


	function remove_all_temp_files(){
		var url = '<?=base_url();?>ifms.php/dct/remove_all_temp_files/'+$("#Generated_VNumber").val();

		$.get(url,function(response){
			//alert(response);
		});
	}

	function load_dct_data_to_view_voucher(voucher_id){
		var use_dct_detail_row =  <?=$this->config->item('use_dct_detail_row');?>;
		

			var url = "<?=base_url();?>ifms.php/dct/load_dct_data_to_view_voucher/"+voucher_id;
			$.get(url,function(response){
				var obj = JSON.parse(response);

				if(obj.dct_view.length > 0){
					$('#voucher_print').html(obj.dct_view);
				}
				
			})
		
		
	}
</script>

<style>
    #overlay{
    position: fixed; /* Sit on top of the page content */
    display: none; /* Hidden by default */
    width: 100%; /* Full width (cover the whole page) */
    height: 100%; /* Full height (cover the whole page) */
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0,0,0,0.5); /* Black background with opacity */
    z-index: 2; /* Specify a stack order in case you're using a different order for other elements */
    cursor: pointer; /* Add a pointer on hover */
}

#overlay img {
    position: absolute;
    top: 50%;
    left: 50%;
}
</style>

<div id="overlay"><img src='<?php echo base_url()."uploads/preloader4.gif";?>'/></div>

<script>

	$( document ).ajaxSend(function() {
	$("#overlay").css("display","block");
	});

	$(document).ajaxSuccess(function() {
		$("#overlay").css("display","none");
	});

	$(document).ajaxError(function(xhr) {
		alert('Error has occurred');
	});
        
</script>