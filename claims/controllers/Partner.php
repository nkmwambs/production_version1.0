<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*	
 *	@author 	: Nicodemus Karisa
 *	date		: 27 April, 2017
 */

class Partner extends CI_Controller
{
    
    
	function __construct()
	{
		parent::__construct();
		$this->load->database();
        $this->load->library('session');
		$this->load->model('file');		
		$this->load->library('zip');
		$this->load->model('medical_model','medical');
		
       /*cache control*/
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
		
    }
	
	/***default functin, redirects to login page if no admin logged in yet***/
   public function index()
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'claims.php/login', 'refresh');
		
    }

    function dashboard()
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url().'admin.php','refresh');
			
        $page_data['page_name']  = 'dashboard';
        $page_data['page_title'] = get_phrase('claims_dashboard');
        $this->load->view('backend/index', $page_data);
    }
	
	public function ajax_list()
	{
		$list = $this->medical->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $claims) {
			
			$row = array();
			
			$row[] = "";//"<input class='chkbox' type='checkbox' id='".$claims->rec."'/> <i class='fa fa-plus'></i>";//0-view
			$row[] = $claims->incidentID;//1-view
			$row[] = $claims->proNo;//2-view
			$row[] = $claims->cluster;//3-view
			$row[] = "<input class='chkbox' type='checkbox' id='".$claims->rec."'/> ".$claims->childNo;//4-view
			$row[] = $claims->childName;//5
			$row[] = $claims->treatDate;//6-view
			$row[] = $claims->date;//7
			$row[] = $claims->diagnosis;//8
			$row[] = $claims->totAmt;//9
			$row[] = $claims->careContr;//10
			$row[] = $claims->nhif;//11
			$row[] = number_format($claims->amtReim,2);//12-view
			$row[] = $claims->facName;//13
			$row[] = ucfirst($claims->facClass);//14-view
			$row[] = $claims->type;//15
			$row[] = $claims->vnum;//16
			
			$rct = "<a href='".base_url()."claims.php/partner/add_claim_rct/".$claims->rec."' class='btn btn-green btn-icon'><i class='fa fa-cloud-download'></i>".get_phrase('download')."</a>";
			
			if(count($this->db->get_where('apps_files',array('file_group'=>$claims->rec,'upload_type'=>"receipt"))->result_object())=== 0){
				$rct =  "<a href='".base_url()."claims.php/partner/add_claim_rct/".$claims->rec."' class='btn btn-orange btn-icon'><i class='fa fa-cloud-upload'></i>".get_phrase('attach_receipt')."</a>";
			}
			
			$row[] = $rct;//17-view
			
			$refNo = "<a href='".base_url()."claims.php/partner/add_claim_docs/".$claims->rec."' class='btn btn-green btn-icon'><i class='fa fa-cloud-download'></i>".get_phrase('download')."</a>";
			
			if(count($this->db->get_where('apps_files',array('file_group'=>$claims->rec,'upload_type'=>"approval"))->result_object())=== 0){
				$refNo =  "<a href='".base_url()."claims.php/partner/add_claim_docs/".$claims->rec."' class='btn btn-orange btn-icon'><i class='fa fa-cloud-upload'></i>".get_phrase('attach_approval')."</a>";
			}						
			
			$row[] = $refNo;//18-view
			
			
			$rmk = "";
			
			switch($claims->rmks):
						
				case "-1":
					$rmk = get_phrase("new");
					break;
				case "0":
					$rmk =  get_phrase("submitted");
					break;
				case "1":
					$rmk =   get_phrase("declined_by_PF");
					break;
				case "2":
					$rmk =   get_phrase("checked_by_PF");
					$rmk2 = get_phrase("declined_by_PF");
					break;
				case "3":
					$rmk =   get_phrase("declined_by_HS");
					break;
				case "4":
					$rmk =   get_phrase("approved_by_HS");
					$rmk2 = get_phrase("declined_by_HS");
					break;	
				case "10":
					$rmk =   get_phrase("archived");
					//$rmk2 = get_phrase("declined_by_HS");
					break;					
				default:
					$rmk =  get_phrase("paid");		
						
				endswitch;
						
				if($claims->reinstatementdate!= NULL){
					$former_status_array = array("1"=>get_phrase("PF_decline"),"3"=>get_phrase("HS_decline"),"10"=>get_phrase("archive"));
					$rmk = get_phrase('reinstated_after_')." ".$former_status_array[$claims->rmks];
				}
			$action_data['rec'] = $claims->rec ;
			$action_data['name'] = $rmk ;
			
			$row[] = $this->load->view("backend/partner/medical_action",$action_data,TRUE);//19
			
			$row[] = $claims->stmp;//20
			//$row[] = $claims->incident_type;//21
			//$row[] = $claims->incident_sub_category_id;//ucfirst($this->db->get_where('illness_sub_category',array('illness_sub_category_id'=>$claims->incident_sub_category_id))->row()->name);//$claims->incident_category;//22
			//$row[] = $claims->illness_id;//23
			$row[] = $claims->incident_type;
			$row[] = $claims->incident_sub_category_id;
			$row[] = $claims->illness_id;
			$row[] = $rmk;
			
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						//"recordsTotal" => $this->medical->count_all(),
						"recordsTotal" => $this->medical->count_filtered(),
						"recordsFiltered" => $this->medical->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	
	/**
	 * Medical Claim Functions
	 */
	 function medical_claims($param1="",$param2="")
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
		
		$icp_id = $this->session->userdata('center_id');
		
		$cluster = $this->session->userdata('cluster');
		
		if($param1==="add"){
			
			//Compute claim count
			
			$current_count = $this->db->get_where('claims',array('childNo'=>$this->input->post('childNo')))->num_rows();
			
			$data['incidentID'] = $this->input->post('incidentID');
			$data['claimCnt'] = $current_count+1;
			$data['proNo'] = $this->input->post('proNo');
			$data['cluster'] = $this->input->post('cluster');
			$data['childNo'] = $this->input->post('childNo');
			$data['childName'] = $this->input->post('childName');
			$data['treatDate'] = $this->input->post('treatDate');
			//$data['incident_type'] = "0";//$this->input->post('incident_type');
			//$data['incident_sub_category_id'] = "0";//$this->input->post('incident_sub_category_id');
			//$data['illness_id'] = "0";//$this->input->post('illness_id');
			$data['diagnosis'] = $this->input->post('diagnosis');
			$data['totAmt'] = $this->input->post('totAmt');
			$data['careContr'] = $this->input->post('careContr');
			$data['nhif'] = $this->input->post('nhif');
			$data['amtReim'] = $this->input->post('amtReim');
			$data['facName'] = $this->input->post('facName');
			$data['facClass'] = $this->input->post('facClass');
			$data['date'] = date('Y-m-d');
			$data['vnum'] = $this->input->post('vnum');
			$data['rmks'] = '-1';
			$data['randomID'] = rand(10000, 99000);
			
			$this->db->insert('claims',$data);
			
			$rec= $this->db->insert_id();
			
			//Upload receipt and Approval Docs
			if(!empty($_FILES['receipt']['name'])) $this->uploads($rec,"receipt");
			if(!empty($_FILES['approval']['name'])) $this->uploads($rec,"approval");
			
        	redirect(base_url().'claims.php/partner/medical_claims', 'refresh');

		}

		if($param1==='edit'){
			$data['incidentID'] = $this->input->post('incidentID');
			//$data['claimCnt'] = $current_count+1;
			$data['proNo'] = $this->input->post('proNo');
			$data['cluster'] = $this->input->post('cluster');
			$data['childNo'] = $this->input->post('childNo');
			$data['childName'] = $this->input->post('childName');
			$data['treatDate'] = $this->input->post('treatDate');
			$data['diagnosis'] = $this->input->post('diagnosis');
			$data['totAmt'] = $this->input->post('totAmt');
			$data['careContr'] = $this->input->post('careContr');
			$data['nhif'] = $this->input->post('nhif');
			$data['amtReim'] = $this->input->post('amtReim');
			$data['facName'] = $this->input->post('facName');
			$data['facClass'] = $this->input->post('facClass');
			$data['date'] = date('Y-m-d');
			$data['vnum'] = $this->input->post('vnum');
			//$data['rmks'] = -1;
			//$data['randomID'] = rand(10000, 99000);
			
			$this->db->where(array('rec'=>$param2));
			
			$this->db->update("claims",$data);
			
			//if($this->db->affected_rows()>0){
				
				$statusMsg = "Claim updated successfully";
			
				$this->session->set_flashdata('flash_message',$statusMsg);
			
				redirect(base_url().'claims.php/partner/add_claim_rct/'.$param2, 'refresh');	
			//}
			
		}
		
		switch($this->session->userdata('logged_user_level')):
			case '1':
				$this->db->where(array("proNo"=>$this->session->userdata('center_id'),'rmks'=>'-1'));
				break;				
			case '2':
				$this->db->where(array("cluster"=>$this->session->userdata('cluster'),'rmks'=>0));
				break;
			case '5':
				$this->db->where(array("rmks"=>2));
		endswitch;
		
		 
		$claims = $this->db->get('claims')->result_object(); 
		
		$page_data['center'] = $icp_id;
		$page_data['cluster'] = $cluster;
		$page_data['claims'] = $claims;
        $page_data['page_name']  = 'medical_claims';
        $page_data['page_title'] = get_phrase('list_claims');
        $this->load->view('backend/index', $page_data);
    }	

	function add_claim(){
		
		//Compute claim count
			
			$current_count = $this->db->get_where('claims',array('childNo'=>$this->input->post('childNo')))->num_rows();
			
			$data['incidentID'] = $this->input->post('incidentID');
			$data['claimCnt'] = $current_count+1;
			$data['proNo'] = $this->input->post('proNo');
			$data['cluster'] = $this->input->post('cluster');
			$data['childNo'] = $this->input->post('childNo');
			$data['childName'] = $this->input->post('childName');
			$data['treatDate'] = $this->input->post('treatDate');
			$data['diagnosis'] = $this->input->post('diagnosis');
			$data['totAmt'] = $this->input->post('totAmt');
			$data['careContr'] = $this->input->post('careContr');
			$data['nhif'] = $this->input->post('nhif');
			$data['amtReim'] = $this->input->post('amtReim');
			$data['facName'] = $this->input->post('facName');
			$data['facClass'] = $this->input->post('facClass');
			$data['date'] = date('Y-m-d');
			$data['vnum'] = $this->input->post('vnum');
			$data['rmks'] = '-1';
			$data['randomID'] = rand(10000, 99000);
			
			$this->db->insert('claims',$data);
			
			$rec= $this->db->insert_id();
			
			//echo $rec;
			//Upload receipt and Approval Docs
	
			if(!empty($_FILES['receipt']['name'])) $this->uploads($rec,"receipt");
			if(!empty($_FILES['approval']['name'])) $this->uploads($rec,"approval");
			
			//echo '<div class="well" style="text-align:center;">'.$msg.'</div>';
			echo $rec;
	}


	function uploads($claim_id="",$file_type=""){
		
		//if($this->input->post('submit') && !(empty($_FILES['receipt']['name']) || empty($_FILES['approval']['name'])) ){
			
		if(!empty($_FILES[$file_type]['name'])){
				
	        	if($file_type === 'receipt')  $param1 = 'claims'; else $param1 = 'supportdocs'; 
				  
							$uploadPath = 'uploads/document/medical/'.$param1;
							//Check if group folder exists
							if(!file_exists($uploadPath."/".$claim_id)){
								mkdir($uploadPath."/".$claim_id);
							}else{
								foreach (glob($uploadPath."/".$claim_id."/*.*") as $filename) {
								    if (is_file($filename)) {
								        unlink($filename);
								    }
								}
								
								$upload_type = "receipt";
								
								if($param1==="supportdocs"){
									$upload_type = "approval";
								}
								
								$this->db->where(array("file_group"=>$claim_id,"upload_type"=>$upload_type));
								$this->db->delete('apps_files');
							}
							
	        	 $filesCount = count($_FILES[$file_type]['name']);
            		for($i = 0; $i < $filesCount; $i++){
            			    $_FILES[$file_type]['name'] = $_FILES[$file_type]['name'][$i];
			                $_FILES[$file_type]['type'] = $_FILES[$file_type]['type'][$i];
			                $_FILES[$file_type]['tmp_name'] = $_FILES[$file_type]['tmp_name'][$i];
			                $_FILES[$file_type]['error'] = $_FILES[$file_type]['error'][$i];
			                $_FILES[$file_type]['size'] = $_FILES[$file_type]['size'][$i];
							
							$fullUploadPath = $uploadPath."/".$claim_id;
			                
			                $config['upload_path'] = $fullUploadPath;
			                $config['allowed_types'] = 'gif|jpg|png|pdf|doc|docx|xls|xlsx';
							
							$this->load->library('upload', $config);
			                $this->upload->initialize($config);
			                if($this->upload->do_upload($file_type)){
			                    $fileData = $this->upload->data();
			                    $uploadData[$i]['file_name'] = $fileData['file_name'];
			                    $uploadData[$i]['file_group'] = $claim_id;
								
								 if($param1==="claims"){
								 	$uploadData[$i]['upload_type'] = 'receipt';	
								 }else{
								 	$uploadData[$i]['upload_type'] = 'approval';
								 }
			                    $uploadData[$i]['created'] = date("Y-m-d H:i:s");
			                    //$uploadData[$i]['modified'] = date("Y-m-d H:i:s");
			                }
					}	

				if(!empty($uploadData)){
	                //Insert file information into the database
	                $insert = $this->file->insert($uploadData);
	                $statusMsg = $insert?'Receipt uploaded successfully.':'Some problem occurred, please try again.';
					//return $statusMsg;
					
	            }
			}
		
	}

	function add_claim_docs($param1=""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
            
		$claim = $this->db->get_where('claims',array('rec'=>$param1))->row();                         

		$page_data['files'] = $this->file->getRows($param1,'approval');  
		$page_data['claim'] = $claim;
        $page_data['page_name']  = 'upload_claim_docs';
        $page_data['page_title'] = get_phrase('approval_attachments');
        $this->load->view('backend/index', $page_data);            		
	}

	function add_claim_rct($param1=""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

       	//$claim = $this->db->get_where('app_medical_claims',array('rec'=>$param1))->row();                         
			  
		$claim = $this->db->get_where('claims',array('rec'=>$param1))->row();                         
			
		$page_data['files'] = $this->file->getRows($param1,'receipt');  			
		$page_data['claim'] = $claim;
        $page_data['page_name']  = 'upload_claim_rct';
        $page_data['page_title'] = get_phrase('receipts_attachments');
        $this->load->view('backend/index', $page_data);            		
	}		
	

	function new_medical_claim($param1="",$param2="")
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

		$icp_id = $this->session->userdata('center_id');
		
		$cluster = $this->session->userdata('cluster');

		$page_data['center'] = $icp_id;
		$page_data['cluster'] = $cluster;
		$page_data['page_name']  = 'new_medical_claim';
        $page_data['page_title'] = get_phrase('new_medical_claim');
        $this->load->view('backend/index', $page_data);		
	}
	function get_claimed_amount($param1=""){
		$claimed = 0;
		
		$total_in_voucher = 0;
		
		if($this->db->get_where('claims',array('vnum'=>$param1,'proNo'=>$this->session->center_id))->num_rows() > 0){
			$claimed = $this->db->select_sum('amtReim')->get_where('claims',array('vnum'=>$param1,'proNo'=>$this->session->center_id))->row()->amtReim;
		}
		if($this->db->get_where('voucher_body',array('VNumber'=>$param1,'icpNo'=>$this->session->center_id))->num_rows() > 0){
			$total_in_voucher = $this->db->select_sum('Cost')->get_where('voucher_body',array('VNumber'=>$param1,'AccNo'=>'1320','icpNo'=>$this->session->center_id))->row()->Cost;
		}

		$to_be_claimed = $total_in_voucher - $claimed;
		
		echo $to_be_claimed;
	}
	function upload_medical_receipts($param1=""){

	        if($this->input->post('fileSubmit') && !empty($_FILES['userFiles']['name'])){
	        	
							$uploadPath = 'uploads/document/medical/'.$param1;
							//Check if group folder exists
							if(!file_exists($uploadPath."/".$this->input->post('claim_id'))){
								mkdir($uploadPath."/".$this->input->post('claim_id'));
							}else{
								foreach (glob($uploadPath."/".$this->input->post('claim_id')."/*.*") as $filename) {
								    if (is_file($filename)) {
								        unlink($filename);
								    }
								}
								
								$upload_type = "receipt";
								
								if($param1==="supportdocs"){
									$upload_type = "approval";
								}
								
								$this->db->where(array("file_group"=>$this->input->post('claim_id'),"upload_type"=>$upload_type));
								$this->db->delete('apps_files');
							}
							
	        	 $filesCount = count($_FILES['userFiles']['name']);
            		for($i = 0; $i < $filesCount; $i++){
            			    $_FILES['userFile']['name'] = $_FILES['userFiles']['name'][$i];
			                $_FILES['userFile']['type'] = $_FILES['userFiles']['type'][$i];
			                $_FILES['userFile']['tmp_name'] = $_FILES['userFiles']['tmp_name'][$i];
			                $_FILES['userFile']['error'] = $_FILES['userFiles']['error'][$i];
			                $_FILES['userFile']['size'] = $_FILES['userFiles']['size'][$i];
							
							$fullUploadPath = $uploadPath."/".$this->input->post('claim_id');
			                
			                $config['upload_path'] = $fullUploadPath;
			                $config['allowed_types'] = 'gif|jpg|png|pdf|doc|docx|xls|xlsx';
							
							$this->load->library('upload', $config);
			                $this->upload->initialize($config);
			                if($this->upload->do_upload('userFile')){
			                    $fileData = $this->upload->data();
			                    $uploadData[$i]['file_name'] = $fileData['file_name'];
			                    $uploadData[$i]['file_group'] = $this->input->post('claim_id');
								
								 if($param1==="claims"){
								 	$uploadData[$i]['upload_type'] = 'receipt';	
								 }else{
								 	$uploadData[$i]['upload_type'] = 'approval';
								 }
			                    $uploadData[$i]['created'] = date("Y-m-d H:i:s");
			                    //$uploadData[$i]['modified'] = date("Y-m-d H:i:s");
			                }
					}	

				if(!empty($uploadData)){
	                //Insert file information into the database
	                $insert = $this->file->insert($uploadData);
	                $statusMsg = $insert?'Receipt uploaded successfully.':'Some problem occurred, please try again.';
	                $this->session->set_flashdata('flash_message',$statusMsg);
					
	            }
			}
                        
			
			if($param1==='claims'){

				$claims_arr = $this->db->get_where('claims',array('rec'=>$this->input->post('claim_id')))->row();
				
				//Check if Facility is not Public
				$facClass = $claims_arr->facClass;
				$public = "yes";
				if($facClass!=="public"){
					$public = "no";
				}
				
				//Check if approval documents have not been attached
				$supportdocs = "no";
				if(file_exists('uploads/document/medical/supportdocs/'.$this->input->post('claim_id'))){
					$supportdocs = "yes";
				}
				
				//Check amount to be spent
				$totAmt = $this->db->get_where('claims',array('rec'=>$this->input->post('claim_id')))->row()->totAmt;
				
				
				if($public === "no" && $supportdocs ==="no" && $totAmt>=10000){
					
					$this->add_claim_docs($this->input->post('claim_id'));
				}else{
					$this->add_claim_rct($this->input->post('claim_id'));
				}
				
				
			}else{
				$this->add_claim_docs($this->input->post('claim_id'));
			}
			
			
	}


	function download($param1="",$param2=""){
		
		$path_arr = $this->db->get_where('apps_files',array('id'=>$param2))->row();
		
		force_download('uploads/document/medical/'.$param1.'/'.$path_arr->file_group.'/'.$path_arr->file_name, NULL);
		
		if($param1==='claims'){
			$this->add_claim_rct($param2);
		}else{
			$this->add_claim_docs($param2);
		}
	}

	function ziparchive($param1="",$param2="",$param3=""){
		
			$files = $this->db->get_where('apps_files',array('file_group'=>$param2,'upload_type'=>$param3))->result_object();
			
			
			foreach ($files as $file) {

				//if (is_file($filename)) {
					$path = 'uploads/document/medical/'.$param1.'/'.$param2.'/'.$file->file_name;
					
					$data = file_get_contents($path);
				
					$this->zip->add_data($file->file_name, $data);
				//}				    
								    
			}
			
			// Write the zip file to a folder on your server. Name it "my_backup.zip"
			$this->zip->archive('downloads/my_backup.zip');
			
			// Download the file to your desktop. Name it "my_backup.zip"
			
			$backup_file = 'downloads_'.date("Y_m_d_H_i_s").'.zip'; 
			
			$this->zip->download($backup_file);
			
			unlink('downloads/'.$backup_file);
			
			if($param1==='claims'){
				$this->add_claim_rct($param2);
			}else{
				$this->add_claim_docs($param2);
			}
		
	}	
	
	function check_receipt($rec_id=""){
		$rct = "no";
		if(file_exists('uploads/document/medical/claims/'.$rec_id)){
				$rct="yes";
		}
		
		return $rct;
	}

	function check_public_facility_type($rec_id=""){
		$claim_arr = $this->db->get_where('claims',array('rec'=>$rec_id))->row();
			
			//$public = "yes";
			
		// if(ucfirst($claim_arr->facClass)!=='Public'){
				// $public = "no";
		// }
		
		return $claim_arr->facClass;
	}
	
	function check_approval_documents($rec_id=""){
		$supportdocs = "no";
			if(file_exists('uploads/document/medical/supportdocs/'.$rec_id)){
				$supportdocs="yes";
			}
			
		return $supportdocs;
	}
	
	function check_total_amount($rec_id=""){
		$totAmt = $this->db->get_where('claims',array('rec'=>$rec_id))->row()->totAmt;
		
		return $totAmt;
	}
	
	function check_submitted($rec_id=""){
		
		$status = "yes";
		
		$submited = $this->db->get_where('claims',array('rec'=>$rec_id))->row()->rmks;
		
		if($submited==='-1'){
			$status = "no";	
		}
		
		return $status;		
	}
	
	function mass_submit_claims(){
		
		$cnt = 0;
		
		$cnt_processed=0;
		
		foreach($this->input->post('claim') as $rec_id):
			
			//Check if receipts are uploaded before submitting
			$rct = $this->check_receipt($rec_id);
		
			//Check if claim is for pubic hospital
			$public = $this->check_public_facility_type($rec_id);	
			
			//Check if approval document been attached
			$supportdocs = $this->check_approval_documents($rec_id);			
			
			//Check amount to be spent
			$totAmt = $this->check_total_amount($rec_id);
			
			//Check claim submitted
			$status = $this->check_submitted($rec_id);
				
			
			if($status === "no" && $rct==="yes" && ($public ==="public" || ($public!=="public" && $supportdocs === "yes"))){
				$this->db->where("rec",$rec_id);
			
				$data = array("rmks"=>"0");
				
				$this->db->update("claims",$data);
				
				++$cnt_processed;	
			}
			
			++$cnt;
			
		endforeach;
		
		$statusMsg = $cnt_processed." out of ".$cnt." claims submitted\n";
		
		if($cnt_processed===0){
			$statusMsg .= "All claims should have receipts and Claims from none public facilities require an approval document. Use reinstate for submit declined claims";
		}
		
		//$this->session->set_flashdata('flash_message',$statusMsg);	
		echo $statusMsg;	
	}
	
	function single_submit($rec_id="",$ajax=""){
			//Check if receipts are uploaded before submitting
			$rct = $this->check_receipt($rec_id);
		
			//Check if claim is for pubic hospital
			$public = $this->check_public_facility_type($rec_id);	
			
			//Check if approval document been attached
			$supportdocs = $this->check_approval_documents($rec_id);			
			
			//Check amount to be spent
			$totAmt = $this->check_total_amount($rec_id);
			
			//Check submitted
			$status = $this->check_submitted($rec_id); 
			
			$cnt_processed = 0;	
			
			if($status === "no" && $rct==="yes" && ($public ==="public" || ($public!=="public" && $supportdocs === "yes"))){
				$this->db->where("rec",$rec_id);
			
				$data = array("rmks"=>"0");
				
				$this->db->update("claims",$data);
				
				$this->email_model->new_claim_nofication($rec_id);
				
				++$cnt_processed;	
			}
			
			$statusMsg = "Claim submitted Successfully";		
			
			if($cnt_processed===0){
				$statusMsg = "Claim not submitted. A claim should have receipts and if from a none public facility requires an approval document. Use reinstate for submit declined claims";
			}
			
			
			$this->session->set_flashdata('flash_message',$statusMsg);
			if($ajax==="ajax") echo $statusMsg; else redirect(base_url().'claims.php/partner/medical_claims','refresh');
	}

	function process_claim($rec_id=""){
		
		$chk = $this->db->get_where('claims',array('rec'=>$rec_id))->row()->rmks;
		
		$reinstatedate = $this->db->get_where('claims',array('rec'=>$rec_id))->row()->reinstatementdate;
		
		$reinstated = "no";
	
		if($reinstatedate!=="0000-00-00"){
			$reinstated = "yes";
		}
		
		$updated_count = 0;
		
		$statusMsg = "";
		
		if($chk==="0"){
				
			$this->db->where(array('rec'=>$rec_id));
			$data=array("rmks"=>2);
			$this->db->update('app_medical_claims',$data);
			$updated_count = $this->db->affected_rows();
			$statusMsg = "Claim checked successfully";
			
		}elseif($chk==="1" && $reinstated ==="yes"){
		
			$this->db->where(array('rec'=>$rec_id));
			$data=array("rmks"=>2,"reinstatementdate"=>"0000-00-00");
			$this->db->update('app_medical_claims',$data);
			$updated_count = $this->db->affected_rows();
			$statusMsg = "Reinstated claim checked successfully";			
		
		}elseif($chk==="2"){
			
			$this->db->where(array('rec'=>$rec_id));
			$data=array("rmks"=>4);
			$this->db->update('app_medical_claims',$data);
			$updated_count = $this->db->affected_rows();
			$statusMsg = "Claim processed successfully";
			
		}elseif($chk==="3" && $reinstated ==="yes"){
			
			$this->db->where(array('rec'=>$rec_id));
			$data=array("rmks"=>4,"reinstatementdate"=>"0000-00-00");
			$this->db->update('app_medical_claims',$data);
			$updated_count = $this->db->affected_rows();
			$statusMsg = "Reinstated claim processed successfully";							
						
		}elseif($chk==="4"){
				
			$this->db->where(array('rec'=>$rec_id));
			$data=array("rmks"=>7);
			$this->db->update('app_medical_claims',$data);
			$updated_count = $this->db->affected_rows();
			$statusMsg = "Claim paid successfully";
		}	
			
		if($updated_count===0){
			$statusMsg = "Claim could not be processed";
		}
			
		$this->session->set_flashdata('flash_message',$statusMsg);
		
		$this->medical_claims();
	}
	
	function delete_claim($rec_id=""){
			
		$chk = $this->db->get_where('claims',array('rec'=>$rec_id))->row()->rmks;	
		
		$statusMsg = "Claim deleted successfully";
		
		$updated_count = 0;
		
		if($chk==="-1"){
			$this->db->where(array('rec'=>$rec_id));
			$this->db->delete("claims");
			$updated_count = $this->db->affected_rows();
		}elseif($chk==='1' || $chk === '3'){
			$this->db->where(array('rec'=>$rec_id));
			$data=array('rmks'=>10);
			$this->db->update('claims',$data); 
			$updated_count = $this->db->affected_rows();
			$statusMsg = "Claim archived successfully";
		}
		
		if($updated_count===0){
			$statusMsg = "Claim could not be deleted";
		}
			
		$this->session->set_flashdata('flash_message',$statusMsg);
		
		redirect(base_url().'claims.php/partner/medical_claims','refresh');
	}
	
	function decline_claim($rec_id=""){
		
		$chk = $this->db->get_where('app_medical_claims',array('rec'=>$rec_id))->row()->rmks;	
		
		$reinstatementdate = $this->db->get_where('app_medical_claims',array('rec'=>$rec_id))->row()->reinstatementdate;
		
		$statusMsg = "Claim could not be declined <br>";
		
		$updated_count = "";
		
		//if($chk==="0"){
			
			//$this->db->where(array('rec'=>$rec_id));
			//$dt = array('rmks'=>1,"reinstatementdate"=>"0000-00-00");
			//$this->db->update("app_medical_claims",$dt);
						
						
			//$data['comment'] = $this->input->post('comment');
			//$data['user_id'] = $this->session->login_user_id;
			//$data['rec_id'] = $rec_id;
			//$data['app_name'] = 'medical';			
		
			//$this->db->insert("apps_decline_comments",$data);
			
			//$statusMsg = "Comment posted successfully";
			
		//}else
		if($chk==="0"){

			$this->db->where(array('rec'=>$rec_id));
			$dt = array('rmks'=>1);
			$this->db->update("app_medical_claims",$dt);
			
						
			$data['comment'] = $this->input->post('comment');
			$data['user_id'] = $this->session->login_user_id;
			$data['rec_id'] = $rec_id;
			$data['app_name'] = 'medical';			
		
			$this->db->insert("apps_decline_comments",$data);
			
			$statusMsg = "Comment posted successfully";
		}elseif($chk==="2"){

			$this->db->where(array('rec'=>$rec_id));
			$dt = array('rmks'=>3);
			$this->db->update("app_medical_claims",$dt);
			
						
			$data['comment'] = $this->input->post('comment');
			$data['user_id'] = $this->session->login_user_id;
			$data['rec_id'] = $rec_id;
			$data['app_name'] = 'medical';			
		
			$this->db->insert("apps_decline_comments",$data);
			
			$statusMsg = "Comment posted successfully";
		}elseif($reinstatementdate!=="0000-00-00" && ($chk==="1" || $chk==="3")){
				$this->db->where(array('rec'=>$rec_id));
				$dt = array('reinstatementdate'=>"0000-00-00");
				$this->db->update("app_medical_claims",$dt);
				
							
				$data['comment'] = $this->input->post('comment');
				$data['user_id'] = $this->session->login_user_id;
				$data['rec_id'] = $rec_id;
				$data['app_name'] = 'medical';			
			
				$this->db->insert("apps_decline_comments",$data);			
		}
		
		$this->session->set_flashdata('flash_message',$statusMsg);		
		
		
		$this->medical_claims();
	}
	
	function edit_medical_claim($rec_id=""){
		
		$chk = $this->db->get_where('claims',array('rec'=>$rec_id))->row()->rmks;

		if($chk==="-1" || $chk === "1" || $chk ==="3" || $chk ==="10"){
			$page_data['rec_id'] = $rec_id;
			$page_data['claim'] = $this->db->get_where("claims",array("rec"=>$rec_id))->row();
			$page_data['page_name']  = 'edit_medical_claim';
	        $page_data['page_title'] = get_phrase('edit_medical_claim');
			$this->load->view('backend/index', $page_data);		
		}else{
			
			$statusMsg = "Claim cannot be editted";
			
			$this->session->set_flashdata('flash_message',$statusMsg);
			
			$this->medical_claims();	
		}
		        	
	}
	
	function add_claim_comments($rec_id=""){

		$chk = $this->db->get_where('claims',array('rec'=>$rec_id))->row()->rmks;
		
		//$statusMsg = "Comment can't be added";	
		
			//if($chk === "-1" || $chk === "1" || $chk ==="3"){
				$data['rson'] = $this->input->post('rson');
				$data['userid'] = $this->session->login_user_id;
				$data['recid'] = $rec_id;
				$data['app_name'] = $this->session->app_name;			
		
				$this->db->insert("detail",$data);
				
				$statusMsg = "Comment posted successfully";
					
			//}	
			
			
			$this->session->set_flashdata('flash_message',$statusMsg);
			
			//$this->medical_claims();  			
			
			redirect(base_url().'claims.php/partner/medical_claims','refresh');
	}
	
	function reinstate_claim($rec_id=""){
		$chk = $this->db->get_where('claims',array('rec'=>$rec_id))->row()->rmks;
		
			if($chk === "1" || $chk ==="3" || $chk ==="10"){
				
				$this->db->where("rec",$rec_id);
				
				$data['reinstatementdate'] = date('Y-m-d');		
		
				$this->db->update("claims",$data);
				
				$this->email_model->reinstated_claim_nofication($rec_id,$chk);
				
				$statusMsg = "Claim reinstated successfully";
					
			}else{
				
				$statusMsg = "Claim can't be reinstated";
					
			}	
			
			
			
			$this->session->set_flashdata('flash_message',$statusMsg);		
		
		
		redirect(base_url().'claims.php/partner/medical_claims','refresh');
	}
	/**
	function search_claim(){
		
		$arr = array();
		
		$start_date = $this->input->post('start_date');
		
		$end_date = $this->input->post('end_date');
		
		$cond_str = " rec > 0 ";
		
		$cond_str_range = " rec > 0 ";
		
		if(isset($_POST['date_range'])){			
			$cond_str_range .= " AND date BETWEEN '".$start_date."' AND '".$end_date."' ";
			
		}
		
		$this->db->where($cond_str_range);
		
		$fields = $this->input->post('field');
		$operators = $this->input->post('operator');
		$vals = $this->input->post('val');
		

			
			for($i=0;$i<sizeof($fields);$i++){
				if($fields[$i]!==""){
					if($operators[$i]==="LIKE"){
						$this->db->like($fields[$i],$vals[$i]);
					}else{
						//$arr[$fields[$i].$operators[$i]] = $vals[$i];
						$cond_str .= " AND ".$fields[$i].$operators[$i]."'".$vals[$i]."' ";
						
						$this->db->where($cond_str);
					}
				}
				
			}
			
		switch($this->session->userdata('logged_user_level')):
			case '1':
				$this->db->where(array("proNo"=>$this->session->userdata('center_id')));
				break;				
			case '2':
				$this->db->where(array("cluster"=>$this->session->userdata('cluster')));
				break;
		endswitch;
		 
		$claims = $this->db->get('app_medical_claims')->result_object(); 
		
		$page_data['claims'] = $claims;
        echo $this->load->view('backend/admin/claim_search_results', $page_data,TRUE);
		 
	}
	**/
	function reset_filter(){
		echo $this->load->view('backend/admin/load_filters', $page_data,TRUE);
	}
	
	function get_incident_sub_category($incident_type){
		$types = $this->db->get_where('illness_sub_category',array('type'=>$incident_type))->result_object();
		
		$opt = "<option value=''>".get_phrase('select')."</option>";
		
		foreach($types as $type):
			$opt .="<option value='".$type->illness_sub_category_id."'>".ucfirst($type->name)."</option>";
		endforeach;	
		
		echo $opt;
			
	}
	
	function get_incident($incident_sub_type){
		$types = $this->db->get_where('illness',array('illness_sub_category'=>$incident_sub_type))->result_object();
		
		$opt = "<option value=''>".get_phrase('select')."</option>";
		
		foreach($types as $type):
			$opt .="<option value='".$type->illness_id."'>".ucfirst($type->illness_name)."</option>";
		endforeach;	
		
		echo $opt;
			
	}
}