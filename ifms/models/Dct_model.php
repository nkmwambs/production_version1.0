<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//DEFINE('DS', DIRECTORY_SEPARATOR);	

class Dct_model extends CI_Model {
	
	function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function post_voucher(){
    
    $hID = 0;

     // Move file to dct_document folder only when database insert is completed successful
     //$voucher_number = $this->input->post('VNumber');
     //$voucher_date = $this->input->post('TDate');
     $this->db->where(array('icpNo'=>$this->input->post('KENo')));
     $projectsdetails_id = $this->db->get('projectsdetails')->row()->ID;

     //$temp_dir_name = BASEPATH . DS . '..' . DS . 'uploads' . DS . 'temps' . DS . $this->temp_folder_hash($voucher_number);
    
    $this->db->trans_begin();
   
    $data['icpNo']  = $this->input->post('KENo');
    $data['TDate'] = date('Y-m-d', strtotime($this->input->post('TDate')));
    $data['Fy'] = get_fy($this->input->post('TDate'), $this->session->center_id);
    $data['VNumber'] = $this->input->post('VNumber');
    $data['Payee'] = $this->input->post('Payee');
    $data['Address'] = $this->input->post('Address');
    $data['VType'] = $this->input->post('VTypeMain');
    //$data['raiser_id'] = $this->session->login_user_id;

    //Check if Bank Details exists

    if ($this->db->get_where('projectsdetails', array('icpNo' => $this->session->userdata('center_id')))->num_rows() > 0) {

        $bank_code = $this->db->get_where('projectsdetails', array('icpNo' => $this->session->userdata('center_id')))->row()->bankID;
    } else {
        $bank_code = 0;
    }

    //Append Bank Code to ChqNo

    if ($this->input->post('reversal')) {
        $bank_code = $bank_code . "-0";
    }

    $data['ChqNo'] = $this->input->post('ChqNo') . "-" . $bank_code;
    $data['TDescription'] = $this->input->post('TDescription');
    $data['totals'] = array_sum($this->input->post('cost'));
    $data['unixStmp'] = time();

    //Check if voucher already exists
    $chk_obj = $this->db->get_where("voucher_header", array("VNumber" => $this->input->post('VNumber'), "icpNo" => $this->input->post('KENo')));

    if ($chk_obj->num_rows() == 0) {

        $this->db->insert('voucher_header', $data);

        //Last id
        $hID = $this->db->insert_id();

        //Populate body //hID,icpNo,VNumber,TDate,VType,ChqNo,unixStmp     Qty,Details,UnitCost,Cost,AccNo,civaCode //$data2[''][$i]=
        $qty = $this->input->post('qty');
        $details = $this->input->post('desc');
        $unitcost = $this->input->post('unit');
        $cost = $this->input->post('cost');
        $acc = $this->input->post('acc');
        $civ = $this->input->post('civaCode');
        $voucher_item_type = $this->input->post('voucher_item_type');
        $support_mode = $this->input->post('support_mode');
        
        $uploads_required=false;

        $get_all_support_modes=$this->db->get('support_mode')->result_array();

        $array_of_support_mode_ids=array_column($get_all_support_modes,'support_mode_id');
        $array_of_is_dct_flag=array_column($get_all_support_modes,'support_mode_is_dct');

        $combined_support_mode_id_with_dct_flag=array_combine($array_of_support_mode_ids, $array_of_is_dct_flag);

        for ($i = 0; $i < sizeof($this->input->post('qty')); $i++) {
            $data2['hID'] = $hID;
            $data2['icpNo'] = $this->input->post('KENo');
            $data2['VNumber'] = $this->input->post('VNumber');
            $data2['TDate'] = $this->input->post('TDate');
            $data2['VType'] = $this->input->post('VTypeMain');
            $data2['ChqNo'] = $this->input->post('ChqNo') . "-" . $bank_code;
            $data2['unixStmp'] = time();
            $data2['Qty'] = $qty[$i];
            $data2['Details'] = $details[$i];
            $data2['UnitCost'] = $unitcost[$i];
            $data2['Cost'] = $cost[$i];
            $data2['AccNo'] = $acc[$i];
            $data2['civaCode'] = $civ[$i];
            $data2['fk_voucher_item_type_id'] = $voucher_item_type[$i];
            $data2['fk_support_mode_id'] = $support_mode[$i];

            //Check the dct flag is on
            if($combined_support_mode_id_with_dct_flag[$support_mode[$i]]==1 && $uploads_required==false){
                $uploads_required=true;
            }

            $this->db->insert('voucher_body', $data2);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $hID = 0;
        } 
        else {

            $this->db->trans_commit(); 
             
            if($uploads_required){
                //$this->move_temp_files_to_dct_document($temp_dir_name, $voucher_date, $voucher_number);
                $update_data['is_upload_to_s3_completed'] = 1;

                $this->db->where([
                    'item_name'=>'dct_documents',
                    'attachment_primary_id'=>$this->input->post('VNumber'),
                    'fk_projectsdetails_id'=>$projectsdetails_id]);

                $this->db->update('attachment',$update_data);
            }         
           

        }
    }

    return $hID;
    }

    function temp_folder_hash($voucher_number){
		$hash_folder_name = $this->session->login_user_id . date('Y-m-d') . $voucher_number; //.random_int(10,1000000);
		$hash = md5($hash_folder_name);

		return $hash;
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

		$final_file_path = BASEPATH . DS . '..' . DS . 'uploads' . DS . 'dct_documents' . DS . $this->session->center_id . DS . $month_folder . DS . $voucher_number;

		return rename($temp_dir_name, $final_file_path);
    }
    
    function get_voucher_item_types(){
		$this->db->select(array('voucher_item_type_id','voucher_item_type_name'));
		$voucher_item_types = $this->db->get_where('voucher_item_type',array('voucher_type_item_is_active'=>1))->result_array();

		return $voucher_item_types;
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
    
    function get_accounts_related_voucher_item_type(int $voucher_item_type_id=0){

        $this->db->join('voucher_items_with_accounts','voucher_items_with_accounts.accounts_id=accounts.accID');
        $this->db->join('voucher_item_type','voucher_items_with_accounts.voucher_item_type_id=voucher_item_type.voucher_item_type_id');
        
        $this->db->where(array('voucher_item_type.voucher_item_type_id'=>$voucher_item_type_id));
    }

    function get_civa_related_voucher_item_type(int $voucher_item_type_id=0){

        $this->db->join('voucher_items_with_civa','voucher_items_with_civa.fk_civa_id=civa.civaID');
        $this->db->join('voucher_item_type','voucher_items_with_civa.fk_voucher_item_type_id=voucher_item_type.voucher_item_type_id');
        
        $this->db->where(array('voucher_item_type.voucher_item_type_id'=>$voucher_item_type_id));
    }
    /**
     * @author: Onduso
     * @Dated: 21/7/2020
     */
    function get_all_dct_recipients(){
        $recipients=$this->db->select(array('voucher_item_type_id','voucher_item_type_name'))->get('voucher_item_type')->result_array();
        return $recipients;
    }

    function uploaded_dct_documents($voucher_number,$reporting_month,$support_mode_id,$row_id,$fcp_id = ""){
        // return [
        //     ['attachment_url'=>'uploads/dct_documents/KE445/2018-06/180620/180620_3_1','attachment_name'=>"Travel Advance Template- Jan '21.pdf",'attachment_created_date'=>'2021-02-24 11:09:59','attachment_size'=>'	126043'],
        //     ['attachment_url'=>'uploads/dct_documents/KE445/2018-06/180620/180620_2_1','attachment_name'=>"BANK STATEMENT DECEMBER  2020 (1).pdf",'attachment_created_date'=>'2021-02-24 11:55:30','attachment_size'=>'2133516']
        // ];
        
        
        
        // $voucher_number = 180620;
        // $reporting_month = '2018-06-20';
        // $support_mode_id = 1;
        // $row_id = 3;

        $document_type = 'dct_documents';

        $fcp_id = $fcp_id == "" ? $this->session->center_id: $fcp_id;

        $this->db->where(array('icpNo'=>$fcp_id));
		$project_id = $this->db->get('projectsdetails')->row()->ID;
            
        $month = date('Y-m',strtotime($reporting_month));
        
        $url = 'uploads/dct_documents/'.$fcp_id.'/'.$month.'/'.$voucher_number.'/'.$voucher_number.'_'.$row_id.'_'.$support_mode_id;

        $this->db->select(array('attachment_id','attachment_name','attachment_url','attachment_created_date','attachment_size'));

        $this->db->where(
            array(
                'item_name'=>$document_type,
                'fk_projectsdetails_id'=>$project_id,
                'attachment_primary_id'=>$voucher_number,
                'attachment_url'=>$url
            )
        );
        $attachment_obj = $this->db->get('attachment');

        $attachment = [];

        if($attachment_obj->num_rows() > 0){
            $attachment = $attachment_obj->result_array();
        }

        return $attachment;
    }
}

