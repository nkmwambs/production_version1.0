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

class Partner extends CI_Controller
{
    
    
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->load->model('dct_model');
		$this->load->model('finance_model');
		
       /*cache control*/
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');

		
    }
    
    /***default functin, redirects to login page if no admin logged in yet***/
    public function index()
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url().'admin.php/login', 'refresh');
    }
    
    /***Load Default Pages***/
    function dashboard()
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
		
		//$max_mfr_id = $this->db->select_max('balHdID')->get_where('opfundsbalheader',array('icpNo'=>$this->session->center_id))->row()->balHdID;
		 	
		//$page_data['tym']  = strtotime($this->finance_model->current_financial_month($this->session->center_id));	
        $page_data['page_name']  = 'dashboard';
        $page_data['page_title'] = get_phrase('finance_dashboard');
        $this->load->view('backend/index', $page_data);
    }
    
  function cash_journal($param1 = '', $param2 = '', $param3 = ''){
		 if ($this->session->userdata('admin_login') != 1)
            redirect(base_url().'admin.php', 'refresh');
		
		
		$period_time_stamp = strtotime($this->finance_model->current_financial_month($this->session->center_id));

		$page_data['cash_journal'] = $this->cash_journal_grid($period_time_stamp);
		$page_data['tym']  = strtotime($this->finance_model->current_financial_month($this->session->center_id));//strtotime('+1 month',strtotime($last_mfr->closureDate));		
        $page_data['month'] = date("Y-m-t",strtotime($this->finance_model->current_financial_month($this->session->center_id)));
        $page_data['page_name']  = 'cash_journal';
        $page_data['page_title'] = get_phrase('cash_journal');
		$this->load->view('backend/index', $page_data);
}


private function cash_journal_grid($period_time_stamp){

	$cash_journal = [];

	$end_period_date = date("Y-m-t",$period_time_stamp);

	$is_bank_reconciled = $this->finance_model->bank_reconciled($this->session->center_id,$end_period_date) <> 0 ? false : true;
	$is_proof_of_cash_correct = $this->finance_model->proof_of_cash($this->session->center_id,$end_period_date) <> 0 ? false : true;
	$is_mfr_submitted = $this->finance_model->mfr_submitted($this->session->center_id,$end_period_date) == 1 ? true : false;

	$vouchers = $this->finance_model->list_month_vouchers_for_fcp($this->session->center_id,$period_time_stamp);

	$bank_balance_brought_forward = $this->finance_model->opening_bank_balance($end_period_date,$this->session->center_id);;
	$bank_deposit = 0;
	$bank_payment = 0;
	$bank_closing_balance = $bank_balance_brought_forward;

	$cash_balance_brought_forward = $this->finance_model->opening_pc_balance($end_period_date,$this->session->center_id);;
	$cash_deposit = 0;
	$cash_payment = 0;
	$cash_closing_balance = $cash_balance_brought_forward;

	$cash_journal['period'] = $end_period_date;
	$cash_journal['is_bank_reconciled'] = $is_bank_reconciled;
	$cash_journal['is_proof_of_cash_correct'] = $is_proof_of_cash_correct;
	$cash_journal['is_mfr_submitted'] = $is_mfr_submitted;
	

	if(!empty($vouchers)){

		$cash_journal['month_utilized_income_accounts'] = [];
		$cash_journal['month_utilized_expense_accounts'] = [];

		foreach($vouchers as $voucher){

			$bank_deposit += $voucher['voucher_type'] == 'CR' || $voucher['voucher_type'] == 'PCR' ? $voucher['Cost'] : 0; 
			$bank_payment += $voucher['voucher_type'] == 'CHQ' || $voucher['voucher_type'] == 'BCHG' || $voucher['voucher_type'] == 'UDCTB' ? $voucher['Cost'] : 0;
			
			$cash_deposit += $voucher['account_number'] == '2000' || $voucher['account_number'] == '2001' ? $voucher['Cost'] : 0; 
			$cash_payment += $voucher['voucher_type'] == 'PC' || $voucher['voucher_type'] == 'PCR' || $voucher['voucher_type'] == 'UDCTC' ? $voucher['Cost'] : 0; 
			
			$cash_journal['voucher_records'][$voucher['voucher_id']] = [
				'voucher_number' => $voucher['voucher_number'],
				'voucher_date' => $voucher['voucher_date'],
				'voucher_type' => $voucher['voucher_type'],
				'payee' => $voucher['payee'],
				'description' => $voucher['description'],
				'cheque_number' => $voucher['cheque_number'],
				'clear_state' => $voucher['clear_state'],
				'clear_month' => $voucher['clear_month'],
				'is_editable' => $voucher['is_editable']
			];

			if($voucher['account_group'] == 1){
				
				$cash_journal['month_utilized_income_accounts'][$voucher['account_number']] = [
					'account_code' => $voucher['account_code'],
					'account_name' => $voucher['account_name']
				];

				if(isset($cash_journal['voucher_records'][$voucher['voucher_id']]['running_balance']['income'])){
					$cash_journal['voucher_records'][$voucher['voucher_id']]['running_balance']['income'] += $voucher['Cost'];
				}else{
					$cash_journal['voucher_records'][$voucher['voucher_id']]['running_balance']['income'] = $voucher['Cost'];
				}
				
				
			}elseif($voucher['account_group'] == 0){
				
				$cash_journal['month_utilized_expense_accounts'][$voucher['account_number']] = [
					'account_code' => $voucher['account_code'],
					'account_name' => $voucher['account_name']
				];		

			}
			
		}

		foreach($vouchers as $voucher){
			$cash_journal['voucher_records'][$voucher['voucher_id']]['spread'][$voucher['account_number']] = $voucher['Cost'];
		}

		$bank_closing_balance = $bank_balance_brought_forward + $bank_deposit - $bank_payment;

		$cash_closing_balance = $cash_balance_brought_forward + $cash_deposit - $cash_payment;

	}

	$cash_journal['bank'] = [
		'balance_bf' => $bank_balance_brought_forward,
		'deposit' =>  $bank_deposit,
		'payment' => $bank_payment,
		'closing_balance' => $bank_closing_balance
	];
	
	$cash_journal['cash'] = [
		'balance_bf' =>  $cash_balance_brought_forward,
		'deposit' => $cash_deposit,
		'payment' => $cash_payment,
		'closing_balance' => $cash_closing_balance
	];

	return $cash_journal;
}
  
  function scroll_cash_journal($date="",$cnt="",$flag=""){
  	
		 if ($this->session->userdata('admin_login') != 1)
             redirect(base_url().'admin.php', 'refresh');
		
	
		 if($flag !== ""){
 		
			 $sign = '+';
 			
			 if($flag == 'prev'){
				 $sign = '-';
			}
 			 	
			 $tym  = strtotime('first day of '.$sign.$cnt.'month',$date);

			 $page[$tym] = $tym;
			 //$page_data['month']  = date("Y-m-t",strtotime('first day of '.$sign.$cnt.'month',$date));
			 $page_data['cash_journal'] = $this->cash_journal_grid($tym); 				
		}else{
			 $tym  = $date;
			 $page[$tym] = $tym;	
			 $page_data['cash_journal'] = $this->cash_journal_grid($tym); 
			 //$page_data['month']  = date("Y-m-t",$date);	
		 }

			
        $page_data['page_name']  = 'cash_journal';
        $page_data['page_title'] = get_phrase('cash_journal');
		$this->load->view('backend/index', $page_data);

}  

public function correct_error($param1="",$param2=""){
		 if ($this->session->userdata('admin_login') != 1)
            redirect(base_url().'admin.php', 'refresh');
			
			$bank_bal_error = $this->finance_model->bank_cash_error($param1,$param2);
			$pc_bal_error = $this->finance_model->petty_cash_error($param1,$param2);
			$fund_bal_error = $this->finance_model->fund_balance_error($param1,$param2);

				if(number_format($bank_bal_error,2)<>0){
					
					//$rec = $this->db->get_where('cashbal',array('icpNo'=>$param1,'month>='=>date('Y-m-t',$param2),'accNo'=>'BC'))->result_object();
					
					//foreach($rec as $row){
						
						if($bank_bal_error<0) $data['amount'] = $this->finance_model->bank_balance(date('Y-m-t',$param2),$param1) .''. $bank_bal_error;
						
						if($bank_bal_error>0) $data['amount'] = $this->finance_model->bank_balance(date('Y-m-t',$param2),$param1) .'+'. $bank_bal_error;
						
					
						$this->db->where(array('icpNo'=>$param1,'month'=>date('Y-m-t',$param2),'accNo'=>'BC'))->update('cashbal',$data);	
					//}
					
				}
				
				if(number_format($pc_bal_error,2)<>0){
					
					//$rec = $this->db->get_where('cashbal',array('icpNo'=>$param1,'month>='=>date('Y-m-t',$param2),'accNo'=>'PC'))->result_object();
				
					//foreach($rec as $row){
						
						if($pc_bal_error<0) $data['amount'] = $this->finance_model->petty_cash_balance(date('Y-m-t',$param2),$param1) .''. $pc_bal_error;
						
						if($pc_bal_error>0) $data['amount'] = $this->finance_model->petty_cash_balance(date('Y-m-t',$param2),$param1) .'+'. $pc_bal_error;
						
					
						$this->db->where(array('icpNo'=>$param1,'month'=>date('Y-m-t',$param2),'accNo'=>'PC'))->update('cashbal',$data);	
					//}
				}
				
		//$page_data['tym']  = strtotime("last day of next month",$param2);	
        //$page_data['page_name']  = 'cash_journal';
        //$page_data['page_title'] = get_phrase('cash_journal');
		//$this->load->view('backend/index', $page_data);	
		redirect(base_url().'ifms.php/partner/scroll_cash_journal/'.$param2.'/1/next');
}

function new_budget_item($param1=""){
	if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
	
	if($param1=='scroll'){
		$page_data['cur_fy'] = $this->input->post('cur_fy');
	}
	
	$page_data['page_name']  = 'new_budget_item';
    $page_data['page_title'] = get_phrase('budget_item');
	$this->load->view('backend/index', $page_data);	
}

function plans($param1='',$param2='',$param3=''){
		 if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
		 
		 
		 if($param1==='clone'){
			
			if(count($this->db->get_where('planheader',array('fy'=>$this->input->post('nextfy'),'icpNo'=>$param2))->row())===0){
				$data['fy'] = $this->input->post('nextfy');
				$data['icpNo'] = $param2;
				$this->db->insert('planheader',$data);
			}
			
			$planHeaderID = $this->db->get_where('planheader',array('icpNo'=>$param2,'fy'=>$this->input->post('fy')))->row()->planHeaderID;
			
			$new_planHeaderID = $this->db->get_where('planheader',array('icpNo'=>$param2,'fy'=>$this->input->post('nextfy')))->row()->planHeaderID;
			
			$fy_items = $this->db->get_where('plansschedule',array('planHeaderID'=>$planHeaderID))->result();
			
			$nextfy_items = array();
			
			foreach($fy_items as $item):
				$arr = (array)$item;
				
				array_shift($arr);
				
				$arr['planHeaderID'] = $new_planHeaderID;
				
				$arr['approved'] = '0';
				
				$nextfy_items[] = $arr;
			endforeach;
			
			//Check if already items exist 
			
			if(count($this->db->get_where('plansschedule',array('planHeaderID'=>$new_planHeaderID))->result())>0){
				$this->session->set_flashdata('flash_message',get_phrase('cloning_failed'));					
			}else{
				$this->db->insert_batch('plansschedule',$nextfy_items);
				$this->session->set_flashdata('flash_message',get_phrase('budget_cloned'));
			}
			
			$page_data['fyr'] = $this->input->post('nextfy');
			redirect(base_url().'ifms.php/partner/scroll_budget_schedules/'.$this->input->post('nextfy'),'refresh');
		}
		
		if($param1==='duplicate'){
			
			$origin  = $this->db->get_where('plansschedule',array('scheduleID'=>$param2))->row();
			
			$fy = $this->db->get_where('planheader',array('planHeaderID'=>$origin->planHeaderID))->row()->fy;
			
			clone_record('plansschedule','scheduleID', $param2,array('approved'));
			$this->session->set_flashdata('flash_message',get_phrase('record_duplicated'));
					
			redirect(base_url().'ifms.php/partner/budget_schedules/','refresh');		
		}
		
		if($param1==='edit'){
			
			$planHeaderID = $this->db->get_where('plansschedule',array('scheduleID'=>$param2))->row()->planHeaderID;
			
						
			$header['fy'] = $this->input->post('fy');
			$header['icpNo'] = $param3;
			
			$this->db->where(array('planHeaderID'=>$planHeaderID))->update('planheader',$header);
			
						
			$body['planHeaderID'] = $planHeaderID;
			$body['AccNo'] = $this->input->post('AccNo');
			$body['plan_item_tag_id'] = $this->input->post('plan_item_tag_id');
			$body['details'] = $this->input->post('details');
			$body['qty'] = $this->input->post('qty');
			$body['unitCost'] = $this->input->post('unitCost');
			$body['often'] = $this->input->post('often');
			$body['totalCost'] = $this->input->post('totalCost');
			$body['notes'] = $this->input->post('notes');
			
			//$range = range(0,11);
			$fields_range = array('month_1_amount','month_2_amount','month_3_amount','month_4_amount','month_5_amount','month_6_amount','month_7_amount','month_8_amount','month_9_amount','month_10_amount','month_11_amount','month_12_amount');
			//$start_month = $this->db->get_where('projectsdetails',array('project_id'=>$param3))->row()->system_start_date;
			
			//$start_month = $this->finance_model->project_system_start_date($param2);
			
			foreach($fields_range as $month):
				$body[$month] = $this->input->post($month);
			endforeach;
			
			

				$this->db->where(array('scheduleID'=>$param2))->update('plansschedule',$body);
				
				//$this->session->set_flashdata('flash_message',get_phrase('record_edited'));
		
			//redirect(base_url().'ifms.php/partner/plans','refresh');
			
			$parentAccID = $this->db->get_where('accounts',array('AccNo'=>$this->input->post('AccNo')))->row()->parentAccID;
			
			$budgeted = $this->db->get_where('accounts',array('accID'=>$parentAccID));
			
			$page_data['budgeted'] = $budgeted->result_object();
			$page_data['fyr'] = $this->input->post('fy'); 
			echo $this->load->view('backend/partner/load_budget_schedules',$page_data,TRUE);
		}
		if($param1==='delete'){
			
			$origin  = $this->db->get_where('plansschedule',array('scheduleID'=>$param2))->row();
			
			$fy = $this->db->get_where('planheader',array('planHeaderID'=>$origin->planHeaderID))->row()->fy;
			
			$this->db->where(array('scheduleID'=>$param2))->delete('plansschedule');
			
			if($this->db->affected_rows()>0){
				$this->session->set_flashdata('flash_message',get_phrase('record_deleted'));
			}else{
				$this->session->set_flashdata('flash_message',get_phrase('no_record_deleted'));
			}
			
			redirect(base_url().'ifms.php/partner/scroll_budget_schedules/'.$fy,'refresh');
		}
		if($param1==='mass_submit'){
				
			$fy = $this->db->get_where('planheader',array('planHeaderID'=>$param2))->row()->fy;
			
			$data['approved'] = '1';
			
			$this->db->where(array('planHeaderID'=>$param2,'approved'=>'0'))->update('plansschedule',$data);
			
			if($this->db->affected_rows()>0){
				$this->session->set_flashdata('flash_message',get_phrase('items_submitted_for_approval'));
			}else{
				$this->session->set_flashdata('flash_message',get_phrase('no_items_submitted_for_approval'));
			}
				
			//$this->session->set_flashdata('flash_message',get_phrase('items_submitted_for_approval'));
			redirect(base_url().'ifms.php/partner/scroll_budget_schedules/'.$fy,'refresh');
		}	
		if($param1==='item_submit'){
			
			$origin  = $this->db->get_where('plansschedule',array('scheduleID'=>$param2))->row();
			
			$fy = $this->db->get_where('planheader',array('planHeaderID'=>$origin->planHeaderID))->row()->fy;
			
			$data['approved'] = '1';
			$data['submitDate'] = date('Y-m-d');
			
			$this->db->where(array('scheduleID'=>$param2,'approved'=>'0'))->update('plansschedule',$data);
			
			if($this->db->affected_rows()>0){
				$this->session->set_flashdata('flash_message',get_phrase('item_submitted_for_approval'));
			}else{
				$this->session->set_flashdata('flash_message',get_phrase('no_item_submitted_for_approval'));
			}
			
			//$this->session->set_flashdata('flash_message',get_phrase('item_submitted_for_approval'));
			redirect(base_url().'ifms.php/partner/budget_schedules/','refresh');
		}	

		if($param1==='delete_budget'){
			$fy = $this->db->get_where('planheader',array('planHeaderID'=>$param2))->row()->fy;
			
			$this->db->where(array('planHeaderID'=>$param2,'approved'=>0))->delete('plansschedule');
			
			if($this->db->affected_rows()>0){
				$this->session->set_flashdata('flash_message',get_phrase('new_budget_items_deleted'));
			}else{
				$this->session->set_flashdata('flash_message',get_phrase('no_budget_items_deleted'));
			}
			
			
			redirect(base_url().'ifms.php/partner/scroll_budget_schedules/'.$fy,'refresh');
		}		

		
		$page_data['fyr'] = $this->finance_model->current_fy($this->session->center_id);
        $page_data['page_name']  = 'budget_schedules';
        $page_data['page_title'] = get_phrase('project_budget');
		$this->load->view('backend/index', $page_data);	        	
	}
function edit_budget_item($param1="",$param2="",$param3=""){
			
			$planHeaderID = $this->db->get_where('plansschedule',array('scheduleID'=>$param1))->row()->planHeaderID;
			
						
			//$header['fy'] = $this->input->post('fy');
			//$header['icpNo'] = $param2;
			
			//$this->db->where(array('planHeaderID'=>$planHeaderID))->update('planheader',$header);
			
						
			$body['planHeaderID'] = $planHeaderID;
			$body['AccNo'] = $this->input->post('AccNo');
			$body['plan_item_tag_id'] = $this->input->post('plan_item_tag_id');
			$body['details'] = $this->input->post('details');
			$body['qty'] = $this->input->post('qty');
			$body['unitCost'] = $this->input->post('unitCost');
			$body['often'] = $this->input->post('often');
			$body['totalCost'] = $this->input->post('totalCost');
			$body['notes'] = $this->input->post('notes');
			
			if($param3==='3'){
				$body['approved'] = "4";
			}
			
			
			//$range = range(0,11);
			$fields_range = array('month_1_amount','month_2_amount','month_3_amount','month_4_amount','month_5_amount','month_6_amount','month_7_amount','month_8_amount','month_9_amount','month_10_amount','month_11_amount','month_12_amount');
			//$start_month = $this->db->get_where('projectsdetails',array('project_id'=>$param3))->row()->system_start_date;
			
			//$start_month = $this->finance_model->project_system_start_date($param2);
			
			foreach($fields_range as $month):
				$body[$month] = $this->input->post($month);
			endforeach;
			
			

				$this->db->where(array('scheduleID'=>$param1))->update('plansschedule',$body);
				
				//$this->session->set_flashdata('flash_message',get_phrase('record_edited'));
		
			//redirect(base_url().'ifms.php/partner/plans','refresh');
			
			$parentAccID = $this->db->get_where('accounts',array('AccNo'=>$this->input->post('AccNo')))->row()->parentAccID;
			
			$budgeted = $this->db->get_where('accounts',array('accID'=>$parentAccID));
			
			//$page_data['budgeted'] = $budgeted->row();
			$page_data['account_id'] = $this->db->get_where("accounts",array("AccNo"=>$this->input->post('AccNo')))->row()->accID;
			$page_data['fyr'] = $this->input->post('fy'); 
			echo $this->load->view('backend/partner/load_schedules_account',$page_data,TRUE);
		}

function budget_item_delete($param1=""){
			
			$origin  = $this->db->get_where('plansschedule',array('scheduleID'=>$param1))->row();
			
			$fy = $this->db->get_where('planheader',array('planHeaderID'=>$origin->planHeaderID))->row()->fy;
			
			$this->db->where(array('scheduleID'=>$param1))->delete('plansschedule');
			
			
			$page_data['account_id'] = $this->db->get_where("accounts",array("AccNo"=>$origin->AccNo))->row()->accID;
			$page_data['fyr'] = $fy; 
			echo $this->load->view('backend/partner/load_schedules_account',$page_data,TRUE);
		
}

function budget_item_submit($param1=""){
			$origin  = $this->db->get_where('plansschedule',array('scheduleID'=>$param1))->row();
			
			$fy = $this->db->get_where('planheader',array('planHeaderID'=>$origin->planHeaderID))->row()->fy;
			
			$data['approved'] = '1';
			$data['submitDate'] = date('Y-m-d');
			
			$this->db->where(array('scheduleID'=>$param1,'approved'=>'0'))->update('plansschedule',$data);
			
			$page_data['account_id'] = $this->db->get_where("accounts",array("AccNo"=>$origin->AccNo))->row()->accID;
			$page_data['fyr'] = $fy; 
			echo $this->load->view('backend/partner/load_schedules_account',$page_data,TRUE);
}

function budget_limits(){
		 if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
		 
		 
		$page_data['fyr'] = $this->finance_model->current_fy($this->session->center_id);
        $page_data['page_name']  = 'budget_limits';
        $page_data['page_title'] = get_phrase('budget_limits');
		$this->load->view('backend/index', $page_data);	 	
}

function budget_summary(){
		 if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
		 
		$fyr = $this->finance_model->current_fy($this->session->center_id);
		$page_data['fyr'] = $fyr;
		$page_data['budget_summary'] = $this->finance_model->budget_spread_grid_by_income_accounts($fyr,$this->session->center_id);
        $page_data['months_range'] = ['July','August','September','October','November','December',
		'January','February','March','April','May','June'];
		$page_data['page_name']  = 'budget_summary';
        $page_data['page_title'] = get_phrase('budget_summary');
		$this->load->view('backend/index', $page_data);		
}

function scroll_budget_summary($fy){
		 if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

		$page_data['fyr'] = $fy;
		$page_data['budget_summary'] = $this->finance_model->budget_spread_grid_by_income_accounts($fy,$this->session->center_id);
        $page_data['months_range'] = ['July','August','September','October','November','December',
		'January','February','March','April','May','June'];
        $page_data['page_name']  = 'budget_summary';
        $page_data['page_title'] = get_phrase('budget_summary');
		$this->load->view('backend/index', $page_data);	 	
}

function load_variance_explanation($center_id,$income_account_number,$month){

	$acc = $this->db->get_where('accounts',array("AccNo"=>$income_account_number))->row();

	$page_data['acc'] = $acc;
	$page_data['month'] = $month;
	$page_data['expense_account'] = $this->finance_model->expense_accounts($acc->accID);
	$page_data['expense_report_grid']= $this->expense_report_grid($center_id,$month,$income_account_number);
	$page_data['budget_spread_grid'] = $this->finance_model->budget_spread_grid(get_fy($month),$center_id,$acc->accID,$month);
	
	echo $this->load->view('backend/partner/variance_explanation_data', $page_data,TRUE);	
}

function budget_schedules(){
		 if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
		 
		

		$page_data['fyr'] = $this->finance_model->current_fy($this->session->center_id);
        $page_data['page_name']  = 'budget_schedules';
        $page_data['page_title'] = get_phrase('budget_schedules');
		$this->load->view('backend/index', $page_data);			
}

function load_budget_schedule_account(){
	
	$page_data['account_id'] = $this->input->post("accID");
	$page_data['fyr'] = $this->input->post("fy");
	
	echo $this->load->view('backend/partner/load_schedules_account', $page_data,TRUE);
	
}

function scroll_budget_schedules($fy){
		 if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

		$page_data['fyr'] = $fy;
        $page_data['page_name']  = 'budget_schedules';
        $page_data['page_title'] = get_phrase('budget_schedules');
		$this->load->view('backend/index', $page_data);	 	
}

function scroll_plan($fy){
		 if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

		$page_data['fyr'] = $fy;
        $page_data['page_name']  = 'plans';
        $page_data['page_title'] = get_phrase('project_budget');
		$this->load->view('backend/index', $page_data);	 		 	
}
function funds_disbursement($param1 = '', $param2 = '', $param3 = ''){
		 if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
		 

        $page_data['page_name']  = 'funds_disbursement';
        $page_data['page_title'] = get_phrase('funds_disbursements');
        $this->load->view('backend/index', $page_data);
	}

function accounts_chart($param1=""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
		
        $page_data['page_name']  = 'accounts_chart';
        $page_data['page_title'] = get_phrase('accounts_chart');
        $this->load->view('backend/index', $page_data);		
	}

/**Financial Reports **/

	function financial_report($param1="",$param2=""){
	        if ($this->session->userdata('admin_login') != 1)
	            redirect(base_url(), 'refresh');
			
	        $page_data['page_name']  = 'financial_report';
	        $page_data['page_title'] = get_phrase('financial_report');
	        $this->load->view('backend/index', $page_data);		
	}

	
	function scroll_financial_report($param1="",$param2=""){
		$data['month'] = date("Y-m-t",strtotime($param2." months",strtotime($param1)));
		echo $this->load->view('backend/partner/load_financial_report', $data,TRUE);
	}
	

		

	function financial_reports($param1='',$param2='',$project=""){
	 if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
	 
	 	if($param1==='fund_balance'){
	 		
			//Check if bal exists
			
			$chk = $this->db->get_where('opfundsbalheader',array('closureDate'=>$param2,'project_id'=>$project))->result_array();
	 		
			
			$fund = $this->input->post('fund');
			
			$total_fund = array_sum($fund);
			
			$data['project_id'] = $project;
			$data['totalBal'] = $total_fund;
			$data['closureDate'] = $param2;
			$data['allowEdit'] = 0;
			$data['submitted'] = 0;
			$data['systemOpening'] = 0;
			
			if(empty($chk)){
				$this->db->insert('opfundsbalheader',$data);
				
				$hid = $this->db->insert_id();
				
				foreach($fund as $acc=>$val):
					if($val<>0){
						$data2['balHdID'] = $hid;
						$data2['funds'] = $acc;
						$data2['amount'] = $val;
						$this->db->insert('opfundsbal',$data2);
					}
					
				endforeach;
				
			}else{
				$this->db->where(array('closureDate'=>$param2,'project_id'=>$project));
				$this->db->update('opfundsbalheader',$data);
				
				//Get ID 
				
				$hid = $this->db->get_where('opfundsbalheader',array('closureDate'=>$param2,'project_id'=>$project))->row()->balHdID;
				
				
				foreach($fund as $acc=>$val):
					
					if($this->db->get_where('opfundsbal',array('funds'=>$acc,'balHdID'=>$hid))->num_rows()==0){
						if($val<>0){
							$data4['balHdID'] = $hid;
							$data4['funds'] = $acc;
							$data4['amount'] = $val;
							$this->db->insert('opfundsbal',$data4);
						}
					}
					
					if($val<>0){
						$this->db->where(array('balHdID'=>$hid,'funds'=>$acc));
						
						$data2['amount'] = $val;
					
						$this->db->update('opfundsbal',$data2);	
					}
					
				endforeach;
				
			}
			
			$this->session->set_flashdata('flash_message', get_phrase('operation_successful'));
            redirect(base_url() . 'ifms.php/partner/cash_journal/'.strtotime($param2), 'refresh');		 		
	 	}
		
		if($param1=='bank_reconcile'){
			
			$data['month'] = $param2;//$this->input->post('bsDate');
			$data['statementDate'] = $param2;
			$data['actualDate'] = $this->input->post('bsDate');
			$data['icpNo'] = $project;
			$data['amount'] = $this->input->post('bankBal');
			
			//check if balance already inserted
			
			$chk = $this->db->get_where('statementbal',array('month'=>$param2,'icpNo'=>$project))->result();
			
			if(count($chk)>0){
				$this->db->where(array('month'=>$param2,'icpNo'=>$project))->update('statementbal',$data);
			}else{
				$this->db->insert('statementbal',$data);
			}
			
			
			
			$this->session->set_flashdata('flash_message', get_phrase('operation_successful'));
            redirect(base_url() . 'ifms.php/partner/cash_journal/'.strtotime($this->input->post('bsDate')), 'refresh');			
		}
		/**if($param1==='cash_proof'){
			
		//Check if cash bal exist
		
		$chk = $this->db->get_where('cashbal',array('month'=>$param2))->result_array();
			
				$data[0]['month'] = $param2;
				$data[0]['project_id'] = $project;
				$data[0]['AccNo'] = 'BC';
				$data[0]['amount'] = $this->input->post('BC');
				
				$data[1]['month'] = $param2;
				$data[1]['project_id'] = $project;
				$data[1]['AccNo'] = 'PC';
				$data[1]['amount'] = $this->input->post('PC');
			
			if(empty($chk)){
				$this->db->insert_batch('cashbal',$data);
			}else{
				$cnt = 0;
				foreach($data as $row):
					$d['amount'] = $data[$cnt]['amount'];	

					$this->db->where(array('month'=>$param2,'project_id'=>$project,'AccNo'=>$data[$cnt]['AccNo']));
					$this->db->update('cashbal',$d);
					
					$cnt++;
					
				endforeach;
			}

			$this->session->set_flashdata('flash_message', get_phrase('operation_successful'));
            redirect(base_url() . 'ifms.php/partner/cash_journal/'.strtotime($param2), 'refresh');			
		}**/

		//Check Max MFR
		
		$max_mfr = $this->db->select_max('closureDate')->get_where('opfundsbalheader',array('icpNo'=>$project))->row()->closureDate;	
		
		$time = date('Y-m-01',strtotime($max_mfr));
		
        $page_data['page_name']  = 'financial_reports';
		$page_data['tym'] = $time;
        $page_data['page_title'] = get_phrase('financial_reports');
        $this->load->view('backend/index', $page_data);	
			
	}

function load_expense_data($param1="",$param2="",$param3=""){
	$page_data['center_id'] =  $param1;
	$page_data['AccNo'] =  $param2;
	$page_data['month'] =  $param3;

	$acc = $this->db->get_where('accounts',array("AccNo"=>$param2))->row();

	$page_data['acc'] = $acc;
	$page_data['expense_account'] = $this->finance_model->expense_accounts($acc->accID);
	$page_data['expense_report_grid']= $this->expense_report_grid($param1,$param3,$param2);
	$page_data['budget_spread_grid'] = $this->finance_model->budget_spread_grid(get_fy($param3),$param1,$acc->accID,$param3);
	
	//echo json_encode([$param1,$param2,$param3]);
	echo $this->load->view('backend/partner/expense_report_data', $page_data,TRUE);	
}

function expense_report_grid($fcp_number,$period_end_date,$income_account_number){

	$period_start_date = fy_start_date($period_end_date,$fcp_number);

	$this->db->where(array('AccNo'=>$income_account_number));
	$parent_account_id = $this->db->get('accounts')->row()->accID;

	$condition = array('parentAccID'=>$parent_account_id);
	
	$expense_report = [];

	$vouchers = $this->finance_model->list_to_date_vouchers_for_fcp($fcp_number,$period_start_date,$period_end_date,$condition);
		
	foreach($vouchers as $voucher){
		
		// Computes expense of the month
		if($voucher['voucher_date'] == $period_end_date){
			$expense_report['month_expenses'][$voucher['account_number']] = $voucher['Cost'];
		}

		// Compute expense to date in the FY
		if(isset($expense_report['expense_to_date'][$voucher['account_number']])){
			$expense_report['expense_to_date'][$voucher['account_number']] += $voucher['Cost'];
		}else{
			$expense_report['expense_to_date'][$voucher['account_number']] = $voucher['Cost'];
		}
			
	}

	return $expense_report;
}

 /***End Load Default Pages***/
 
function reverse_cheque($param1=''){
		//Get Bank Code
		$bank_code = $this->db->get_where('projectsdetails',array('icpNo'=>$this->session->userdata('center_id')))->row()->bankID;
		if(!isset($bank_code)){
			$bank_code = 0;
		}
		//Check if Cheque No exists
		$chqNo = $param1."-".$bank_code;
		
		$chk = $this->db->get_where('voucher_body',array('ChqNo'=>$chqNo,"icpNo"=>$this->session->userdata('center_id')))->result_object();
		
		echo json_encode($chk);
	}

	function get_account_without_civs(String $non_civ_condition, int $voucher_item_type_id = 0){
		
		if($voucher_item_type_id > 0){
			$this->dct_model->get_accounts_related_voucher_item_type($voucher_item_type_id);
		}

		$this->db->select(array('accID','AccNo','AccText','AccName'));
		$this->db->where($non_civ_condition);
		$expenses_or_income_accs = $this->db->get('accounts')->result_array();

		return $expenses_or_income_accs;
	}

	function get_accounts_with_civs(String $civ_condition, int $voucher_item_type_id = 0){

		$this->db->join('civa', 'accounts.accID=civa.accID');
		
		if($voucher_item_type_id > 0){
			$this->dct_model->get_civa_related_voucher_item_type($voucher_item_type_id);
		}

		$this->db->select(array('accounts.accID as accID','AccNo','AccText','AccName','civaID','AccNoCIVA','AccTextCIVA','allocate','open'));
		$this->db->where($civ_condition);
		$expenses_or_income_accs = $this->db->get('accounts')->result_array();

		return $expenses_or_income_accs;
	}

	private function get_accounts(String $non_civ_condition, String $civ_condition, int $voucher_item_type_id = 0): array
	{
		
		$get_account_without_civs = [];
		$get_accounts_with_civs = [];

		if($non_civ_condition !== ""){
			$get_account_without_civs = $this->get_account_without_civs($non_civ_condition, $voucher_item_type_id);
		}

		if($civ_condition !== ""){
			$get_accounts_with_civs = $this->get_accounts_with_civs($civ_condition, $voucher_item_type_id);
		}

		return array_merge($get_account_without_civs,$get_accounts_with_civs);
	}

	function voucher_accounts($param1 = '', $voucher_item_type_id = 0)
	{
		//Return as JSON object
		$rst_rw = "";
		if ($param1 === 'CHQ') {
			//Bank Expenses Accounts
			$non_civ_cond = "((accounts.AccGrp = 0 OR accounts.AccGrp = 3) AND accounts.Active=1)";
			$civ_cond = "(accounts.AccGrp = 0 AND accounts.Active=0 AND civa.open=1 AND civa.closureDate>CURDATE() AND civa.is_direct_cash_transfer = 0)"; 
			$rst_rw = $this->get_accounts($non_civ_cond, $civ_cond , $voucher_item_type_id);
		}

		if ($param1 === 'PC' || $param1 === 'BCHG') {
			//PC and BC Expenses Accounts
			$non_civ_cond = "(accounts.AccGrp = 0 AND accounts.Active=1)";	
			$civ_cond = "(accounts.AccGrp = 0 AND accounts.Active=0 AND civa.open=1 AND civa.closureDate>CURDATE() AND civa.is_direct_cash_transfer = 0)";
			$rst_rw = $this->get_accounts($non_civ_cond, $civ_cond , $voucher_item_type_id);
		}

		if ($param1 === 'CR') {
			//Revenue accounts
			$non_civ_cond = "(accounts.AccGrp = 1 AND accounts.Active=1)";
			$civ_cond = "(accounts.AccGrp = 1 AND accounts.Active=0 AND civa.open=1 AND civa.closureDate>CURDATE())";
			$rst_rw = $this->get_accounts($non_civ_cond, $civ_cond , $voucher_item_type_id);
		}

		if ($param1 === 'PCR') {
			//Petty Cash rebanking account
			$non_civ_cond = "accounts.AccGrp = 4";
			$civ_cond = "";
			$rst_rw = $this->get_accounts($non_civ_cond, $civ_cond , $voucher_item_type_id);
		}

		// if ($param1 == 'UDCTB' || $param1 == 'UDCTC') {
		// 	$exp_cond = "(accounts.AccGrp = 0 AND accounts.is_direct_cash_transfer = 1 AND accounts.Active=1) OR (accounts.AccGrp = 0 AND accounts.is_direct_cash_transfer = 1 AND accounts.Active=0 AND civa.open=1 AND civa.closureDate>CURDATE() AND civa.is_direct_cash_transfer = 1)";
		// 	$rst_rw = $this->get_accounts($exp_cond, $voucher_item_type_id);
		// }

		$rst = array();
		
		$rst['acc'] = [];

		// Check Voucher type allow_support_mode_and_recipient value
		$allow_support_mode_and_recipient = $this->db->get_where('voucher_type',
		array('voucher_type_abbrev'=>$param1))->row()->allow_support_mode_and_recipient;

		foreach ($rst_rw as $civaAcc) :
			// Do not show accounts list if there is not recipient selected and the config use_dct_detail_row is true
			if($voucher_item_type_id == 0 && $this->config->item('use_dct_detail_row') && $allow_support_mode_and_recipient) continue;

			$untrimmed_explode_allocate = isset($civaAcc['allocate'])?explode(',',$civaAcc['allocate']):[];
			$trimmed_explode_allocate = array_map(array($this,'trim_spaces'),$untrimmed_explode_allocate);
			
			if (isset($civaAcc['civaID']) && is_numeric($civaAcc['civaID']) && in_array($this->session->userdata('center_id'),$trimmed_explode_allocate)) {
				$rst['acc'][] = $civaAcc;
			} elseif (!isset($civaAcc['civaID']) || !is_numeric($civaAcc['civaID'])) {
				$rst['acc'][] = $civaAcc;
			}
		endforeach;

		$rst['voucher_type_effect'] = $this->db->get_where('voucher_type',array('voucher_type_abbrev'=>$param1))->row()->voucher_type_effect;
		
		if($this->config->item('use_dct_detail_row')){
			$rst['item_types'] = $this->dct_model->get_voucher_item_types();
			$rst['support_modes'] = $this->dct_model->get_support_modes_for_voucher_type($param1);
		}

		//echo json_encode($rst);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($rst));
	}

	function trim_spaces($elem){
		return trim($elem);
	}

function chqIntel($param1){
	$return_flag = -1;// Error occurred flag

		$bank_code_obj = $this->db->get_where('projectsdetails', 
		array('icpNo' => $this->session->userdata('center_id')));

		//Get Bank Code
		if($bank_code_obj->num_rows() > 0){

			$bank_code = $bank_code_obj->row()->bankID;
			
			//Check if Cheque No exists
			$chqNo = $param1 . "-" . $bank_code;
			$chqNo_reversed = $param1 . "-" . $bank_code . "-0";

			$chk_cond = "(ChqNo ='" . $chqNo . "' OR ChqNo='" . $chqNo_reversed . "') AND icpNo='" . $this->session->userdata('center_id') . "'";
			$chk = $this->db->where($chk_cond)->get('voucher_header')->result_array();

			//echo count($chk)>0?1:0;
			if (count($chk) === 1) {
				//Cheque present but not reversed
				$return_flag  = 1;
			} elseif (count($chk) > 1) {
				// Cheque present and reversed
				$return_flag  = 2;
			} else {
				// Cheque not used
				$return_flag  = 0;
			}
		}

		echo $return_flag;
		// //Get Bank Code
		// $bank_code = $this->db->get_where('projectsdetails',array('icpNo'=>$this->session->userdata('center_id')))->row()->bankID;
		
		// //Check if Cheque No exists
		// $chqNo = $param1."-".$bank_code;
		// $chqNo_reversed = $param1."-".$bank_code."-0";
		
		// $chk_cond = "(ChqNo ='".$chqNo."' OR ChqNo='".$chqNo_reversed."') AND icpNo='".$this->session->userdata('center_id')."'";
		// $chk = $this->db->where($chk_cond)->get('voucher_header')->result_array();
		
		// //echo count($chk)>0?1:0;
		// if(count($chk)===1){
		// 	echo 1;
		// }elseif(count($chk)>1){
		// 	echo 2;
		// }else{
		// 	echo 0;
		// }
		
		
	}

	function generate_dct_reference_number($voucher_date){
		//$date=$this->input->post('TDate');
		//$v_date=str_replace(',','',$date);
		echo $this->finance_model->generate_dct_reference_number($voucher_date);
	}
	
public function multiple_vouchers($tym){
	if ($this->session->userdata('admin_login') != 1)
	      redirect(base_url(), 'refresh');
	
	$post = $this->input->post();
	//array_shift($post);
	/**
	 * $this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($rst));	
	 */
	//$page_data['page_name']  = 'multiple_vouchers';
	//$page_data['app_name'] = 'ifms';
	$page_data['vouchers']= $post;
	//$page_data['page_title'] = get_phrase('multiple_vouchers');
	
	$page_data['page_name']  = 'multiple_vouchers';
	$page_data['tym'] = $tym;
    $page_data['page_title'] = get_phrase('multiple_vouchers');
    $this->load->view('backend/index', $page_data);	

	//$this->load->view('backend/partner/multiple_vouchers', $page_data);

	
}
	function new_voucher(){
		if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
		
		
		
		$page_data['page_name']  = 'new_voucher';
		$page_data['voucher_types'] = $this->finance_model->get_voucher_types();
	    $page_data['page_title'] = get_phrase('new_voucher');
	    $this->load->view('backend/index', $page_data);			
	}
	
	function post_voucher(){
		
		if ($this->session->userdata('admin_login') != 1)
			redirect(base_url(), 'refresh');

		$allow_support_mode_and_recipient = $this->db->get_where('voucher_type',
			array('voucher_type_abbrev'=>$this->input->post('VTypeMain')))->row()->allow_support_mode_and_recipient;	

		$data['msg'] = get_phrase('voucher_posted_successfully');

		$hID = 0;

		if($this->config->item('use_dct_detail_row') && $allow_support_mode_and_recipient){
			
			$hID = $this->dct_model->post_voucher();
		}else{
			$hID = $this->_post_voucher();
		}	
       
        if (!$hID) {
			$data['msg'] = get_phrase('voucher_posting_failed');
        } else {

            //Send email to PF
            $cname = $this->db->get_where('users',array('fname'=>$this->session->center_id))->row()->cname;
					
			$pfEmail = $this->db->get_where('users',array('userlevel'=>'2','cname'=>$cname))->row()->email;
						
			$this->email_model->voucher_submitted($hID,$pfEmail);

        }

		$data['tym'] = strtotime($this->input->post('TDate'));
		$data['voucher_types'] = $this->finance_model->get_voucher_types();
		echo $this->load->view("backend/partner/new_voucher", $data, true);
        
	 }
	 
	function _post_voucher(){

		$hID = 0;

		$this->db->trans_start();
		        
	    $data['icpNo']  = $this->input->post('KENo');
	    $data['TDate'] = date('Y-m-d',strtotime($this->input->post('TDate')));
		$data['Fy'] = get_fy($this->input->post('TDate'),$this->session->center_id);
		$data['VNumber'] = $this->input->post('VNumber');
		$data['Payee'] = $this->input->post('Payee');
		$data['Address'] = $this->input->post('Address');
		$data['VType'] = $this->input->post('VTypeMain');
		//$data['raiser_id'] = $this->session->login_user_id;
					
		//Check if Bank Details exists
								
		if($this->db->get_where('projectsdetails',array('icpNo'=>$this->session->userdata('center_id')))->num_rows()>0){
					
			$bank_code = $this->db->get_where('projectsdetails',array('icpNo'=>$this->session->userdata('center_id')))->row()->bankID;

		}else{
											
			//$message = get_phrase('bank_details_missing');
				
			$bank_code = 0;
		}
						
		//Append Bank Code to ChqNo

		if($this->input->post('reversal')){
			$bank_code = $bank_code."-0";
		}
					
		$data['ChqNo'] = $this->input->post('ChqNo')."-".$bank_code;
		$data['TDescription'] = $this->input->post('TDescription');
		$data['totals'] = array_sum($this->input->post('cost'));
		$data['unixStmp'] = time();
		            
		//Check if voucher already exists
		$chk_obj = $this->db->get_where("voucher_header",array("VNumber"=>$this->input->post('VNumber'),"icpNo"=>$this->input->post('KENo')));
					
		if($chk_obj->num_rows() == 0){						
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
						
			for($i=0;$i<sizeof($this->input->post('qty'));$i++){
				$data2['hID'] = $hID;
				$data2['icpNo']= $this->input->post('KENo');
				$data2['VNumber']=$this->input->post('VNumber');
				$data2['TDate']=$this->input->post('TDate');
				$data2['VType']=$this->input->post('VTypeMain');
				$data2['ChqNo']=$this->input->post('ChqNo')."-".$bank_code;
				$data2['unixStmp']=time();
				$data2['Qty'] = $qty[$i];
				$data2['Details']=$details[$i];
				$data2['UnitCost']=$unitcost[$i];
				$data2['Cost']=$cost[$i];
				$data2['AccNo']=$acc[$i];
				$data2['civaCode']=$civ[$i];
							
				$this->db->insert('voucher_body', $data2);
			}
												
		}

		$this->db->trans_complete();
       
        if ($this->db->trans_status() === false) {
			$hID  = 0;
        } 

		return $hID;
	} 

	function reset_voucher(){
		$page_data['page_name']  = 'new_voucher';
		$page_data['voucher_types'] = $this->finance_model->get_voucher_types();
		echo $this->load->view("backend/partner/new_voucher",$page_data,true);
	}

	function voucher_approval_request($param1=''){
		if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
					//Populate header elements //icpNo,TDate,Fy,VNumber,Payee,Address,VType,ChqNo,TDescription,totals,unixStmp
		            
		            $rmk = get_phrase('approval_request_submitted');
		            
		            $data['icpNo']  = $this->input->post('KENo');
		            $data['TDate'] = $this->input->post('TDate');
					$data['Payee'] = $this->input->post('Payee');
					$data['Address'] = $this->input->post('Address');
					$data['VType'] = $this->input->post('VTypeMain');
					$data['raiser_id'] = $this->session->login_user_id;
					
					//Check if Bank Details exists
					$bank_code = $this->db->get_where('projectsdetails',array('project_id'=>$this->session->userdata('center_id')))->row()->bankID;
					
					if(!isset($bank_code)){
						$rmk = get_phrase('bank_details_missing');
					}
						
					//Append Bank Code to ChqNo

					if($this->input->post('reversal')){
						$bank_code = $bank_code."-0";
					}
					
					$data['ChqNo'] = $this->input->post('ChqNo')."-".$bank_code;
					$data['TDescription'] = $this->input->post('TDescription');
					$data['totals'] = array_sum($this->input->post('cost'));
					$data['unixStmp'] = time();
		            
		            $this->db->insert('voucher_requests_header', $data);
					
					//Last id
					$hID = $this->db->insert_id();
					
					//Populate body //hID,icpNo,VNumber,TDate,VType,ChqNo,unixStmp     Qty,Details,UnitCost,Cost,AccNo,civaCode //$data2[''][$i]=
					$qty = $this->input->post('qty');
					$details = $this->input->post('desc');
					$unitcost = $this->input->post('unit');
					$cost = $this->input->post('cost');
					$acc = $this->input->post('acc');
					$civ = $this->input->post('civaCode');
					
					for($i=0;$i<sizeof($this->input->post('qty'));$i++){
						$data2['reqID'] = $hID;
						$data2['icpNo']= $this->input->post('KENo');
						$data2['TDate']=$this->input->post('TDate');
						$data2['VType']=$this->input->post('VTypeMain');
						$data2['ChqNo']=$this->input->post('ChqNo')."-".$bank_code;
						$data2['unixStmp']=time();
						$data2['Qty'] = $qty[$i];
						$data2['Details']=$details[$i];
						$data2['UnitCost']=$unitcost[$i];
						$data2['Cost']=$cost[$i];
						$data2['AccNo']=$acc[$i];
						$data2['civaCode']=$civ[$i];
						
						$this->db->insert('voucher_requests_body', $data2);
					}
			
		$this->session->set_flashdata('flash_message',$rmk);
		
        $page_data['page_name']  = 'cash_journal';
		$page_data['tym'] = strtotime($this->input->post('TDate'));
        $page_data['page_title'] = get_phrase('cash_journal');
        $this->load->view('backend/index', $page_data);
     }

	function get_ajax_voucher($param1=""){
		//echo $param1;
		$body = $this->db->get_where('voucher_body',array('hID'=>$param1))->result_object();
		
		echo json_encode($body);
	}
	
	function clear_effects($param1='',$param2=''){
		
			$hID_arr = explode("_", $param1);
			
			//$data=array();
			if(!substr_count($hID_arr[0], 'unclear')){
				$data['ChqState'] = '1';
				$data['clrMonth'] = date('Y-m-d',$param2);	
				
				$this->db->where(array('hID'=>$hID_arr[1]))->update('voucher_header',$data);	
			}
			else{
				$data['ChqState'] = '0';
				$data['clrMonth'] = '0000-00-00';	
				
				$this->db->where(array('hID'=>$hID_arr[1]))->update('voucher_header',$data);	
			}
			

			echo get_phrase('operation_successful');		
		
	}
	function clear_bank_transactions($param1='',$param2='',$param3=""){
		
		$data['ChqState'] = $param2;
		$data['clrMonth'] = $param3;

		$message = 'Transaction clearance failed';
		
		if($param2 == 0 && $this->finance_model->mfr_submitted($this->session->center_id,$param3) != 1){
			$data['clrMonth'] ="0000-00-00";	
			$this->db->where(array('hID'=>$param1))->update('voucher_header',$data);
			$message = "Transaction cleared successfully";
		}elseif($param2 <> 0){
			$this->db->where(array('hID'=>$param1))->update('voucher_header',$data);
			$message = "Transaction cleared successfully";
		}
		
		echo $message;
		
	}

function create_budget_item($project){
		 if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');		
			
			if(count($this->db->get_where('planheader',array('icpNo'=>$project,'fy'=>$this->input->post('fy')))->row())===0){
					
				$header['icpNo'] = 	$project;
				$header['fy'] = $this->input->post('fy');
				
				$this->db->insert('planheader',$header);
			}
			
			$planHeaderID = $this->db->get_where('planheader',array('icpNo'=>$project,'fy'=>$this->input->post('fy')))->row()->planHeaderID;
			
			$body['planHeaderID'] = $planHeaderID;
			$body['AccNo'] = $this->input->post('AccNo');
			$body['plan_item_tag_id'] = $this->input->post('plan_item_tag_id');
			$body['details'] = $this->input->post('details');
			$body['qty'] = $this->input->post('qty');
			$body['unitCost'] = $this->input->post('unitCost');
			$body['often'] = $this->input->post('often');
			$body['totalCost'] = $this->input->post('totalCost');
			$body['notes'] = $this->input->post('notes');
			
			$range = range(1,12);
			$fields_range = array('month_1_amount','month_2_amount','month_3_amount','month_4_amount','month_5_amount','month_6_amount','month_7_amount','month_8_amount','month_9_amount','month_10_amount','month_11_amount','month_12_amount');
			//$start_month = $this->finance_model->project_system_start_date($this->session->center_id);
			
			foreach($fields_range as $month):
				$body[$month] = $this->input->post($month);
			endforeach;
			
			
			//$parentAccID = $this->db->get_where('accounts',array('AccNo' => $this->input->post('AccNo')))->row()->parentAccID;
			
			//$rev_acc = $this->db->get_where('accounts',array('accID'=>$parentAccID))->row()->AccNo;
			
			//$dif = $this->finance_model->grant_budget_limit_dif($rev_acc,$this->input->post('fy'));
			
			//if($dif<$this->input->post('totalCost')&&$dif!==0){
				//$this->session->set_flashdata('flash_message',get_phrase('limit_exceeded'));
			//}else{
				$this->db->insert('plansschedule',$body);
				//$this->session->set_flashdata('flash_message',get_phrase('record_created'));
			//}
		
		$page_data['param2'] = $this->input->post('fy');	
		$page_data['msg'] = get_phrase('record_created');
			
		echo $this->load->view('backend/partner/modal_new_budget_item',$page_data,TRUE);	
		//redirect(base_url().'ifms.php/partner/scroll_budget_schedules/'.$this->input->post('fy'),'refresh');
	}	
	
	public function variance_explanation(){

		$data = $this->input->post('rec');
		
		$cnt = count($this->db->get_where('varjustify',array('icpNo'=>$data[0]['project_id'],'reportMonth'=>$data[0]['reportMonth']))->result_array());
		
		if($cnt>0){
				
			if($cnt!==count($data)){
				$this->db->where('reportMonth',$data[0]['reportMonth']);	
				$this->db->where('icpNo',$this->session->center_id);
				$this->db->delete('varjustify');
				$this->db->insert_batch('varjustify',$data);
			}else{
				$this->db->where('reportMonth',$data[0]['reportMonth']);	
				$this->db->where('icpNo',$this->session->center_id);
				$this->db->update_batch('varjustify', $data,'AccNo');
			}
		}else{
			$this->db->insert_batch('varjustify',$data);
		}

	}

	// private function get_uploaded_bank_statement($reporting_month,$fcp_id = ''){

	// 	$fcp_id = $fcp_id == ''?$this->session->center_id:$fcp_id;

	// 	$statementbal_id = $this->get_bank_statement_id($fcp_id,$reporting_month);

	// 	$this->db->where([
	// 		'item_name'=>'bank_statement',
	// 		'attachment_primary_id' => $statementbal_id
	// 	]);
		
	// 	$attachment_obj = $this->db->get('attachment');

	// 	$attachments = [];

	// 	if($attachment_obj->num_rows() > 0){
	// 		$attachments = $attachment_obj->result_array();
	// 	}

	// 	return $attachments;
	// }

	function bank_statements($param1=""){
		 if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
			
		$page_data['uploaded_bank_statement'] = $this->finance_model->get_uploaded_bank_statement($param1);
        $page_data['page_name']  = 'bank_statements_upload';
		$page_data['tym'] = strtotime($param1);
        $page_data['page_title'] = get_phrase('bank_statements');
        $this->load->view('backend/index', $page_data);		
	}

	// private function get_bank_statement_id($fcp_id,$reporting_month){

	// 	$this->db->where(['month'=>$reporting_month,'icpNo'=>$fcp_id]);
	// 	$statementbal_obj = $this->db->get('statementbal');

	// 	$statementbal_id = 0;

	// 	if($statementbal_obj->num_rows() == 0){
	// 		// Insert and Get Id
	// 		$data['month'] = $reporting_month;
	// 		$data['statementDate'] = $reporting_month;
	// 		$data['actualDate'] = $reporting_month;
	// 		$data['icpNo'] = $fcp_id;
	// 		$data['amount'] = 0;

	// 		$this->db->insert('statementbal',$data);

	// 		$statementbal_id = $this->db->insert_id();
	// 	}else{
	// 		// Get Id
	// 		$this->db->where(['month'=>$reporting_month,'icpNo'=>$fcp_id]);
	// 		$statementbal_id = $this->db->get('statementbal')->row()->balID;
	// 	}

	// 	return $statementbal_id;
	// }

	public function bank_statements_upload(){

			$preassigned_urls = [];
			$reporting_month = date('Y-m-t',$this->input->post('reporting_month'));
			$fcp_id = $this->input->post('fcp_id');

			$statemenbal_id = $this->finance_model->get_bank_statement_id($fcp_id,$reporting_month);

			 //if (!empty($_FILES)) {
				
				//foreach($_FILES['file']['name'] as $index=>$name){
	            
				//$file = explode('.',$name);
				//$filename = $file[0];
				//$file_ext=$file[1];	
	             
	            //  if(!file_exists('uploads/bank_statements/'.$this->session->center_id))
				// 		mkdir('uploads/bank_statements/'.$this->session->center_id);//.$name
						
	            //  if(!file_exists('uploads/bank_statements/'.$this->session->center_id.'/'.date('Y-m',$param1)))
				// 		mkdir('uploads/bank_statements/'.$this->session->center_id.'/'.date('Y-m',$param1));//.$name
				
				// if(!file_exists('uploads/bank_statements/'.$this->session->center_id.'/'.date('Y-m',$param1).'/'.sha1($filename).'.'.$file_ext)){				    
	            //         move_uploaded_file($_FILES["file"]["tmp_name"][$index],'uploads/bank_statements/'.$this->session->center_id.'/'.date('Y-m',$param1).'/'.sha1($filename).'.'.$file_ext);
	            // 		echo $name.' uploaded successful';
				//  }

				$this->db->where(array('icpNo'=>$fcp_id));
				$projectsdetails_id = $this->db->get('projectsdetails')->row()->ID;
				
				$additional_attachment_table_insert_data = [];

				$additional_attachment_table_insert_data['attachment_primary_id'] = $statemenbal_id;//15304;
				$additional_attachment_table_insert_data['item_name'] = 'bank_statements';
				$additional_attachment_table_insert_data['fk_projectsdetails_id'] = $projectsdetails_id;

				$attachment_where_condition_array = [];

				$attachment_where_condition_array['item_name'] = 'bank_statement';
				$attachment_where_condition_array['attachment_primary_id'] = $statemenbal_id;//15304;


				$storeFolder = 'uploads/bank_statements/'.$fcp_id.'/'.date('Y-m',$this->input->post('reporting_month'));//.'/'.sha1($filename);
	            
				$preassigned_urls =  $this->aws_attachment_library->upload_files($storeFolder,$additional_attachment_table_insert_data, $attachment_where_condition_array);

	          	//}
	        //}
		
			return $preassigned_urls;
		}
	
	public function get_bank_statements($param1){
		$ds          = DIRECTORY_SEPARATOR; 
	 
		$storeFolder = 'uploads/bank_statements/'.$this->session->center_id.'/'.date('Y-m',strtotime($param1));  
		 
		if (!empty($_FILES)) {
		 
		    $tempFile = $_FILES['file']['tmp_name'];         
		 
		    $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds; 
		 
		    $targetFile =  $targetPath. $_FILES['file']['name']; 
		 
		    move_uploaded_file($tempFile,$targetFile);
		 
		} else {                                                           
		    $result  = array();
		 
		    $files = scandir($storeFolder);                 //1
		    if ( false!==$files ) {
		        foreach ( $files as $file ) {
		            if ( '.'!=$file && '..'!=$file) {       //2
		                $obj['name'] = $file;
		                $obj['size'] = filesize($storeFolder.$ds.$file);
		                $result[] = $obj;
		            }
		        }
		    }
		     
		    header('Content-type: text/json');              //3
		    header('Content-type: application/json');
		    echo json_encode($result);
		}
	}

	function delete_single_file(){

		$post = $this->input->post();

		$path = $post['path'];
		$file_name = $post['file_name'];

		$this->aws_attachment_library->delete_s3_object($path,$file_name);

		$this->db->where(array('attachment_url'=>$path,'attachment_name'=>$file_name));
		
		if($this->db->delete('attachment')){
			echo "File deleted!";
		}else{
			echo "Deletion failed";
		}
		
		// $this->session->set_flashdata('flash_message',get_phrase('files_deleted'));	
		// redirect(base_url().'ifms.php/partner/bank_statements/'.date('Y-m-d',$tym),'refresh');
	}
	
	public function delete_bank_statement($param1){
		//$t= $_POST['name'];
		$storeFolder = 'uploads/bank_statements/'.$this->session->center_id.'/'.date('Y-m',$param1);

		//unlink($storeFolder);
		// foreach (glob($storeFolder."/*.*") as $filename) {
		// 	if (is_file($filename)) {
		// 	       unlink($filename);
		// 	}
		// }

		$this->aws_attachment_library->delete_s3_objects($storeFolder);

		$this->db->where(array('attachment_url'=>$storeFolder));
		$this->db->delete('attachment');
		
		$this->session->set_flashdata('flash_message',get_phrase('files_deleted'));	
		redirect(base_url().'ifms.php/partner/bank_statements/'.date('Y-m-d',$param1),'refresh');
	}
	
	public function bank_statement_download($param1,$param2){
		force_download('uploads/bank_statements/'.$this->session->center_id.'/'.date('Y-m',$param2).'/'.$param1,NULL);
	}
	
	function submit_mfr($project_id,$date){
		
		$proof_of_cash = $this->finance_model->proof_of_cash($this->session->center_id,date('Y-m-t',$date));
		
		$bank_validation = abs(floor($this->finance_model->bank_reconciled($this->session->center_id,date('Y-m-t',$date))));
		
		$bs_check = $this->finance_model->check_bank_statement($this->session->center_id,date('Y-m-t',$date)); 
		
		if($proof_of_cash <> 0 || $bank_validation <> 0 || $bs_check == 0 ){
			
			echo get_phrase('report_not_submitted_due_to_validation_error');
		
		}elseif($this->db->get_where('opfundsbalheader',array('icpNo'=>$this->session->center_id,'closureDate'=>date('Y-m-t',$date)))->num_rows()>0){
		
			echo get_phrase('report_not_submitted_due_to_an_existing_report');
		
		}else{
			
			
			//Update Fund Balances
			
			$data2['icpNo'] = $this->session->center_id;
			$data2['totalBal'] = $this->finance_model->total_months_closing_balance($this->session->center_id,date('Y-m-t',$date));
			$data2['closureDate'] = date('Y-m-t',$date);
			$data2['allowEdit'] = '1';
			$data2['submitted'] = '1';
			$data2['mfr_submitted_date'] = date('Y-m-d h:i:s');
			$data2['systemOpening'] = '0';
			
			$this->db->insert('opfundsbalheader',$data2);
			
			$balHdID = $this->db->insert_id();
			
			$rec_accs = $this->db->get_where('accounts',array("AccGrp"=>"1"))->result_object();
									
			foreach($rec_accs as $row):
				if($this->finance_model->months_closing_fund_balance_per_revenue_vote($this->session->center_id,$row->AccNo,date('Y-m-d',$date))!==0){
					$data3['balHdID'] = $balHdID;
					$data3['funds'] = $row->AccNo;
					$data3['amount'] = $this->finance_model->months_closing_fund_balance_per_revenue_vote($this->session->center_id,$row->AccNo,date('Y-m-t',$date));
					
					$this->db->insert("opfundsbal",$data3);		
				}
						
			endforeach;	
			
			
			//Update Cash Balance
			
			$accArr = array("PC","BC");
			
			foreach($accArr as $rw):
				$data4['month'] = date('Y-m-t',$date);
				$data4['AccNo'] = $rw;
				$data4['icpNo'] = $this->session->center_id;
				if($rw==="PC"){
					$data4['amount'] = $this->finance_model->petty_cash_balance(date('Y-m-01',$date),$this->session->center_id);
				}elseif($rw==="BC"){
					$data4['amount'] = $this->finance_model->bank_balance(date('Y-m-01',$date),$this->session->center_id);
				}
				
				$this->db->insert('cashbal',$data4);
						
			endforeach;	
			
			// Register dashboard change after a run
			$this->register_dashboard_change($this->session->center_id,$date);

			$this->email_model->submit_mfr_notification($this->session->login_user_id,date('Y-m-t',$date));
			
			
			echo $this->session->center_id." ".get_phrase('financial_report_for')." ".date('Y-m-t',$date)." ".get_phrase('submitted');
		}
		
		
	}
	
	function register_dashboard_change($fcp_id,$date){
		$dashboard_run = $this->db->get_where('dashboard_run',array('month'=>date('Y-m-t',$date)));

			$projectsdetails = $this->db->get_where('projectsdetails',
			array('icpNo'=>$fcp_id));

			if($dashboard_run->num_rows() > 0 && $projectsdetails->num_rows() > 0){

				$dashboard_change = $this->db->get_where('dashboard_change',
				array('projectsdetails_id'=>$projectsdetails->row()->ID,'month'=>date('Y-m-t',$date),'status'=>1));
				
				if($dashboard_change->num_rows() == 0){
					$dashboard_change_data['projectsdetails_id'] = $projectsdetails->row()->ID;
				
					$dashboard_change_data['change_date'] = date('Y-m-d H:i:s');
					$dashboard_change_data['month'] = date('Y-m-t',$date);
					$dashboard_change_data['status'] = 1;

					$this->db->insert('dashboard_change',$dashboard_change_data);
				}
				
			}
	}

	public function finance_settings($param1 = '', $param2 = ''){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
     
	 
	    $page_data['page_name']  = 'finance_settings';
        $page_data['page_title'] = get_phrase('finance_accounts_settings');
        $this->load->view('backend/index', $page_data);		
	}		
	

 function civ_report($param1=""){
	if ($this->session->userdata('admin_login') != 1)
	      redirect(base_url(), 'refresh');
	
	$page_data['page_name']  = 'civ_report';
	
	if($param1==="closed"){
		$page_data['page_name']  = 'closed_civs_report';
	}
	//$page_data['page_name']  = 'civ_report';
    $page_data['page_title'] = get_phrase('interventions_report');
    $this->load->view('backend/index', $page_data);	 	
 } 
 
 function assets(){
 	
 }

function insert_attachment_records_from_local_file_system(){
	
	// $fcps = $this->finance_model->get_projectsdetails();
	// $statements = $this->finance_model->get_statement_balance_ids();
	// $claiming_fcp_projectsdetails = $this->finance_model->claiming_fcp_projectsdetails();

	$this->finance_model->insert_attachment_records_from_local_file_system();

}

}