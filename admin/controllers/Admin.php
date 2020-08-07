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

class Admin extends CI_Controller
{
    
    
	function __construct()
	{
		parent::__construct();
		$this->load->database();
        $this->load->library('session');
		//$this->load->model('admin_model');
		$this->load->model('users_model','contact');
		
       /*cache control*/
		//$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		//$this->output->set_header('Pragma: no-cache');
		
    }
    
    /***default functin, redirects to login page if no admin logged in yet***/
    public function index()
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
    }
	
	function track_clicks($param1=""){
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
		
		//echo $this->db->affected_rows();
	}
	
	function get_active_announcements(){
		$active_announcements = $this->db->order_by('announcement_created_date DESC')->get_where('announcement',array('announcement_end_date>='=>date('Y-m-d')))->result_array();
		
		return $active_announcements;
	}

	function get_expired_announcements(){
		$expired_announcements = $this->db->order_by('announcement_created_date DESC')->get_where('announcement',array('announcement_end_date<='=>date('Y-m-d')))->result_array();
		
		return $expired_announcements;
	}

	function post_announcement(){
		$post = $this->input->post();

		$data['announcement_title'] = $post['announcement_title'];
		$data['announcement_detail'] = $post['announcement_detail'];
		$data['announcement_end_date'] = $post['announcement_end_date'];
		$data['announcement_created_by'] = $this->session->login_user_id;
		$data['announcement_created_date'] = date('Y-m-d');
		$data['announcement_last_modified_by'] = $this->session->login_user_id;

		$this->db->insert('announcement',$data);

		redirect(base_url().'admin.php/admin/announcement', 'refresh');
	}

	function edit_announcement($announcement_id){
		$post = $this->input->post();

		$data['announcement_title'] = $post['announcement_title'];
		$data['announcement_detail'] = $post['announcement_detail'];
		$data['announcement_end_date'] = $post['announcement_end_date'];
		$data['announcement_last_modified_by'] = $this->session->login_user_id;

		$this->db->where(array('announcement_id'=>$announcement_id));
		$this->db->update('announcement',$data);

		redirect(base_url().'admin.php/admin/announcement', 'refresh');
	}

	function delete_annnouncement($announcement_id){

		$this->db->where(array('announcement_id'=>$announcement_id));
		$this->db->delete('announcement');

		redirect(base_url().'admin.php/admin/announcement', 'refresh');
	}

	function announcement(){
        if ($this->session->userdata('admin_login') != 1)
			redirect(base_url(), 'refresh');
			

		$page_data['expired_announcements'] = $this->get_expired_announcements();	
		$page_data['active_announcements'] = $this->get_active_announcements();	
        $page_data['page_name']  = 'announcement';
        $page_data['page_title'] = get_phrase('new_announcement');
        $this->load->view('backend/index', $page_data);
	}
	
    /***ADMIN DASHBOARD***/
    function dashboard()
    {
        if ($this->session->userdata('admin_login') != 1)
			redirect(base_url(), 'refresh');
			
		$page_data['active_announcements'] = $this->get_active_announcements();	
        $page_data['page_name']  = 'dashboard';
        $page_data['page_title'] = get_phrase('apps_login_panel');
        $this->load->view('backend/index', $page_data);
    }
    
  	function users_list(){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
		
		$page_data['active_announcements'] = $this->get_active_announcements();	
        $page_data['page_name']  = 'users_list';
        $page_data['page_title'] = get_phrase('users_list');
        $this->load->view('backend/index', $page_data);  		
  	}

	public function ajax_list()
	{
		$list = $this->contact->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $rec) {
			$no++; 
			$row = array();
			$val = array('userid'=>$rec->ID,'auth'=>$rec->auth);
			$row[] = $this->load->view('backend/admin/user_action',$val,true);
			$row[] = $rec->userfirstname;
			$row[] = $rec->userlastname;
			$row[] = $rec->username;
			$row[] = $rec->email;
			$row[] = $this->db->get_where('positions',array('pstID'=>$rec->userlevel))->row()->dsgn;
			$row[] = $rec->fname;
			$row[] = $rec->cname;
				$status = "";						
					if($rec->auth==='1') $status = get_phrase('active');
					else $status = get_phrase('suspended');
											
			$row[] = $status;							
			
			$data[] = $row;
		}
		
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->contact->count_all(),
						"recordsFiltered" => $this->contact->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
		
	}			
	function switch_mode($param1=""){
		$data['description'] = $param1;;
		
		$this->db->where(array('type'=>'mode'));
		
		$this->db->update('settings',$data);
		
		echo $param1;
	}
    
	public function switch_user(){
		
		$users_id=$this->input->post('users_list');
		$app_name="admin";
		
		 $credential = array('ID' => $users_id);
		
        // Checking login credential for admin
        $query = $this->db->get_where('users', $credential);
        if ($query->num_rows() > 0) {
		            $row = $query->row();
		            $this->session->set_userdata('admin_login', 1);
		            $this->session->set_userdata('admin_id', $row->ID);
		            $this->session->set_userdata('login_user_id', $row->ID);
		            $this->session->set_userdata('name', $row->userfirstname);
					$this->session->set_userdata('logged_user_level', $row->userlevel);
		            $this->session->set_userdata('login_type', 'admin');
					
					//$fname = $this->db->get_where('settings',array('type'=>'default_user_group'))->row()->description;
					
					//if(is_object($this->db->get_where('projectsdetails',array('projectsdetails_id'=>$row->project_id))->row())){
						//$fname = $this->db->get_where('projectsdetails',array('projectsdetails_id'=>$row->project_id))->row()->project_id;
					//}
					
					
					//if(strlen($fname)===5){
						//$this->session->set_userdata('center_id', "KE0".substr($fname, 2));
					//}else{
						$this->session->set_userdata('center_id', $row->fname);
					//}	
					
					//$cluster = $this->db->get_where('settings',array('type'=>'default_user_group'))->row()->description;
					
					//if(is_object($this->db->get_where('clusters',array('cluster_id'=>$row->cluster_id))->row())){
						//$cluster = $this->db->get_where('clusters',array('cluster_id'=>$row->cluster_id))->row()->name;
					//}
					
					$this->session->set_userdata('cluster', $row->cname);			
									
					
            redirect(base_url().$app_name.'.php/admin/dashboard', 'refresh');
        }

       

        redirect(base_url() . 'admin.php', 'refresh');	
		
	}
	
	// function populate_projects($param1=""){
		
	// 	$opt = '<option value="">'.get_phrase("select").'</option>';

	// 	$projects = $this->db->get_where('users',array('cname'=>$param1,'department'=>'0','userlevel'=>'1'))->result_object();
										
	// 		foreach($projects as $row):

	// 			$opt .= '<option value="'.$row->fname.'">'.$row->fname.'</option>';
	
	// 		endforeach;
		
	// 	echo $opt;	
	// }

	function populate_projects($param1=""){
		
		$opt = '<option value="">'.get_phrase("select").'</option>';

		$this->db->select(array('icpNo'));
		$this->db->join('clusters','clusters.clusters_id=projectsdetails.cluster_id');
		$projects = $this->db->get_where('projectsdetails',
		array('clusterName'=>$param1))->result_object();
										
			foreach($projects as $row):

				$opt .= '<option value="'.$row->icpNo.'">'.$row->icpNo.'</option>';
	
			endforeach;
		
		echo $opt;	
	}
	
	function check_email($param1=""){
			
		$err = "0";
				
		$this->db->where('email',$param1);
		
		if($this->db->get("users")->num_rows()>0){
			//$err = get_phrase('email_exists');
			$err = "1";
		}
		
		echo $err;
	}
	
	/**System Settings**/
	function system_settings($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'admin.php/login', 'refresh');
        
        if ($param1 == 'do_update') {
			 
            $data['description'] = $this->input->post('system_name');
            $this->db->where('type' , 'system_name');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('system_title');
            $this->db->where('type' , 'system_title');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('address');
            $this->db->where('type' , 'address');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('phone');
            $this->db->where('type' , 'phone');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('paypal_email');
            $this->db->where('type' , 'paypal_email');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('currency');
            $this->db->where('type' , 'currency');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('system_email');
            $this->db->where('type' , 'system_email');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('system_name');
            $this->db->where('type' , 'system_name');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('language');
            $this->db->where('type' , 'language');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('text_align');
            $this->db->where('type' , 'text_align');
            $this->db->update('settings' , $data);
			
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated')); 
            redirect(base_url() . 'admin.php/admin/system_settings/', 'refresh');
        }
        if ($param1 == 'upload_logo') {
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/logo.png');
            
            $this->session->set_flashdata('flash_message', get_phrase('settings_updated'));
            redirect(base_url() . 'admin.php/admin/system_settings/', 'refresh');
        }
        if ($param1 == 'change_skin') {
            $data['description'] = $param2;
            $this->db->where('type' , 'skin_colour');
            $this->db->update('settings' , $data);
            $this->session->set_flashdata('flash_message' , get_phrase('theme_selected')); 
            redirect(base_url() . 'admin.php/admin/system_settings/', 'refresh'); 
        }
        $page_data['page_name']  = 'system_settings';
        $page_data['page_title'] = get_phrase('system_settings');
        $page_data['settings']   = $this->db->get('settings')->result_array();
        $this->load->view('backend/index', $page_data);
    }



/*****SMS SETTINGS*********/
    function sms_settings($param1 = '' , $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'admin.php/login', 'refresh');
        if ($param1 == 'clickatell') {

            $data['description'] = $this->input->post('clickatell_user');
            $this->db->where('type' , 'clickatell_user');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('clickatell_password');
            $this->db->where('type' , 'clickatell_password');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('clickatell_api_id');
            $this->db->where('type' , 'clickatell_api_id');
            $this->db->update('settings' , $data);

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'admin.php/admin/sms_settings/', 'refresh');
        }

        if ($param1 == 'twilio') {

            $data['description'] = $this->input->post('twilio_account_sid');
            $this->db->where('type' , 'twilio_account_sid');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('twilio_auth_token');
            $this->db->where('type' , 'twilio_auth_token');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('twilio_sender_phone_number');
            $this->db->where('type' , 'twilio_sender_phone_number');
            $this->db->update('settings' , $data);

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'admin.php/admin/sms_settings/', 'refresh');
        }

        if ($param1 == 'active_service') {

            $data['description'] = $this->input->post('active_sms_service');
            $this->db->where('type' , 'active_sms_service');
            $this->db->update('settings' , $data);

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'admin.php/admin/sms_settings/', 'refresh');
        }

        $page_data['page_name']  = 'sms_settings';
        $page_data['page_title'] = get_phrase('sms_settings');
        $page_data['settings']   = $this->db->get('settings')->result_array();
        $this->load->view('backend/index', $page_data);
    }

   /*****LANGUAGE SETTINGS*********/
    function manage_language($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
			redirect(base_url() . 'admin.php/login', 'refresh');
		
		if ($param1 == 'edit_phrase') {
			$page_data['edit_profile'] 	= $param2;	
		}
		if ($param1 == 'update_phrase') {
			$language	=	$param2;
			$total_phrase	=	$this->input->post('total_phrase');
			for($i = 1 ; $i < $total_phrase ; $i++)
			{
				//$data[$language]	=	$this->input->post('phrase').$i;
				$this->db->where('phrase_id' , $i);
				$this->db->update('language' , array($language => $this->input->post('phrase'.$i)));
			}
			redirect(base_url() . 'admin.php/admin/manage_language/edit_phrase/'.$language, 'refresh');
		}
		if ($param1 == 'do_update') {
			$language        = $this->input->post('language');
			$data[$language] = $this->input->post('phrase');
			$this->db->where('phrase_id', $param2);
			$this->db->update('language', $data);
			$this->session->set_flashdata('flash_message', get_phrase('settings_updated'));
			redirect(base_url() . 'admin.php/admin/manage_language/', 'refresh');
		}
		if ($param1 == 'add_phrase') {
			$data['phrase'] = $this->input->post('phrase');
			$this->db->insert('language', $data);
			$this->session->set_flashdata('flash_message', get_phrase('settings_updated'));
			redirect(base_url() . 'admin.php/admin/manage_language/', 'refresh');
		}
		if ($param1 == 'add_language') {
			$language = $this->input->post('language');
			$this->load->dbforge();
			$fields = array(
				$language => array(
					'type' => 'LONGTEXT'
				)
			);
			$this->dbforge->add_column('language', $fields);
			
			$this->session->set_flashdata('flash_message', get_phrase('settings_updated'));
			redirect(base_url() . 'admin.php/admin/manage_language/', 'refresh');
		}
		if ($param1 == 'delete_language') {
			$language = $param2;
			$this->load->dbforge();
			$this->dbforge->drop_column('language', $language);
			$this->session->set_flashdata('flash_message', get_phrase('settings_updated'));
			
			redirect(base_url() . 'admin.php/admin/manage_language/', 'refresh');
		}
		$page_data['page_name']        = 'manage_language';
		$page_data['page_title']       = get_phrase('manage_language');
		//$page_data['language_phrases'] = $this->db->get('language')->result_array();
		$this->load->view('backend/index', $page_data);	
    }
	
	function application_settings($param1="",$param2=""){
         if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
		 
		 if($param1==='edit'){
		 	$this->db->where(array('apps_id'=>$param2));
			 
			$this->db->update('apps',$this->input->post());
			
			$this->session->set_flashdata('flash_message',get_phrase('record_edited_successfully'));
			redirect(base_url().'admin.php/admin/application_settings','refresh');
		 }
		 
		
		$page_data['page_name']        = 'application_settings'; 	
		$page_data['page_title']       = get_phrase('application_settings');
		$this->load->view('backend/index', $page_data);	
	}
	
	function toggle_status($param1=""){
		 $app = explode('_', $param1);
		 
		 $apps_id = $app[0];
		 
		 $userlevel_id = $app[1];
		 
		 $status = $this->db->get_where('access',array('apps_id'=>$apps_id,'level'=>$userlevel_id))->row()->status;
		 
		 $this->db->where(array('apps_id'=>$apps_id,'level'=>$userlevel_id));
		 
		 if($status==='0'){
		 	$this->db->update('access',array('status'=>'1'));
		 }elseif($status==='1'){
		 	$this->db->update('access',array('status'=>'0'));
		 }	
	}
	
	function user_groups($param1=""){
         if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
		 
		if($param1==='add_supervisory_group'){
				
			$data['name'] = $this->input->post('name');
			
			$this->db->insert('clusters',$data);
			
			$this->session->set_flashdata('flash_message', get_phrase('group_added'));
            redirect(base_url() . 'admin.php/admin/manage_profile/', 'refresh');			
		}
	    
		if($param1="delete_supervisory_group"){
		
		}
      	
		if($param1==='change_work_group'){
			
			$data['cluster_id'] = $this->input->post('cluster_id');
			$data['project_id'] = $this->input->post('project_id');
			
			$this->db->where('ID',$param2);
			
			$this->db->update('users',$data);
			
			$this->session->set_flashdata('flash_message', get_phrase('user_updated'));
            redirect(base_url() . 'admin.php/admin/manage_profile/', 'refresh');
		}
		
	    $page_data['page_name']  = 'manage_profile';
        $page_data['page_title'] = get_phrase('manage_profile');
        $page_data['edit_data']  = $this->db->get_where('users', array('ID' => $this->session->userdata('login_user_id')))->result_array();
	    $page_data['all_users'] = $this->db->get_where('users',array('ID!='=>$this->session->login_user_id))->result_object();
        $this->load->view('backend/index', $page_data);
}

 /******MANAGE OWN PROFILE AND CHANGE PASSWORD***/
    function manage_profile($param1 = '', $param2 = '', $param3 = '')
    {
         if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
			
		if($param1==="add_work_group"){
			$data['name'] = $this->input->post('name');
            $data['project_id'] = $this->input->post('project_id');
			$data['cluster_id'] = $param2;

            $this->db->insert('projectsdetails', $data);
			 
            $this->session->set_flashdata('flash_message', get_phrase('work_group_created'));
            redirect(base_url() . 'admin.php/admin/manage_profile/', 'refresh');	
		}	
		
		if($param1==='add_cluster'){
			$data['clusterName'] = $this->input->post('clusterName');

			$this->db->where(array('clusterName'=>$this->input->post('clusterName')));
			$query = $this->db->get('clusters');
			
			if($query->num_rows() === 0){
			
				$this->db->insert('clusters', $data);
				 
	            $this->session->set_flashdata('flash_message', get_phrase('cluster_added'));				
			}else{
				$this->session->set_flashdata('flash_message', get_phrase('action_failed'));
			}

            redirect(base_url() . 'admin.php/admin/manage_profile/#clusters', 'refresh');	
		}
		if($param1==='edit_project'){
			
			$data['icpNo'] = $this->input->post('icpNo');
			$data['icpName'] = $this->input->post('icpName');
			$data['cluster_id'] = $this->input->post('cluster_id');
			$data['email'] = $this->input->post('email');
			$data['status'] = $this->input->post('status');
			
			$this->db->where(array('icpNo'=>$this->input->post('icpNo')));
			$this->db->or_where(array('email'=>$this->input->post('email')));
			$query = $this->db->get('projectsdetails');
			
			if($query->num_rows() === 1){
				
				$this->db->where(array('ID'=>$param2));	
				$this->db->update('projectsdetails',$data);
				
				/**Updating User table **/
				$data2['email'] = $this->input->post('email');
				$data2['auth'] = $this->input->post('status');
				
				$this->db->where(array('fname'=>$this->input->post('icpNo'),'department'=>'0','userlevel'=>'1'));	
				$this->db->update('users',$data);
				
				/** **/
				
				$this->session->set_flashdata('flash_message', get_phrase('project_edited'));				
			}else{
				$this->session->set_flashdata('flash_message', get_phrase('action_failed'));
			}

            redirect(base_url() . 'admin.php/admin/manage_profile/#groups', 'refresh');	
			
		}

		if($param1==='add_project'){
			$data['icpNo'] = $this->input->post('icpNo');
			$data['icpName'] = $this->input->post('icpName');
			$data['cluster_id'] = $this->input->post('cluster_id');
			$data['email'] = $this->input->post('email');
			
			$this->db->where(array('icpNo'=>$this->input->post('icpNo')));
			$this->db->or_where(array('email'=>$this->input->post('email')));
			$query = $this->db->get('projectsdetails');
			
			if($query->num_rows() === 0){
			
				$this->db->insert('projectsdetails', $data);
				
				/**This code creates a project user in users table**/
					$data2['username'] = $this->input->post('icpNo');
					$data2['userfirstname'] = $this->input->post('icpNo');
					$data2['userlastname'] = $this->input->post('icpName');
		            $data2['email'] = $this->input->post('email');
					$data2['userlevel'] = '1';
					$data2['fname'] = $this->input->post('icpNo');	
					$data2['lname'] = $this->input->post('icpName');
					$data2['cname'] = $this->db->get_where('clusters',array('clusters_id'=>$this->input->post('cluster_id')))->row()->clusterName;
					$data2['auth'] = '1';
					$new_password = substr( md5(rand(100000000,20000000000)) , 0,7); 
					$data2['password'] =  md5($new_password);
		            $data2['department'] = '0';		
					
					$query2 = $this->db->get_where('users',array('fname'=>$this->input->post('icpNo')));	
					
					if($query2->num_rows() === 0){
						$this->db->insert('users',$data2);

						// Send an email to the new account user email
						$account_type = 'Partner';
						$this->email_model->account_opening_email($account_type, $this->input->post('email'),$new_password);
					}		 
				 
				/****/
				 
	            $this->session->set_flashdata('flash_message', get_phrase('project_added'));				
			}else{
				$this->session->set_flashdata('flash_message', get_phrase('action_failed'));
			}

            redirect(base_url() . 'admin.php/admin/manage_profile/#groups', 'refresh');	
		}

		if($param1==='edit_cluster'){
			$data['clusterName'] = $this->input->post('clusterName');

			$this->db->where(array('clusterName'=>$this->input->post('clusterName')));
			$query = $this->db->get('clusters');
			
			if($query->num_rows() === 0){
			
				$this->db->where(array('clusters_id'=>$param2));
				$this->db->update('clusters',$data);
				 
	            $this->session->set_flashdata('flash_message', get_phrase('cluster_edited'));	
							
			}else{
				$this->session->set_flashdata('flash_message', get_phrase('action_failed'));
			}
			

            redirect(base_url() . 'admin.php/admin/manage_profile/#clusters', 'refresh');
		}		
				
		if($param1==='add_user'){
			$data['username'] = $this->input->post('username');
			$data['userfirstname'] = $this->input->post('userfirstname');
			$data['userlastname'] = $this->input->post('userlastname');
            $data['email'] = $this->input->post('email');
			$data['userlevel'] = $this->input->post('userlevel');
			$data['fname'] = $this->input->post('fname');	
			$data['cname'] = $this->input->post('cname');
			if($this->input->post('userlevel') > '2'){
				$data['fname'] = "KE";	
				$data['cname'] = "KE";
			}elseif($this->input->post('userlevel')=== '2'){
				$data['fname'] = "KE";
			}	
	
			$data['auth'] = '1';
			$new_password = substr( md5(rand(100000000,20000000000)) , 0,7);
			$data['password'] = md5($new_password);//md5($this->input->post('password'));	
            $data['department'] = $this->input->post('department');
			
			if($this->db->get_where('users',array('email'=>$this->input->post('email')))->num_rows()===0 && 
				$this->db->get_where('users',array('username'=>$this->input->post('username')))->num_rows()===0
			){
				$this->db->insert('users', $data);
				$this->email_model->account_opening_email($this->db->get_where("positions",array("pstID"=>$this->input->post('userlevel')))->row()->dsgn , $this->input->post('email'),$new_password);
				$this->session->set_flashdata('flash_message', get_phrase('user_created'));
			}else{
				$this->session->set_flashdata('flash_message', get_phrase('user_not_created:_duplicate_email_or_username'));
			}
            
			 
            
            redirect(base_url() . 'admin.php/admin/manage_profile/#users', 'refresh');			
		}
		
		if($param1==='edit_user'){
			$msg = get_phrase('user_not_updated');
			
			$account = $this->db->get_where("users",array("ID"=>$param2))->row();
			
            $data['password'] = md5($this->input->post('password'));
			
			if($this->db->get_where('users',array('email'=>$this->input->post('email')))->num_rows()===0){
				$data['email'] = $this->input->post('email');
			}	
            
			$data['userfirstname'] = $this->input->post('userfirstname');
            $data['userlastname'] = $this->input->post('userlastname');
			
            $this->db->where('ID', $param2);
            $this->db->update('users', $data);
			
			if($this->db->affected_rows() > 0) {
				$msg = get_phrase('user_updated');	
				$this->email_model->password_reset_email($this->input->post('password'),$this->db->get_where("positions",array("pstID"=>$account->userlevel))->row()->dsgn , $this->input->post('email'));
			
			}

            $this->session->set_flashdata('flash_message', $msg);
            redirect(base_url() . 'admin.php/admin/manage_profile/#users', 'refresh');			
		}
		if($param1==='suspend_user'){
			
			$msg = get_phrase('user_not_updated');	
				
			if($param3==='1'){
				$data['auth'] = 0;
			}else{
				$data['auth'] = 1;
			}
			
			$account = $this->db->get_where("users",array("ID"=>$param2))->row(); 
			
			$this->db->where(array('ID'=>$param2));
			
			$this->db->update('users',$data);
			
			if($this->db->affected_rows() > 0) {
				$msg = get_phrase('user_updated');	
				$this->email_model->account_suspension_email($this->input->post('email'));
			}
			
			$this->session->set_flashdata('flash_message', $msg);
            redirect(base_url() . 'admin.php/admin/manage_profile/#users', 'refresh');
		}
        if ($param1 == 'update_profile_info') {
            //$data['name']  = $this->input->post('name');
            $data['email'] = $this->input->post('email');
			$data['userfirstname'] = $this->input->post('userfirstname');
			$data['userlastname'] = $this->input->post('userlastname');

            
            $this->db->where('ID', $this->session->userdata('login_user_id'));
            $this->db->update('users', $data);
            //move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/admin_image/' . $this->session->userdata('login_user_id') . '.jpg');
            $this->session->set_flashdata('flash_message', get_phrase('account_updated'));
            redirect(base_url() . 'admin.php/admin/manage_profile/#list', 'refresh');
        }
        if ($param1 == 'change_password') {
            $data['password']             = $this->input->post('password');
            $data['new_password']         = $this->input->post('new_password');
            $data['confirm_new_password'] = $this->input->post('confirm_new_password');
            
            $current_password = $this->db->get_where('users', array('ID' => $this->session->userdata('login_user_id')))->row()->password;
            if ($current_password == md5($data['password']) && $data['new_password'] == $data['confirm_new_password']) {
                $this->db->where('ID', $this->session->userdata('login_user_id'));
                $this->db->update('users', array('password' => md5($data['new_password'])));
                $this->session->set_flashdata('flash_message', get_phrase('password_updated'));
            } else {
                $this->session->set_flashdata('flash_message', get_phrase('password_mismatch'));
            }
            redirect(base_url() . 'admin.php/admin/manage_profile/#password', 'refresh');
        }
		
		$page_data['active_announcements'] = $this->get_active_announcements();	
        $page_data['page_name']  = 'manage_profile';
        $page_data['page_title'] = get_phrase('manage_profile');
        $page_data['edit_data']  = $this->db->get_where('users', array('ID' => $this->session->userdata('login_user_id')))->row();
	    //$page_data['all_users'] = $this->db->get_where('users',array('ID!='=>$this->session->login_user_id))->result_object();
        $this->load->view('backend/index', $page_data);
    }

	function manage_data($param1="",$param2=""){
         if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');	
		
		if($param1==='back_up'){
			
			if($param2==="all"){
				$this->db->where(array('TABLE_SCHEMA'=>$this->db->database,'TABLE_COMMENT<>'=>""));
			}else{
				$this->db->where(array('TABLE_SCHEMA'=>$this->db->database,'TABLE_COMMENT'=>$param2));
			}
			
			$tables_array = $this->db->select('TABLE_NAME')->get('information_schema.tables')->result_array(); 
			
			$new_array = array();
			
			foreach($tables_array as $key=>$value){
				$new_array[] = $value['TABLE_NAME'];
			}
			
			$this->settings_model->create_backup($new_array,$param2);
			
			$this->session->set_flashdata('flash_message' , get_phrase('back_up_complete'));
			redirect(base_url().'admin.php/admin/manage_data/', 'refresh'); 
		}
		
		if($param1==='restore'){
			$this->settings_model->restore_backup();
			
			$this->session->set_flashdata('flash_message' , get_phrase('restore_complete'));
			redirect(base_url().'admin.php/admin/manage_data/', 'refresh'); 
		}
		
		if($param1==="restore_csv"){
				
			$msg = get_phrase("restore_failure");
				
			$table = $this->input->post('table_name');
			
			$this->db->trans_start();
			
			$this->db->empty_table($table);
			
			$file = $_FILES['userfile']['tmp_name'];
			
			$result =   $this->csvreader->parse_file($file);

        	$this->db->insert_batch($table,$result);
			
			$this->db->trans_complete();
			
			if($this->db->affected_rows()>0){
				$msg = get_phrase('restore_complete').". ".count($result)." ".get_phrase("records_uploaded");
			}
			
			$this->session->set_flashdata('flash_message' ,$msg);
			redirect(base_url().'admin.php/admin/manage_data/', 'refresh'); 
		}
		
		if($param1==="delete"){
			$this->db->where(array('TABLE_SCHEMA'=>$this->db->database,'TABLE_COMMENT'=>$param2));
			
			$tables_array = $this->db->select('TABLE_NAME')->get('information_schema.tables')->result_array(); 
			
			$new_array = array();
			
			foreach($tables_array as $key=>$value){
				$new_array[] = $value['TABLE_NAME'];
			}
			
			$this->settings_model->truncate($new_array);
			
			$this->session->set_flashdata('flash_message' , get_phrase('truncation_complete'));
			redirect(base_url().'admin.php/admin/manage_data/', 'refresh'); 
		}
		
		$this->db->where(array('TABLE_SCHEMA'=>$this->db->database,'TABLE_COMMENT<>'=>""));
		$this->db->group_by('TABLE_COMMENT');
		$query = $this->db->select('TABLE_COMMENT as TABLE_NAME,COUNT(*) as COUNT_TABLES')->select_sum('DATA_LENGTH')->get("information_schema.tables");
		$tables = $query->result_object();
		 			
        $page_data['page_name']  = 'manage_data';
        $page_data['page_title'] = get_phrase('manage_data');
		$page_data['tables'] =  $tables;
	    $this->load->view('backend/index', $page_data);
	}

	function switch_app_status($app_id="",$status=""){
		$data['status'] = $status;
		$this->db->where(array('apps_id'=>$app_id));
		$this->db->update('apps',$data);
		//echo $status==='1'?get_phrase('app_is_now_active'):get_phrase('app_is_now_inactive');
	}
	
	function test(){
         if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');	
		 		
		
        $page_data['page_name']  = __FUNCTION__;
        $page_data['page_title'] = "Test Page";
	    $this->load->view('backend/index', $page_data);		
	}

}
