<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*	
 *	@author 	: Joyonto Roy
 *	date		: 27 september, 2014
 *	FPS School Management System Pro
 *	http://codecanyon.net/user/FreePhpSoftwares
 *	support@freephpsoftwares.com
 */

class Protection extends CI_Controller
{
    
    
	function __construct()
	{
		parent::__construct();
		$this->load->database();
        $this->load->library('session');
		$this->load->library('zip');

       /*cache control*/
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
		
    }
    
    /***default functin, redirects to login page if no admin logged in yet***/
    public function index()
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
	}
	
	function covid19_vouchers($cluster_name,$support_mode_name,$account_code){

		$cluster_name = urldecode($cluster_name);
		$support_mode_name =  urldecode($support_mode_name);
		$account_code = urldecode($account_code);

		$this->db->select(array('voucher_header.hID as voucher_id','voucher_header.icpNo as fcp_number','voucher_header.VNumber as voucher_number','voucher_header.TDate as transaction_date'));
		$this->db->select_sum('Cost');
		$this->db->join('projectsdetails','projectsdetails.icpNo=voucher_header.icpNo');
		$this->db->join('clusters','clusters.clusters_id=projectsdetails.cluster_id');
		$this->db->join('voucher_body','voucher_body.hID=voucher_header.hID');
		$this->db->join('accounts','accounts.AccNo=voucher_body.AccNo');
		$this->db->join('support_mode','support_mode.support_mode_id=voucher_body.fk_support_mode_id');
		$this->db->where(array('clusterName'=>$cluster_name));
		$this->db->where(array('support_mode_name'=>$support_mode_name));
		$this->db->where(array('accounts.AccText'=>$account_code));
		$this->db->where(array('Cost > '=> 0));

		$this->db->group_by(array('clusterName','voucher_header.VNumber','support_mode_name','accounts.AccText'));

		$vouchers = $this->db->get('voucher_header')->result_array();

		$page_data['page_name']  = __FUNCTION__;
		$page_data['page_title'] = "Voucher List";
		//$page_data['account_type'] = "admin";
		$page_data['vouchers']  = $vouchers;
		
		$this->load->view('backend/index', $page_data);	
		
	}
    
    /***ADMIN DASHBOARD***/
    function external_links($param1="",$param2="")
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url().'resources.php','refresh');
			
			if($param1=="add"){
				$data = $this->input->post();
				
				array_pop($data);
								
				$this->db->insert('external_links',$data);
				
				$link_id = $this->db->insert_id();
				
				$userlevels = $this->input->post('userlevels');
				
				for($i=0;$i<sizeof($userlevels);$i++){
					$data2['external_links_id'] = $link_id;
					$data2['userlevel'] = $userlevels[$i];
					$data2['status'] = '1';
					
					$this->db->insert('links_group_access',$data2);
				}
				//break;
				$this->session->set_flashdata('flash_message' , get_phrase('link_added')); 
            	redirect(base_url() . 'resources.php/admin/external_links/', 'refresh'); 
			}
			
			if($param1=="edit"){
				$data = $this->input->post();
				
				array_pop($data);
				
				$this->db->update('external_links',$data,array('external_links_id'=>$param2));
				
				$userlevels = $this->input->post('userlevels');
				
				for($i=0;$i<sizeof($userlevels);$i++){
					$data2['external_links_id'] = $param2;
					$data2['userlevel'] = $userlevels[$i];
					$data2['status'] = '1';
					if($this->db->get_where('links_group_access',array('external_links_id'=>$param2,'userlevel'=>$userlevels[$i]))->num_rows() === 0){
						$this->db->insert('links_group_access',$data2);
					}else{
						$this->db->update('links_group_access',$data2,array('external_links_id'=>$param2,'userlevel'=>$userlevels[$i]));
					}

				}
				//break;
				$this->session->set_flashdata('flash_message' , get_phrase('link_editted')); 
            	redirect(base_url() . 'resources.php/admin/external_links/', 'refresh'); 
			}
						
        $page_data['page_name']  = __FUNCTION__;
        $page_data['page_title'] = get_phrase('external_links');
        $this->load->view('backend/index', $page_data);
    }
	
	function toggle_status($param1=""){
		 $link = explode('_', $param1);
		 
		 $link_id = $link[0];
		 
		 $userlevel = $link[1];
		 
		 $status = $this->db->get_where('links_group_access',array('external_links_id'=>$link_id,'userlevel'=>$userlevel))->row()->status;
		 
		 $this->db->where(array('external_links_id'=>$link_id,'userlevel'=>$userlevel));
		 
		 if($status==='0'){
		 	$this->db->update('links_group_access',array('status'=>'1'));
		 }elseif($status==='1'){
		 	$this->db->update('links_group_access',array('status'=>'0'));
		 }	
	}
	
	  function documents($param1="",$param2="")
    	{
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url().'resources.php','refresh');
			
			if($param1==='add'){
				//if($this->input->post('fileSubmit') && !empty($_FILES['userFiles']['name'])){
					
								$fld = str_replace(" ", "_", $this->input->post('title'));
	        	
								$uploadPath = 'uploads/document/resources/';
								//Check if group folder exists
								if(!file_exists($uploadPath."/".$fld)){
									mkdir($uploadPath."/".$fld);
								}
								
		        	 $filesCount = count($_FILES['userFiles']['name']);
	            		for($i = 0; $i < $filesCount; $i++){
	            			    $_FILES['userFile']['name'] = $_FILES['userFiles']['name'][$i];
				                $_FILES['userFile']['type'] = $_FILES['userFiles']['type'][$i];
				                $_FILES['userFile']['tmp_name'] = $_FILES['userFiles']['tmp_name'][$i];
				                $_FILES['userFile']['error'] = $_FILES['userFiles']['error'][$i];
				                $_FILES['userFile']['size'] = $_FILES['userFiles']['size'][$i];
								
								$fullUploadPath = $uploadPath."/".$fld;
				                
				                $config['upload_path'] = $fullUploadPath;
				                $config['allowed_types'] = 'gif|jpg|png|pdf|doc|docx|xls|xlsx';
								
								$this->load->library('upload', $config);
				                $this->upload->initialize($config);
				                if($this->upload->do_upload('userFile')){
				                    $fileData = $this->upload->data();
				                    $uploadData['title'] = $fld;
				                    $uploadData['status'] = $this->input->post('status');	
									$uploadData['description'] = $this->input->post('description');				
				                    $uploadData['timestamp'] = date("Y-m-d H:i:s");
				                    $uploadData['owner'] = $this->input->post('owner');	
				                }
						}	
	
					if(!empty($uploadData)){
		                //Insert file information into the database
		                $insert = $this->db->insert('document',$uploadData);
		                $statusMsg = $insert?'Document uploaded successfully.':'Some problem occurred, please try again.';
		                $this->session->set_flashdata('flash_message',$statusMsg);
						//break;
						redirect(base_url() . 'resources.php/admin/documents/', 'refresh');
						
		            }
				//}
			}
		
		    $page_data['page_name']  = __FUNCTION__;
        	$page_data['page_title'] = get_phrase('documents');
        	$this->load->view('backend/index', $page_data);
		}

		function ziparchive($param1="",$param2="",$param3=""){
		
			$fld = $this->db->get_where('document',array('document_id'=>$param1))->row()->title;
			
			$path = 'uploads/document/resources/'.$fld;
			
			if ($handle = opendir($path)) {
			    while (false !== ($file = readdir($handle))) {
			        if ('.' === $file) continue;
			        if ('..' === $file) continue;
			
			        // do something with the file
			        $data = file_get_contents($file);
				
					$this->zip->add_data($file, $data);
			    }
			    closedir($handle);
			}
			
			//foreach ($files as $file) {

				//if (is_file($filename)) {
					//$path = 'uploads/document/medical/'.$param1.'/'.$param2.'/'.$file->file_name;
					
					//$data = file_get_contents($path);
				
					//$this->zip->add_data($file->file_name, $data);
				//}				    
								    
			//}
			
			// Write the zip file to a folder on your server. Name it "my_backup.zip"
			$this->zip->archive('downloads/my_backup.zip');
			
			// Download the file to your desktop. Name it "my_backup.zip"
			
			$backup_file = 'downloads_'.date("Y_m_d_H_i_s").'.zip'; 
			
			$this->zip->download($backup_file);
			
			unlink('downloads/'.$backup_file);
			
			//if($param1==='claims'){
				//$this->add_claim_rct($param2);
			//}else{
				//$this->add_claim_docs($param2);
			//}
		
	}
	
	/**function track_clicks($param1=""){
		$data['user_id'] = $this->session->login_user_id;
		$data['link_id'] = $param1;
		$data['visit_count'] = '1';
		
		if($this->db->get_where('visited_notifications',array('link_id'=>$param1,'user_id'=>$this->session->login_user_id))->num_rows()>0){
			$cnt = $this->db->get_where('visited_notifications',array('link_id'=>$param1,'user_id'=>$this->session->login_user_id))->row()->visit_count + 1; 
			$data['visit_count'] = $cnt;
			$this->db->where(array('link_id'=>$param1,'user_id'=>$this->session->login_user_id));	
			$this->db->update('visited_notifications',$data);
		}else{
			$this->db->insert('visited_notifications',$data);
		}
		
		
	}**/
}
