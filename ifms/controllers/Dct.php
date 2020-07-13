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
	
	function dct_documents_download($fcp_number, $tym, $vnumber)
	{

		if (file_exists('uploads/dct_documents/' . $fcp_number . '/' . date('Y-m', $tym) . '/' . $vnumber . '/')) {
			$map = directory_map('uploads/dct_documents/' . $fcp_number . '/' . date('Y-m', $tym) . '/' . $vnumber . '/', FALSE, TRUE);

			foreach ($map as $row) :

				$path = 'uploads/dct_documents/' . $fcp_number . '/' . date('Y-m', $tym) . '/' . '/' . $vnumber . '/' . $row;

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

		$this->db->select(array('support_mode_id','support_mode_name','support_mode_is_dct'));
		
		$this->db->join('voucher_type_support_mode','voucher_type_support_mode.fk_support_mode_id=support_mode.support_mode_id');
		$this->db->join('voucher_type','voucher_type.voucher_type_id=voucher_type_support_mode.fk_voucher_type_id');
		$this->db->join('accounts_support_mode','accounts_support_mode.fk_support_mode_id=support_mode.support_mode_id');
		$this->db->join('accounts','accounts.accID=accounts_support_mode.fk_accounts_id');
		
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


		foreach (new DirectoryIterator($temp_hashed_directory_path) as $detail_temp_directory) {
			if ($detail_temp_directory->isDot()) continue;
			
			$this->rrmdir($temp_hashed_directory_path .DS. $detail_temp_directory);
		}		

		rmdir($temp_hashed_directory_path);

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
}