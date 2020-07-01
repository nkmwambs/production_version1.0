<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');

class Login extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        /* cache control */
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 2010 05:00:00 GMT");
    }

	
	public function validate_login($users_id="",$app_name=""){
		 $credential = array('users_id' => $users_id);

        // Checking login credential for admin
        $query = $this->db->get_where('users', $credential);
        if ($query->num_rows() > 0) {
		            $row = $query->row();
		            $this->session->set_userdata('admin_login', 1);
		            $this->session->set_userdata('admin_id', $row->users_id);
		            $this->session->set_userdata('login_user_id', $row->users_id);
		            $this->session->set_userdata('name', $row->name);
					$this->session->set_userdata('logged_user_level', $row->userlevel);
		            $login_type = $this->db->get_where('userlevels',array('userlevel_id'=>$row->userlevel))->row()->short_name;
		            $this->session->set_userdata('login_type', $login_type);
					
					$this->session->set_userdata('center_id', $this->db->get_where('settings',array('type'=>'default_user_group'))->row()->description);

					if($row->project_id > 0){
						$fname = $this->db->get_where('projectsdetails',array('projectsdetails_id'=>$row->project_id))->row()->project_id;
						
						if(strlen($fname)===5){
							$this->session->set_userdata('center_id', "KE0".substr($fname, 2));
						}else{
							$this->session->set_userdata('center_id', $fname);
						}	
					}

					$this->session->set_userdata('cluster', $this->db->get_where('settings',array('type'=>'default_user_group'))->row()->description);
					
					if($row->cluster_id > 0){
						$this->session->set_userdata('cluster', $this->db->get_where('clusters',array('cluster_id'=>$row->cluster_id))->row()->name);	
					}	
					
            redirect(base_url().$app_name.'.php/admin/dashboard', 'refresh');
        }

       

        redirect(base_url() . 'admin.php', 'refresh');	
		
	}

}