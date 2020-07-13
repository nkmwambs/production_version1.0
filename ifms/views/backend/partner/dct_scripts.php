<script>
	// function dct_scripts_voucher_type_on_change(elem) {
	// 	var cheque_number = $('#ChqNo');

	// 	cheque_number.parent().removeClass('hidden');

	// 	cheque_number.prop('type', 'text');

	// 	// Change the title for the cheque number to reference number
	// 	cheque_number.prev().find('span').html('<?= get_phrase('reference_number'); ?>')

	// 	// Make the reference number field not readonly
	// 	cheque_number.removeAttr('readonly');

	// 	// Remove reversal control and add upload area
	// 	$("#td_reversal").remove();

	// 	$("#td_voucher_type").closest('tr').append('<td colspan="2" id="td_dropzone">' + create_upload_area() + '</td>');

	// 	// Dropzone api code
	// 	dropzoneLoad('myDropzone');

	// }

	// function create_upload_area() {
	// 	var upload_area = '<div id="myDropzone" class="dropzone">' +
	// 		'<div class="dropzone-previews"></div>' +
	// 		'<div class="fallback">' +
	// 		'<input name="fileToUpload" type="file" multiple />' +
	// 		'</div>' +
	// 		'</div> <input class="hidden" value="0" id="check_upload_count" name="" type="text"/>';

	// 	return upload_area;
	// }



	// function dropzoneLoad(myDropzoneId) {

	// 	var myDropzone = new Dropzone("#" + myDropzoneId, {
	// 		url: "<?= base_url() ?>ifms.php?/dct/create_uploads_temp/" + $("#Generated_VNumber").val(),
	// 		paramName: "fileToUpload", // The name that will be used to transfer the file
	// 		maxFilesize: 5, // MB
	// 		uploadMultiple: true,
	// 		addRemoveLinks: true,
	// 		parallelUploads: 10,
	// 		acceptedFiles: 'image/*,application/pdf,.doc,.docx,.xls,.xlsx,.csv',
	// 	});

	// 	myDropzone.on("success", function(file, response) {
	// 			if (response == 0) {
	// 				alert('Error in uploading files');
	// 				return false;
	// 			} else {
	// 				var json = JSON.parse(response);

	// 				var size_of_file = json.fileToUpload['size'][0];
	// 				var count_files = json.fileToUpload['size'].length;
	// 				if (size_of_file > 0 && count_files > 0) {

	// 					$('#check_upload_count').val(count_files);
	// 					$('#error_msg').html('');

	// 				}


	// 				//alert(response);
	// 			}
	// 			$('#myDropzone').css({
	// 				'border': '2px solid gray'
	// 			});
	// 			//$('#error_msg').html('');

	// 		}



	// 	);
	// 	myDropzone.on('removedfile', function(file) {

	// 		/* here do AJAX call to the server ... */
	// 		var url = "<?= base_url() ?>ifms.php/dct/remove_dct_files_in_temp/" + $("#VNumber").val();
	// 		var file_name = file.name;
	// 		$.ajax({
	// 			//async: false,
	// 			type: "POST",
	// 			url: url,
	// 			data: {
	// 				'file_name': file_name
	// 			},
	// 			// beforeSend: function() {
	// 			// 	$('#error_msg').html('<div style="text-align:center;"><img style="width:60px;height:60px;" src="<?php echo base_url(); ?>uploads/preloader4.gif" /></div>');
	// 			// },
	// 			success: function(response) {
	// 				//alert('This file'+data+' has been removed');
	// 				var count_files = $('#check_upload_count').val();

	// 				var remaining_files = parseInt(count_files) - 1;

	// 				$('#check_upload_count').val(remaining_files)

	// 				alert('This file ' + response + ' has been removed');
	// 			},

	// 		});

	// 	});
	// }


	// function check_if_dct_upload_empty() {

	// 	var check_empty = 1;
	// 	var voucher_type_main = $('#VTypeMain').val();
	// 	if ($('#check_upload_count').val() == 0 && (voucher_type_main == 'UDCTB' || voucher_type_main == 'UDCTC')) {

	// 		$('#error_msg').html('<?php echo get_phrase("Upload supporting document"); ?>');
	// 		$('#myDropzone').css({
	// 			'border': '2px solid red'
	// 		});
	// 		check_empty = 0

	// 	}
	// 	return check_empty;

	// }

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


	function create_voucher_row(response, vtype) {

		var obj_voucher_item_type = response.item_types;
		var obj = response.acc;
		var voucher_type_effect = response.voucher_type_effect;
		var obj_support_mode = response.support_modes;

		//alert(voucher_type_effect.length);

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


		// Voucher Item Type/Rcipient
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
			//alert("Hello!");  
			document.getElementById("civaCode" + rowCount).value = obj[this.selectedIndex].civaID;
			//check_pc_other_ac_mix(this);
			build_support_mode_list(this);
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
		x.onchange = function() {
			//enable_disabled_voucher_item_type(this);

			remove_support_documents(this);
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
		var element8 = document.createElement("input");
		element8.type = "text";
		element8.name = "civaCode[]";
		element8.setAttribute('readonly', 'readonly');
		element8.className = "civaCode form-control";
		element8.id = "civaCode" + rowCount;
		cell8.appendChild(element8);

		//}
	}




	function populate_accounts(account_code_select) {
		//Find the closest td with accounts dropdown
		var accounts_dropdown = $(account_code_select).closest('tr').find('.td_accounts').find('select');
		var voucher_item_type_value = $(account_code_select).val();

		//Get the accounts form server
		var url = '<?= base_url() ?>ifms.php/dct/get_accounts_for_voucher_item_type/' + voucher_item_type_value;

		$.get(url, function(response) {

			var response_object = JSON.parse(response);

			var options = "<option value='0'><?= get_phrase('select_account'); ?></option>";
			//Redraw the account dropdown with options
			if (response_object.length > 0) {

				accounts_dropdown.removeAttr('disabled');

				for (var index = 0; index < response_object.length; index++) {
					options += "<option value='" + response_object[index].accno + "'>" + response_object[index].acctext + ' - ' + response_object[index].accname + "</option>";
				}

				accounts_dropdown.html(options);

			} else {
				accounts_dropdown.prop('disabled', 'disabled');
			}
			accounts_dropdown.html(options);

		});


	}

	//END Onduso Added code

	function build_support_mode_list(acSelect) {
		var sibling_support_mode_select = $(acSelect).closest('tr').find('.td_support_mode').find('select');

		//sibling_support_mode_select.removeAttr('disabled')
		//alert(sibling_support_mode_select);

		var accno = $(acSelect).val();
		var voucher_type_abbrev = $('#VTypeMain').val();

		var url = "<?= base_url(); ?>ifms.php/dct/get_support_modes";
		var data = {
			'accno': accno,
			'voucher_type_abbrev': voucher_type_abbrev
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


			//dct_ref_number.val($("#modal_dct_reference").val());
		});


	});

	function remove_support_documents(modes_select) {

		var support_mode_id = $(modes_select).val();
		var voucher_detail_row_index = parseInt($(modes_select).closest('tr').index()) + 1;
		var voucher_number = $("#Generated_VNumber").val();

		var url = "<?= base_url() ?>ifms.php/dct/remove_all_dct_files_in_temp/" + voucher_number + "/" + voucher_detail_row_index + "/" + support_mode_id;

		$.get(url, function(response) {
			if (response < 0) {
				alert('All files are removed');
			}

			show_upload_area(modes_select);
		});
	}
</script>