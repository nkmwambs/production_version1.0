<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

	DEFINE('DS', DIRECTORY_SEPARATOR);		

/*	
 *	@author 	: Nicodemus Karisa
 *	date		: 25 July, 2018
 *	Compassion International 
 *	https://www.compassion-africa.org
 *	support@compassion-africa.org
 */

class Dct extends CI_Controller
{
    
    
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->load->model('dct_model');
		$this->load->library('zip');
		
       /*cache control*/
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');

		
	}
	
	
	
	
	function temp_folder_hash($voucher_number){
		echo $this->dct_model->temp_folder_hash($voucher_number);
	}
	
	function create_uploads_temp()
	{

		$voucher_number = $_POST['voucher_number'];
		$voucher_detail_row_index = $_POST['voucher_detail_row_number'];
		$support_mode_id = $_POST['support_mode_id'];

		//Hash the folder to make user depended
		$hash = $this->dct_model->temp_folder_hash($voucher_number); //.random_int(10,1000000);
		$detail_folder_name = $voucher_number .'_'. $voucher_detail_row_index .'_'. $support_mode_id; //.random_int(10,1000000);

		//$hash = md5($hash_main_folder_name);

		//Folder for temp and call the upload_files method to temperarily hold files on server
		
		$storeFolder = 'uploads' . DS . 'temps' . DS . $hash . DS . $detail_folder_name;

		if (
			is_array($this->upload_files($storeFolder)) &&
			count($this->upload_files($storeFolder)) > 0
		) {
			$info = ['temp_id' => $hash];

			$files_array = array_merge($this->upload_files($storeFolder), $info);

			echo json_encode($files_array);

		} else {
			echo 0;
		}

	}

    function upload_files($storeFolder)
	{
		//uploads/DCT documents/FCPID/month/voucher/files
		$path_array = explode(DS, $storeFolder);

		$path = [];

		for ($i = 0; $i < count($path_array); $i++) {

			array_push($path, $path_array[$i]);

			$modified_path = implode(DS, $path);

			if (!file_exists($modified_path)) {
				mkdir($modified_path, 0777);
			}
		}

		if (!empty($_FILES)) {

			for ($i = 0; $i < count($_FILES['fileToUpload']['name']); $i++) {
				$tempFile = $_FILES['fileToUpload']['tmp_name'][$i];

				$targetPath = BASEPATH . DS . '..' . DS . $storeFolder . DS;
				$targetFile =  $targetPath . $_FILES['fileToUpload']['name'][$i];

				move_uploaded_file($tempFile, $targetFile);
			}

			return $_FILES;
		}
		
	}


	function remove_dct_files_in_temp($voucher_number, $voucher_detail_row_number, $support_mode_id,$remove_all_files=false)
	{

		$output = [];

		$detail_folder_name = $voucher_number.'_'.$voucher_detail_row_number.'_'.$support_mode_id;
		$storeFolder = BASEPATH . DS . '..' . DS . 'uploads' . DS . 'temps' . DS . $this->dct_model->temp_folder_hash($voucher_number). DS . $detail_folder_name;
		
		//Loop the $hash directory and delete the selected file
		$data = $this->input->post('file_name');

        //If true loop and delete all files in Temp
		if($remove_all_files==true){
			
			foreach (new DirectoryIterator($storeFolder) as $fileInfo) {
				if ($fileInfo->isDot()) continue;


					unlink($storeFolder . DS . $fileInfo);

					$output['file_name'] = 'All';
				
			}

		}
		else{

			foreach (new DirectoryIterator($storeFolder) as $fileInfo) {
				if ($fileInfo->isDot()) continue;

				if ($fileInfo->getFilename() == $data) {

					unlink($storeFolder . DS . $fileInfo);

					$output['file_name'] = $fileInfo->getFilename();
				}
			}

		}
	
		

        //Onduso comment: When echo is used in this 'count_files_in_temp_dir' "count_of_files" =null so we have to use return for to count of files
		$output['count_of_files'] = $this->count_files_in_temp_dir($voucher_detail_row_number,$voucher_number,$support_mode_id, true);

		echo json_encode($output);
	}
	
	function check_if_support_requires_upload($support_mode_id){
		$is_support_mode_require_upload=$this->db->get_where('support_mode', array('support_mode_id'=>$support_mode_id))->row()->support_mode_is_dct;

		echo $is_support_mode_require_upload;
	}
	
	private function get_bank_code()
	{

		if ($this->db->get_where('projectsdetails', array('icpNo' => $this->session->center_id))->num_rows() > 0) {

			return $bank_code = $this->db->get_where('projectsdetails', array('icpNo' => $this->session->center_id))->row()->bankID;
		} else {

			return $bank_code = 0;
		}
	}

	function is_reference_number_exist($ref_number_from_post, $voucher_number_from_post)
	{

		$bank_code = $this->get_bank_code();
		$reference_number_in_db = trim($ref_number_from_post) . '-' . $bank_code;
		$voucher_number_in_db = trim($voucher_number_from_post);

		/*
		   Get the reference number and voucher numbers and then:
		   -Return 1 if ref_no and voucher_number exist
		   -Return 2 if ref_no exists and voucher_number does not exist
		   -Return 3 if ref_no does not exist and voucher number exists
		   -Return 0 if ref_no and voucher_number do not exist
		*/
		$result_reference_no = $this->db->select(array('ChqNo'))->get_where('voucher_header', array('ChqNo' => $reference_number_in_db, 'icpNo' => $this->session->center_id))->row_array('ChqNo');
		$result_voucher_no = $this->db->select(array('VNumber'))->get_where('voucher_header', array('VNumber' => $voucher_number_in_db, 'icpNo' => $this->session->center_id))->row_array('VNumber');

		if ((!empty($result_reference_no)) && (!empty($result_voucher_no))) {
			echo '1';
		} else if (!empty($result_reference_no) && empty($result_voucher_no)) {
			echo '2';
		} else if (!empty($result_voucher_no) && empty($result_reference_no)) {
			echo '3';
		} else {
			echo '0';
		}
	}

	function delete_empty_folder($storeFolder)
	{
		$iterator = new \FilesystemIterator($storeFolder);

		if (!$iterator->valid()) {
			rmdir($storeFolder);
		} else {
			foreach (new DirectoryIterator($storeFolder) as $fileInfo) {
				if ($fileInfo->isDot()) continue;

				if ($fileInfo->isFile()) {
					unlink($storeFolder . DS . $fileInfo);
				}
			}

			rmdir($storeFolder);
		}
	}



	function check_if_temp_session_is_empty($voucher_number)
	{

		$storeFolder = BASEPATH . DS . '..' . DS . 'uploads' . DS . 'temps' . DS . $this->dct_model->temp_folder_hash($voucher_number);

		if (file_exists($storeFolder)) {
			
			$this->delete_empty_folder($storeFolder);
		}

	}

	function move_temp_files_to_dct_document($temp_dir_name, $voucher_date, $voucher_number)
	{
		$month_folder = date('Y-m', strtotime($voucher_date));

		if (!file_exists('uploads' . DS . 'dct_documents'))
			mkdir('uploads' . DS . 'dct_documents');

		if (!file_exists('uploads' . DS . 'dct_documents' . DS . $this->session->center_id))
			mkdir('uploads' . DS . 'dct_documents' . DS . $this->session->center_id);

		if (!file_exists('uploads' . DS . 'dct_documents' . DS . $this->session->center_id . DS . $month_folder))
			mkdir('uploads' . DS . 'dct_documents' . DS . $this->session->center_id . DS . $month_folder);

		$final_file_path = 'uploads' . DS . 'dct_documents' . DS . $this->session->center_id . DS . $month_folder . DS . $voucher_number;

		return rename($temp_dir_name, $final_file_path);
	}

	function get_accounts_for_voucher_item_type(int $voucher_item_type_id=0){
		echo json_encode($this->dct_model->get_accounts_related_voucher_item_type($voucher_item_type_id));

	 }

	 function get_support_modes_for_voucher_type($voucher_type_abbrev){
		$this->db->select(array('support_mode_id','support_mode_name','support_mode_is_dct'));
		
		$this->db->join('voucher_type_support_mode','voucher_type_support_mode.fk_support_mode_id=support_mode.support_mode_id');
		$this->db->join('voucher_type','voucher_type.voucher_type_id=voucher_type_support_mode.fk_voucher_type_id');
		
		$this->db->where(array('support_mode_is_active'=>1,'voucher_type_abbrev'=>$voucher_type_abbrev));
		
		$support_mode_obj =  $this->db->get('support_mode');

		$support_modes = [];

		if($support_mode_obj->num_rows() > 0){
			$support_modes = $support_mode_obj->result_array();
		}

		return $support_modes;
	}

	function get_support_modes(){

		$voucher_type_abbrev = $this->input->post('voucher_type_abbrev');
		$accno = $this->input->post('accno');
		$civa_id = $this->input->post('civa_id');

		$this->db->select(array('support_mode_id','support_mode_name','support_mode_is_dct'));
		
		$this->db->join('voucher_type_support_mode','voucher_type_support_mode.fk_support_mode_id=support_mode.support_mode_id');
		$this->db->join('voucher_type','voucher_type.voucher_type_id=voucher_type_support_mode.fk_voucher_type_id');
		
		if($civa_id > 0){
			$this->db->join('civa_support_mode','civa_support_mode.fk_support_mode_id=support_mode.support_mode_id');
			$this->db->join('civa','civa.civaID=civa_support_mode.fk_civa_id');
			$this->db->join('accounts','accounts.accID=civa.accID');
		}else{
			$this->db->join('accounts_support_mode','accounts_support_mode.fk_support_mode_id=support_mode.support_mode_id');
			$this->db->join('accounts','accounts.accID=accounts_support_mode.fk_accounts_id');
		}
		
		
		$this->db->where(array('support_mode_is_active'=>1,'voucher_type_abbrev'=>$voucher_type_abbrev,'AccNo'=>$accno));
		
		$support_mode_obj =  $this->db->get('support_mode');

		$support_modes = [];

		if($support_mode_obj->num_rows() > 0){
			$support_modes = $support_mode_obj->result_array();
		}

		echo json_encode($support_modes);
	}

	function remove_all_temp_files($voucher_number){
		$hash = $this->dct_model->temp_folder_hash($voucher_number); 
		$cnt = 0;
		
		
		$temp_hashed_directory_path = BASEPATH . DS . '..' . DS . 'uploads' . DS . 'temps' . DS . $hash;

        if(file_exists($temp_hashed_directory_path)){

			foreach (new DirectoryIterator($temp_hashed_directory_path) as $detail_temp_directory) {
				if ($detail_temp_directory->isDot()) continue;
				
				$this->rrmdir($temp_hashed_directory_path .DS. $detail_temp_directory);
			}		
	
			rmdir($temp_hashed_directory_path);

		}
		echo $cnt;

	}

	function rrmdir($dir) { 
		if (is_dir($dir)) { 
		  $objects = scandir($dir);
		  foreach ($objects as $object) { 
			if ($object != "." && $object != "..") { 
			  if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
				rrmdir($dir. DIRECTORY_SEPARATOR .$object);
			  else
				unlink($dir. DIRECTORY_SEPARATOR .$object); 
			} 
		  }
		  rmdir($dir); 
		} 
	  }
	

	function remove_voucher_row_dct_files_in_temp($voucher_number, $voucher_detail_row_number, $support_mode_id){
		$hash = $this->dct_model->temp_folder_hash($voucher_number); 
		$detail_folder = $voucher_number.'_'.$voucher_detail_row_number.'_'.$support_mode_id;

		//Folder path
		$storeFolder = BASEPATH . DS . '..' . DS . 'uploads' . DS . 'temps' . DS . $hash . DS . $detail_folder;
		
		$cnt = 0;
		
		$count_files_in_temp_dir = $this->count_files_in_temp_dir($voucher_detail_row_number, $voucher_number, $support_mode_id);

		if($count_files_in_temp_dir > 0){
	
				foreach (new DirectoryIterator($storeFolder) as $fileInfo) {
					if ($fileInfo->isDot()) continue;
		
					if ($fileInfo->isFile()) {
						unlink($storeFolder . DS . $fileInfo);
						$cnt++;
					}
				}
				rmdir($storeFolder);
			
		}

		echo $cnt;
		
	}

	private function count_files_in_temp_dir($voucher_detail_row_index,$voucher_number, $support_mode_id){

		$filecount = 0;

		$detail_folder_name = $voucher_number . '_' . $voucher_detail_row_index . '_' . $support_mode_id;
		$storeFolder = BASEPATH . DS . '..' . DS . 'uploads' . DS . 'temps' . DS . $this->dct_model->temp_folder_hash($voucher_number) . DS . $detail_folder_name;
		$files2 = glob($storeFolder . "/*.*");

		if ($files2) {
			$filecount = count($files2);
		}  
		return $filecount; 
	    
	}

	function count_files_in_temp_dir_for_ajax_use($voucher_detail_row_index,$voucher_number, $support_mode_id){

		$count_of_files=$this->count_files_in_temp_dir($voucher_detail_row_index,$voucher_number, $support_mode_id);

		echo $count_of_files;
	}

	function check_if_mode_is_dct($support_mode_id){
		$mode_is_dct = $this->db->get_where('support_mode',array('support_mode_id'=>$support_mode_id))->row()->support_mode_is_dct;

		echo $mode_is_dct;
	}


	function get_uploaded_support_mode_files($voucher_detail_row_index, $voucher_number, $support_mode_id){
		$result  = array();
		
		$hash = $this->dct_model->temp_folder_hash($voucher_number);
		$detail_folder_name = $voucher_number .'_'. $voucher_detail_row_index .'_'. $support_mode_id;
		
		$storeFolder = BASEPATH . DS . '..' . DS . 'uploads' . DS . 'temps' . DS . $hash . DS . $detail_folder_name;

		if(file_exists($storeFolder)){

			$result['store_folder'] = $storeFolder;

			$files = scandir($storeFolder);                 
			if ( false!==$files ) {
				foreach ( $files as $file ) {
					if ( '.'!=$file && '..'!=$file) {       
						$obj['name'] = $file;
						$obj['size'] = filesize($storeFolder. DS .$file);
						$result['uploaded_files'][] = $obj;
					}
				}
			}
		}
		
		header('Content-type: text/json');              
		header('Content-type: application/json');
		echo json_encode($result);
	}
	
	function load_dct_data_to_view_voucher($voucher_id){
		$data = [];
		$page_view = [];

		
		$this->db->select(array('voucher_header.icpNo as icpNo','voucher_header.VNumber as VNumber','voucher_header.chqNo as chqNo'));
		$this->db->select(array('voucher_header.TDate as TDate','voucher_header.Payee as Payee','voucher_header.Address as Address'));
		$this->db->select(array('voucher_header.TDescription as TDescription','voucher_header.VType as VType'));
		$this->db->select(array('voucher_body.Qty as Qty','voucher_item_type.voucher_item_type_name as voucher_item_type_name'));
		$this->db->select(array('support_mode.support_mode_name as support_mode_name','voucher_body.Details as Details'));
		$this->db->select(array('voucher_body.UnitCost as UnitCost','voucher_body.Cost as Cost','voucher_body.AccNo as AccNo'));
		$this->db->select(array('accounts.AccText as AccText','support_mode.support_mode_is_dct as support_mode_is_dct','support_mode.support_mode_id as support_mode_id'));

		$this->db->join('voucher_header','voucher_header.hID=voucher_body.hID');
		$this->db->join('accounts','accounts.AccNo=voucher_body.AccNo');
		$this->db->join('voucher_item_type','voucher_item_type.voucher_item_type_id=voucher_body.fk_voucher_item_type_id');
        $this->db->join('support_mode','support_mode.support_mode_id=voucher_body.fk_support_mode_id');
		$voucher_details_obj = $this->db->get_where('voucher_body',array('voucher_header.hID'=>$voucher_id));

		// Check if the voucher has support mode/ voucher item type not zero
		if($voucher_details_obj->num_rows() > 0){
			$data['record'] = $voucher_details_obj->row_array();
			$data['body'] = $voucher_details_obj->result_array();
			$page_view['dct_view'] = $this->load->view('backend/partner/dct_view_voucher',$data,true);
		}

		echo json_encode($page_view);
	}


	function dct_documents_download($fcp_number, $tym, $vnumber, $detail_folder = '')
	{

		$uploads_path = 'uploads/dct_documents/' . $fcp_number . '/' . date('Y-m', $tym) . '/' . $vnumber . '/';

		if($detail_folder !== ''){
			$uploads_path = 'uploads/dct_documents/' . $fcp_number . '/' . date('Y-m', $tym) . '/' . $vnumber . '/'. $detail_folder .'/';
		}

		if (file_exists($uploads_path)) {
			$map = directory_map($uploads_path, FALSE, TRUE);

			foreach ($map as $row) :

				$path = $uploads_path . $row;
				
				$data = file_get_contents($path);

				$this->zip->add_data($row, $data);
			endforeach;


			// Write the zip file to a folder on your server. Name it "my_backup.zip"
			$this->zip->archive('downloads/my_backup_' . $this->session->login_user_id . '.zip');

			// Download the file to your desktop. Name it "my_backup.zip"

			$backup_file = 'downloads_' . $this->session->login_user_id . date("Y_m_d_H_i_s") . '.zip';

			$this->zip->download($backup_file);

			unlink('downloads/' . $backup_file);
		}
	}

	function voucher_type_item_accounts_matrix(){
		$this->db->select(array('voucher_item_type.voucher_item_type_id as voucher_item_type_id','voucher_item_type_name'));
		$this->db->select(array('accID','AccText','voucher_type_item_is_active','voucher_type_item_is_beneficiary','voucher_type_item_is_household'));
		$this->db->join('voucher_items_with_accounts','voucher_items_with_accounts.voucher_item_type_id=voucher_item_type.voucher_item_type_id','left');
		$this->db->join('accounts','accounts.accID=voucher_items_with_accounts.accounts_id','left');
		$ungrouped_matrix = $this->db->get('voucher_item_type')->result_array();

		$matrix = [];

		foreach($ungrouped_matrix as $matrix_element){
			$matrix[$matrix_element['voucher_item_type_id'].'-'.$matrix_element['voucher_item_type_name']]['accounts'][$matrix_element['accID']] = $matrix_element['AccText'];
			$matrix[$matrix_element['voucher_item_type_id'].'-'.$matrix_element['voucher_item_type_name']]['status'] = $matrix_element['voucher_type_item_is_active'];
			$matrix[$matrix_element['voucher_item_type_id'].'-'.$matrix_element['voucher_item_type_name']]['is_beneficiary'] = $matrix_element['voucher_type_item_is_beneficiary'];
			$matrix[$matrix_element['voucher_item_type_id'].'-'.$matrix_element['voucher_item_type_name']]['is_household'] = $matrix_element['voucher_type_item_is_household'];
		}

		return $matrix;
	}

	function support_mode_accounts_matrix(){
		$this->db->select(array('support_mode.support_mode_id as support_mode_id','support_mode_name'));
		$this->db->select(array('accID','AccText','support_mode_is_active','support_mode_is_dct'));
		$this->db->join('accounts_support_mode','accounts_support_mode.fk_support_mode_id=support_mode_id','left');
		$this->db->join('accounts','accounts.accID=accounts_support_mode.fk_accounts_id','left');
		$ungrouped_matrix = $this->db->get('support_mode')->result_array();

		$matrix = [];

		foreach($ungrouped_matrix as $matrix_element){
			$matrix[$matrix_element['support_mode_id'].'-'.$matrix_element['support_mode_name']]['accounts'][$matrix_element['accID']] = $matrix_element['AccText'];
			$matrix[$matrix_element['support_mode_id'].'-'.$matrix_element['support_mode_name']]['status'] = $matrix_element['support_mode_is_active'];
			$matrix[$matrix_element['support_mode_id'].'-'.$matrix_element['support_mode_name']]['is_dct'] = $matrix_element['support_mode_is_dct'];
		}

		return $matrix;
	}

	function get_all_expense_accounts(){
		$this->db->select(array('accID','AccText'));
		$this->db->where_in('AccGrp',array(0,3));
		$this->db->where(array('Active'=>1));
		$accounts = $this->db->get('accounts')->result_array();

		$accID = array_column($accounts,'accID');
		$accText = array_column($accounts,'AccText');

		return array_combine($accID,$accText);
	}

	function dct_settings(){
		if ($this->session->userdata('admin_login') != 1)
			redirect(base_url(), 'refresh');
		
		$page_data['voucher_type_item_accounts_matrix'] = $this->voucher_type_item_accounts_matrix();
		$page_data['support_mode_accounts_matrix'] = $this->support_mode_accounts_matrix();
		$page_data['expense_accounts'] = $this->get_all_expense_accounts();
		$page_data['account_type']= 'admin';
		$page_data['page_name']  = 'dct_settings';
        $page_data['page_title'] = get_phrase('dct_settings');
        $this->load->view('backend/index', $page_data);	
	}

	function update_support_mode_accounts(){
		$post = $this->input->post();

		$selected_account_ids = $post['account_ids'];
		$mode_id = $post['mode_id'];

		$message = get_phrase('error_occurred');

		//Check if account is not there for the mode and 
		$this->db->select(array('fk_accounts_id as account_id'));
		$all_accounts_in_mode_raw = $this->db->get_where('accounts_support_mode',
			array('fk_support_mode_id'=>$mode_id))->result_array();	

		$all_accounts_in_mode = array_column($all_accounts_in_mode_raw,'account_id');

		foreach($all_accounts_in_mode as $used_account_id){

			if(!in_array($used_account_id,$selected_account_ids)){
				// Delete the record for this account
				$this->db->where(array('fk_accounts_id'=>$used_account_id,'fk_support_mode_id'=>$mode_id));
				$this->db->delete('accounts_support_mode');

				$message = get_phrase('account_deleted_successfully');
			}
		}

		foreach($selected_account_ids as $selected_account_id){

			if(!in_array($selected_account_id,$all_accounts_in_mode)){
				// Insert the record for this account
				$insert_data['fk_accounts_id'] = $selected_account_id;
				$insert_data['fk_support_mode_id'] = $mode_id;
				$insert_data['accounts_support_mode_created_by'] = $this->session->login_user_id;
				$insert_data['accounts_support_mode_create_date'] = date('Y-md');
				$insert_data['accounts_support_mode_last_modified_by'] = $this->session->login_user_id;
				$insert_data['accounts_support_mode_last_modified_date'] =date('Y-m-d h:i:s');

				$this->db->insert('accounts_support_mode',$insert_data);

				$message = get_phrase('account_added_successfully');
			}
		}
		

		echo $message;
	}

	function update_voucher_item_type_accounts(){
		$post = $this->input->post();

		$selected_account_ids = $post['account_ids'];
		$type_id = $post['type_id'];

		$message = get_phrase('error_occurred');

		//Check if account is not there for the mode 
		$this->db->select(array('accounts_id as account_id'));
		$all_accounts_in_type_raw = $this->db->get_where('voucher_items_with_accounts',
			array('voucher_item_type_id'=>$type_id))->result_array();	

		$all_accounts_in_type = array_column($all_accounts_in_type_raw,'account_id');

		foreach($all_accounts_in_type as $used_account_id){

			if(!in_array($used_account_id,$selected_account_ids)){
				// Delete the record for this account
				$this->db->where(array('accounts_id'=>$used_account_id,'voucher_item_type_id'=>$type_id));
				$this->db->delete('voucher_items_with_accounts');

				$message = get_phrase('account_deleted_successfully');
			}
		}

		foreach($selected_account_ids as $selected_account_id){

			if(!in_array($selected_account_id,$all_accounts_in_type)){
				// Insert the record for this account
				$insert_data['accounts_id'] = $selected_account_id;
				$insert_data['voucher_item_type_id'] = $type_id;
				$insert_data['voucher_items_with_accounts_created_by'] = $this->session->login_user_id;
				$insert_data['voucher_items_with_accounts_created_date'] = date('Y-md');
				$insert_data['voucher_items_with_accounts_created_by'] = $this->session->login_user_id;
				$insert_data['voucher_items_with_accounts_last_modified_date'] =date('Y-m-d h:i:s');

				$this->db->insert('voucher_items_with_accounts',$insert_data);

				$message = get_phrase('account_added_successfully');
			}
		}
		

		echo $message;
	}
}