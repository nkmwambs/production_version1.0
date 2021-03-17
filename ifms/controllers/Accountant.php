<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Accountant extends CI_Controller
{   
	

	function __construct()
	{
		parent::__construct();
		$this->load->database();
        $this->load->library('session');
		
		$this->load->library('finance_dashboard');
		$this->load->model('dashboard_model');
		$this->load->model('finance_model');

		set_time_limit(5000);
			
       /*cache control*/
		// $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		// $this->output->set_header('Pragma: no-cache');
        
        //if(count($this->uri->segment_array()) < 4){
           $this->output->cache(120);
        //}		
		

    }
    
    /***default functin, redirects to login page if no admin logged in yet***/
    public function index()
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url().'admin.php/login', 'refresh');
    }
    
    /***Load Default Pages***/
   
   function get_clusters_with_active_icps($closure_date = ""){
   	
	if($closure_date == "") $closure_date = date('Y-m-t');
	
	$result = array();

	/**
	 * The correct table to be used should be clusters and projectdetails other than users 
	 * Use with a join and select cluster and icp number.
	 * **/
	$this->db->join('opfundsbalheader','opfundsbalheader.icpNo=users.fname'); 
	$this->db->select(array('fname','cname','allowEdit','submitted'));
	$active_icps_with_clusters = $this->db->get_where('users',
	array('department'=>0,'userlevel'=>1,'closureDate'=>$closure_date))->result_object();
	
	foreach($active_icps_with_clusters as $icp){
		//$result[$icp->cname][$icp->fname] = $icp->fname; 
		$result[$icp->cname][$icp->fname] = array('submitted'=>$icp->submitted,'allowEdit'=>$icp->allowEdit);
	}
	
	return $result;
   }
    
   function dashboard($month = "")
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
		
		$current_month = $month;	
			
		if($month == "") {
			//Get last dashboard run
			$last_run_obj = $this->db->select_max('month')->get('dashboard_run');

			if($last_run_obj->result_array()[0]['month'] != null){
				$current_month = strtotime($last_run_obj->row()->month);
			}else{
				$current_month = strtotime(date('Y-m-t'));
			}
		}
		
		$page_data['month']= $current_month;
        $page_data['page_name']  = 'dashboard';
        $page_data['page_title'] = get_phrase('finance_dashboard');
        $this->load->view('backend/index', $page_data);
    }
	
	function cash_journal($timestamp = '', $fcp_number = '', $param3 = '')
	{
		if ($this->session->userdata('admin_login') != 1)
			redirect(base_url() . 'admin.php', 'refresh');


		$page_data['project'] = $fcp_number;
		$page_data['cash_journal'] = $this->finance_model->cash_journal_grid($timestamp, $fcp_number);
		$page_data['tym']  = $timestamp; //strtotime($this->finance_model->current_financial_month($fcp_number));
		$page_data['month'] = date("Y-m-t", strtotime($this->finance_model->current_financial_month($fcp_number)));
		$page_data['page_name']  = 'cash_journal';
		$page_data['page_title'] = get_phrase('cash_journal');
		$this->load->view('backend/index', $page_data);
	}

	function scroll_cash_journal($project, $date = "", $cnt = "", $flag = "")
	{
		if ($this->session->userdata('admin_login') != 1)
			redirect(base_url() . 'admin.php', 'refresh');


		if ($flag !== "") {

			$sign = '+';

			if ($flag == 'prev') {
				$sign = '-';
			}

			$tym  = strtotime('first day of ' . $sign . $cnt . 'month', $date);

			$page[$tym] = $tym;
			$page_data['cash_journal'] = $this->finance_model->cash_journal_grid($tym, $project);
		} else {
			$tym  = $date;
			$page[$tym] = $tym;
			$page_data['cash_journal'] = $this->finance_model->cash_journal_grid($tym, $project);
		}

		$page_data['project'] = $project;
		$page_data['page_name']  = 'cash_journal';
		$page_data['page_title'] = get_phrase('cash_journal');
		$this->load->view('backend/index', $page_data);
	}	
	
// 	function cash_journal($param1 = '', $param2 = '', $param3 = ''){
// 		 if ($this->session->userdata('admin_login') != 1)
//             redirect(base_url().'admin.php', 'refresh');
		
		
// 		$max_mfr_id = $this->db->select_max('balHdID')->get_where('opfundsbalheader',array('icpNo'=>$param2))->row()->balHdID;
		 	
// 		$page_data['tym']  = $param1;//strtotime($this->db->select_max('closureDate')->get_where('opfundsbalheader',array('icpNo'=>$param2))->row()->closureDate);//strtotime('+1 month',strtotime($last_mfr->closureDate));		
//         $page_data['project'] = $param2;
//         $page_data['page_name']  = 'cash_journal';
//         $page_data['page_title'] = get_phrase('cash_journal');
// 		$this->load->view('backend/index', $page_data);
// }
  
//   function scroll_cash_journal($project,$date="",$cnt="",$flag=""){
// 		 if ($this->session->userdata('admin_login') != 1)
//             redirect(base_url().'admin.php', 'refresh');
		
		
// 		$max_mfr_id = $this->db->select_max('balHdID')->get_where('opfundsbalheader',array('icpNo'=>$project))->row()->balHdID;
		
// 		$sign = '+';
		
// 		if($flag==='prev'){
// 			$sign = '-';
// 		}
		 	
// 		$page_data['tym']  = strtotime($sign.$cnt.' months',$date);	
// 		$page_data['project'] = 	$project;
//         $page_data['page_name']  = 'cash_journal';
//         $page_data['page_title'] = get_phrase('cash_journal');
// 		$this->load->view('backend/index', $page_data);

// }

public function fo_fund_balance_report($month){
		 if ($this->session->userdata('admin_login') != 1)
            redirect(base_url().'admin.php', 'refresh');			
		
		
		$header = $this->db->get_where('opfundsbalheader',array('closureDate'=>date('Y-m-t',strtotime("last day of previous month",$month))))->result_object();	
		
		$append_funds = array();
		
		foreach($header as $row):
			$append_funds[$row->icpNo] = $this->db->get_where('opfundsbal',array('balHdID'=>$row->balHdID))->result_object();
		endforeach;	
			
		$page_data['header'] = $header;	
		$page_data['tym']  = $month;
		$page_data['rec']  = $append_funds;	
        $page_data['page_name']  = 'fo_fund_balance_report';
        $page_data['page_title'] = get_phrase('fund_balance_report');
		$this->load->view('backend/index', $page_data);
}

// public function fo_expense_report($month){
		 // if ($this->session->userdata('admin_login') != 1)
            // redirect(base_url().'admin.php', 'refresh');			
// 		
		// $append_funds = array();
		// $month = "2017-11-30";
		// $query = "SELECT users.cname as cname,voucher_body.icpNo as icpNo, voucher_body.AccNo as AccNo, SUM(voucher_body.Cost) as Cost  FROM `voucher_body` LEFT JOIN `accounts` ON voucher_body.AccNo=accounts.AccNo LEFT JOIN users ON voucher_body.icpNo=users.fname WHERE accounts.AccGrp=0 AND voucher_body.TDate>='".date('Y-m-01',$month)."' AND voucher_body.TDate<='".date('Y-m-t',$month)."' GROUP BY voucher_body.icpNo,voucher_body.AccNo";
// 		
		// $results = $this->db->query($query)->result_object();
// 			
		// foreach($results as $row){
			// $append_funds[$row->cname][$row->icpNo][$row->AccNo] = $row->Cost;
		// }
// 			
// 			
		// $page_data['tym']  = $month;
		// $page_data['rec']  = $append_funds;	
        // $page_data['page_name']  = 'fo_expense_report';
        // $page_data['page_title'] = get_phrase('expense_report');
		// $this->load->view('backend/index', $page_data);
// }


function get_per_account_month_transactions_per_icp_in_cluster($epoch_month_end_date = "",$transaction_type = ""){
	
	$result = array();
	
	$month_start_date = date('Y-m-01',$epoch_month_end_date);
	$month_end_date = date('Y-m-t',$epoch_month_end_date);
	
	
	$this->db->join('users','users.fname=voucher_body.icpNo');
	$this->db->join('accounts','accounts.AccNo=voucher_body.AccNo');
	$this->db->select_sum('Cost');
	$this->db->select(array('cname','icpNo','voucher_body.AccNo','AccText'));
	$this->db->group_by(array('voucher_body.AccNo','icpNo'));
	$this->db->order_by('icpNo ASC,accounts.AccNo ASC');
	$icps_with_clusters = $this->db->get_where('voucher_body',
	array('TDate>='=>$month_start_date,'TDate<='=>$month_end_date,
	'AccGrp'=>$transaction_type,'department'=>0,'userlevel'=>1))->result_object();
	
	$has_missing_accounts_for_some_icps = array();
	
	foreach($icps_with_clusters as $icp){
		$has_missing_accounts_for_some_icps[$icp->cname][$icp->icpNo][$icp->AccText] = $icp->Cost;
	}
	
	$accounts_utilized_in_the_month = array();
	
	foreach($has_missing_accounts_for_some_icps as $cluster=>$icps){
		foreach($icps as $icp=>$transactions){
			foreach($transactions as $account=>$amount){
				$accounts_utilized_in_the_month[$account] = $account;
			}
		}
	}
	
	$accounts_utilized_values = array_values($accounts_utilized_in_the_month);
	
	sort($accounts_utilized_values);
 	
	foreach($has_missing_accounts_for_some_icps as $cluster=>$icps){
		foreach($icps as $icp=>$transactions){
			foreach($accounts_utilized_values as $utilized_account){
					if(isset($transactions[$utilized_account])){
						$result[$cluster][$icp][$utilized_account] = $transactions[$utilized_account];
					}else{
						$result[$cluster][$icp][$utilized_account] = 0;
					}
			}
		}			
	}

	
	
	return $result;
}

public function fo_expense_report($month="",$param2="",$param3=""){
	if ($this->session->userdata('admin_login') != 1)
           redirect(base_url().'admin.php', 'refresh');	
    
   	 	$month_closure_date = strtotime(date('Y-m-t'));
		$page_data['tym']  = $month_closure_date;
		
		if($month!=="" && $param2===""){

			$page_data['tym']  = $month;
			$month_closure_date = $month;
		}
		
		if($param3==="prev" || $param3==="next"){
				$sign = '+';
		
				if($param3==='prev'){
					$sign = '-';
				}
				 	
				$page_data['tym']  = strtotime($sign.$param2.' months',$month);
				$month_closure_date = strtotime($sign.$param2.' months',$month);	
		}
	
           
    //$page_data['tym']  = $month;
	$page_data['records']  = $this->get_per_account_month_transactions_per_icp_in_cluster($month_closure_date,0);
	
    $page_data['page_name']  = 'fo_expense_report';
    $page_data['page_title'] = get_phrase('expense_report');
	$this->load->view('backend/index', $page_data);       
}

public function delete_voucher($project="",$voucher_id="",$month=""){
	//echo "Voucher ID ".$voucher_id." delete successful";
	
	$mfr_submitted = $this->finance_model->mfr_submitted($project,date('Y-m-t',$month));
	
	$voucher_number  = $this->db->get_where('voucher_header',array('hID'=>$voucher_id))->row()->VNumber;
	
	$this->db->where("hID >= ",$voucher_id);
	$this->db->where("TDate <= ",date('Y-m-t',$month));
	$this->db->where("icpNo",$project);
	
	$set_of_vouchers = $this->db->get('voucher_header')->result_object();
	
	$msg = "Could not delete the voucher number " .$voucher_number;
	
	if($mfr_submitted=== '0'){
		
		$this->db->delete('voucher_header',array('hID'=>$voucher_id));
		
		$deleted_voucher = array_shift($set_of_vouchers);
		
		if(count($set_of_vouchers)>0){
			
			foreach($set_of_vouchers as $rename){
				
				$current_voucher_pointer = $this->db->get_where('voucher_header',array('hID'=>$rename->hID))->row()->VNumber;
				
				$voucher_frame = substr($current_voucher_pointer, 0,4);
				
				$voucher_serial = substr($current_voucher_pointer, 4)-1; 
				
				$new_voucher_pointer =  $voucher_frame.$voucher_serial." ";
				
				$data['VNumber'] = $new_voucher_pointer;
				
				$this->db->update('voucher_header', $data, array('hID' => $rename->hID));
				
				$this->db->update('voucher_body', $data, array('hID' => $rename->hID));
			}			
		}

		$msg = "Voucher ".$deleted_voucher->VNumber." deleted. ".count($set_of_vouchers)." vouchers re-numbered";
	}	
	
	echo $msg;
	
}

public function delete_all_vouchers($project="",$voucher_id="",$month=""){
		
	$mfr_submitted = $this->finance_model->mfr_submitted($project,date('Y-m-t',$month));
	
	$voucher_number  = $this->db->get_where('voucher_header',array('hID'=>$voucher_id))->row()->VNumber;
	
	$msg = "Could not delete the voucher number " .$voucher_number;
	
	if($mfr_submitted=== '0'){
			
		$num_of_vouchers = $this->db->get_where('voucher_header',array('hID>='=>$voucher_id,'icpNo'=>$project))->num_rows();
		
		$this->db->delete('voucher_header',array('hID>='=>$voucher_id,'icpNo'=>$project));
		
		$this->db->delete('voucher_body',array('hID>='=>$voucher_id,'icpNo'=>$project));
		
		$msg = $num_of_vouchers." vouchers deleted successful";	
	}

	echo $msg;
}

public function validate_mfr($project,$tym,$code){
	if ($this->session->userdata('admin_login') != 1)
            redirect(base_url().'admin.php', 'refresh');
	
	$msg = get_phrase("report_validated");
	
	$data['allowEdit'] = "1";	
	$msg = get_phrase("report_unvalidated");
				 	
	if($code==="1"){
		$data['allowEdit'] = "0";
		$msg = get_phrase("report_validated");
	}
	
	$cond = $this->db->where(array('icpNo'=>$project,'closureDate'=>$tym));
	$this->db->update("opfundsbalheader",$data);
	
	$this->session->set_flashdata('flash_message',$msg);
	
	redirect(base_url().'ifms.php/facilitator/dashboard/'.strtotime($tym),'refresh');	
}

function decline_mfr($month="",$project=""){
	
	$max_mfr = $this->db->select_max('closureDate')->get_where("opfundsbalheader",array("icpNo"=>$project))->row()->closureDate;
	
	//$message = get_phrase("report_declined_successful");
	$msg = "Error Occurred!"; 
	
	//Check if there is a next month MFR present
	
	$next_mfr = $this->db->get_where("opfundsbalheader",array("icpNo"=>$project,"closureDate"=>date('Y-m-t',strtotime('last day of next month',$month))))->num_rows();
	
	$current_mfr = $this->db->get_where("opfundsbalheader",array("icpNo"=>$project,"closureDate"=>date('Y-m-t',$month)))->num_rows();
	
	if($next_mfr!==0){
		$msg = get_phrase("decline_failure._first_decline_the_").$max_mfr." ".get_phrase("report");
	}elseif($current_mfr=== 0){
		$msg = get_phrase("current_month_report_not_submitted");
	}else{	
	
		//Delete Cash Balances
		$this->db->delete('cashbal',array('month'=>date('Y-m-t',$month),'icpNo'=>$project));	
		
		//Delete Statement Balance
		//$this->db->delete('statementbal',array('month'=>date('Y-m-t',$month),'icpNo'=>$project));	
		
		//Unlink Bank Statements
		
		//$this->delete_bank_statement($month, $project);
		
		//Delete Fund Balances
		
		$balHdID = $this->db->get_where('opfundsbalheader',array('icpNo'=>$project,'closureDate'=>date('Y-m-t',$month)))->row()->balHdID;
			
		$this->db->delete('opfundsbalheader',array('balHdID'=>$balHdID));
			
		$this->db->delete('opfundsbal',array('balHdID'=>$balHdID));
		
		
		$msg = date('M Y',$month)." financial report deleted successfully";
	}
	echo $msg;
		
	
}

	public function delete_bank_statement($param1,$param2){
		//$t= $_POST['name'];
		$storeFolder = 'uploads/bank_statements/'.$param2.'/'.date('Y-m',$param1).'/';  
		//unlink($storeFolder);
		foreach (glob($storeFolder."/*.*") as $filename) {
			if (is_file($filename)) {
			       unlink($filename);
			}
		}
	}
    
function plans($param1='',$param2='',$param3=''){
		 if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
		 

		$page_data['fyr'] = get_fy(date('Y-m-d',$param1),$param2);
		$page_data['tym'] = $param1;
		$page_data['project'] = $param2;
        $page_data['page_name']  = 'plans';
        $page_data['page_title'] = get_phrase('project_budget');
		$this->load->view('backend/index', $page_data);	        	
	}
	
public function multiple_vouchers($tym,$project){
	if ($this->session->userdata('admin_login') != 1)
	      redirect(base_url(), 'refresh');
	
	$post = $this->input->post();

	$page_data['vouchers']= $post;
	
	$page_data['page_name']  = 'multiple_vouchers';
	$page_data['project'] = $project;
	$page_data['tym'] = $tym;
    $page_data['page_title'] = get_phrase('multiple_vouchers');
    $this->load->view('backend/index', $page_data);	

}	

// Cron Job Functions


function cron_insert_parameters_dashboard_table(){
	
	ini_set('memory_limit', '1024M');

	$result = [];

	$last_run_obj = $this->db->select_max('month')->get('dashboard_run');

	$date1 = strtotime($this -> config -> item('dashboard_runs_start_date'));//strtotime('2017-06-01');
	$date2 = strtotime($this->dashboard_model->get_max_dashboard_run_date());//strtotime('2018-02-01');

	// $date1 = strtotime('2018-02-01');
	// $date2 = strtotime('2018-04-30');

	$count_of_fcps = $this->db->get_where('projectsdetails',array('status'=>1))->num_rows();

	while ($date1 <= $date2) {
		

		$month = date('Y-m-t', $date1);

		// Check if the month has a run and update the dashboard_run table
		$run_obj = $this->db->get_where('dashboard_run',array("month"=>$month));

		// Count of FCP in a dashboard
		$this->db->join('dashboard_run','dashboard_run.dashboard_run_id=dashboard_header.dashboard_run_id');
		$fcp_in_dashboard = $this->db->get_where('dashboard_header',array("month"=>$month))->num_rows();

		if($this->config->item('fcp_test_loops') > 0){
			$fcp_in_dashboard = $this->config->item('fcp_test_loops');
		}

		// Check if their is a change for the month
		$month_change = $this->db->get_where('dashboard_change',array('status'=>1,'month'=>$month));

		if(	
				($run_obj->num_rows() == 0 && $month_change->num_rows() == 0) || // No run and no change
				($run_obj->num_rows() == 1 && ($month_change->num_rows() > 0 || $fcp_in_dashboard < $count_of_fcps)) // There is a run and a change or FCP in dashboard are less than the count of FCPs that are active
			) 
		{
			$result = $this->finance_dashboard->build_dashboard_array($month,$count_of_fcps,$fcp_in_dashboard);
		}

		$date1 = strtotime('+1 month', $date1);

	}

	echo json_encode($result);

}


function send_dashboard_message(){
	//echo json_encode($this->input->post());

	$post = $this->input->post();
	$projectsdetails_id = $this->db->get_where('projectsdetails',array('icpNo'=>$post['fcp_id']))->row()->ID;
	$message_trail_for_center_in_month = $this->db->get_where('dashboard_messaging',array('projectsdetails_id'=>$projectsdetails_id,'dashboard_month'=>$post['month']));
	$dashboard_messaging_id = 0;
	$json_data['success'] = true;

	$this->db->trans_start();
	// Insert dashboard message if not existing for project and month 
	if($message_trail_for_center_in_month->num_rows() == 0){
		$insert_data['projectsdetails_id'] = $projectsdetails_id;
		$insert_data['dashboard_month'] = $post['month'];
		$insert_data['created_date'] = date('Y-m-d');
		$insert_data['create_by'] = $this->session->login_user_id;
		$insert_data['last_modified_by'] = $this->session->login_user_id;

		$this->db->insert('dashboard_messaging',$insert_data);

		$dashboard_messaging_id = $this->db->insert_id();
	}else{
		$dashboard_messaging_id = $message_trail_for_center_in_month->row()->dashboard_messaging_id;
	}

	$to_id = $this->_get_center_pf($post['fcp_id']);

	$insert_detail_data['dashboard_messaging_id'] = $dashboard_messaging_id;
	$insert_detail_data['message_from'] = $this->session->login_user_id;
	$insert_detail_data['message_to'] = $to_id; 
	$insert_detail_data['message_body'] = $post['message'];
	$insert_detail_data['create_date'] = date('Y-m-d');
	$insert_detail_data['created_by'] = $this->session->login_user_id;
	$insert_detail_data['last_modified_by'] = $this->session->login_user_id;

	$this->db->insert('dashboard_messaging_detail',$insert_detail_data);

	$json_data['message'] = $post['message'];
	$json_data['sender_name'] = $this->session->userfirstname.' '.$this->session->userlastname;
	$json_data['send_date'] = date('jS M Y');

	$this->db->trans_complete();
	
	if($this->db->trans_status() == false){
		$json_data['success'] = false;
	}else{
		// Send an email to the recepient (PF)
		if(parse_url(base_url())['host'] != 'localhost'){
			$this->email_model->send_dashboard_notification($this->session->login_user_id, $to_id, $post['message']);
		}
	}	
	

	echo json_encode($json_data);


}

private function _get_center_pf($fcp_id){
	$fcp_cluster = $this->db->select('cname')->get_where('users',array('fname'=>$fcp_id,'userlevel'=>1,'department'=>0))->row()->cname;
	$pf_id = $this->db->get_where('users',array('cname'=>$fcp_cluster,'userlevel'=>2,'auth'=>1))->row()->ID;
	return $this->config->item('use_test_recipient_id_mfr_review_notification') > 0?$this->config->item('use_test_recipient_id_mfr_review_notification'):$pf_id;
}

function bank_statements($param1="",$param2="",$param3=""){
	if ($this->session->userdata('admin_login') != 1)
	   redirect(base_url(), 'refresh');

   $page_data['uploaded_bank_statement'] = $this->finance_model->get_uploaded_bank_statement(date('Y-m-t',$param1),$param2);		   
   $page_data['page_name']  = 'bank_statements_upload';
   $page_data['tym'] = $param1;
   $page_data['project'] = $param2;;
   $page_data['page_title'] = get_phrase('bank_statements');
   $this->load->view('backend/index', $page_data);		
}	

public function bank_statement_download($param1,$param2,$param3){
   force_download('uploads/bank_statements/'.$param3.'/'.date('Y-m',$param2).'/'.$param1,NULL);
}


function fund_balance_grid($month){
	$this->db->select(array('icpNo'));
	$centers = $this->db->get_where('projectsdetails',array('status'=>1))->result_array();

	$opening = $this->finance_model->months_opening_fund_balances_for_centers($month);
	$income = $this->finance_model->months_income_per_revenue_account_for_centers($month);
	$expense = $this->finance_model->months_expense_per_revenue_account_for_centers($month);

	$revenue_accounts = $this->finance_model->ordered_revenue_accounts();

	$return_grid = [];

	foreach($centers as $center){
		foreach($revenue_accounts as $account){
			$loop_opening = isset($opening[$center['icpNo']][$account])?$opening[$center['icpNo']][$account]:0;
			$loop_income = isset($income[$center['icpNo']][$account])?$income[$center['icpNo']][$account]:0;
			$loop_expense = isset($expense[$center['icpNo']][$account])?$expense[$center['icpNo']][$account]:0;
			$loop_closing = $loop_opening + $loop_income - $loop_expense;

			if($loop_closing != 0 ){
				$return_grid[$center['icpNo']][$account]['revenue_account_opening_balance'] = $loop_opening;
				$return_grid[$center['icpNo']][$account]['revenue_account_income'] = $loop_income;
				$return_grid[$center['icpNo']][$account]['revenue_account_expense'] = $loop_expense;
				$return_grid[$center['icpNo']][$account]['revenue_account_closing_balance'] = $loop_closing;
			}
			
		}
	}

	return $return_grid;
}

function fund_balance_report($month = ""){
	if ($this->session->userdata('admin_login') != 1)
	   redirect(base_url(), 'refresh');

	$month = $month == ""?strtotime(date('Y-m-t')):$month;
	
	$month_date_format = date('Y-m-t',$month);
	
	$result = $this->fund_balance_grid($month_date_format);

	$balance_type = "revenue_account_closing_balance";

	if( $this->input->post('balance_type') != null ) {
		$balance_type = $this->input->post('balance_type');
	}
	   
	$page_data['result'] = $result;
	$page_data['month'] = $month;
	$page_data['balance_type'] = $balance_type;
	$page_data['revenue_accounts'] = $this->finance_model->ordered_revenue_accounts(); 			   
	$page_data['page_name']  = 'fund_balance_report';
   	$page_data['page_title'] = get_phrase('fund_balance_report');
   	$this->load->view('backend/index', $page_data);	
}

function get_account_title(){
	$post = $this->input->post();

	echo $this->db->get_where('accounts',array('AccNo'=>$post['account_number']))->row()->AccName;
}

  
}