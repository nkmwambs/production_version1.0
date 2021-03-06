<?php 

class Dashboard_model extends CI_Model{
    private $table_prefix = '';
   // private $uncleared_transactions; 
    
    function __construct() {
        parent::__construct();
		
		$this -> load -> config('dev_config');
        $this -> get_table_prefix();
        
        $this->load->model('finance_model');

		$this -> load -> config('dev_config');
		$this -> get_table_prefix();
    }


    	 
	//General Methods
	private function get_table_prefix() {

		$this -> table_prefix = $this -> config -> item('table_prefix');

		return $this -> table_prefix;
	}

	private function checkFolderIsEmptyOrNot($folderName) {
		$files = array();
		if ($handle = opendir($folderName)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..")
					$files[] = $file;
				if (count($files) >= 1)
					break;
			}
			closedir($handle);
		}
		return (count($files) > 0) ? TRUE : FALSE;
	}

	private function group_data_by_fcp_id($database_results) {

		$group_by_fcp_id_array = array();

		foreach ($database_results as $row) {

			if (isset($row['fcp_id'])) {
				$group_by_fcp_id_array[$row['fcp_id']] = $row;
			}

		}

		return $group_by_fcp_id_array;
	}

	//Prod data arrays
	
	

	//Test Models Methods

	public function test_fcps_with_risk_model() {

		$fcp_array = array();

		//KE0200 array
		$fcp_array[1]['fcp_id'] = 'KE0200';
		$fcp_array[1]['risk'] = 'High';

		//KE0215 array
		$fcp_array[2]['fcp_id'] = 'KE0215';
		$fcp_array[2]['risk'] = 'Low';

		//KE0300 array
		$fcp_array[3]['fcp_id'] = 'KE0300';
		$fcp_array[3]['risk'] = 'Medium';

		//KE0320 array
		$fcp_array[4]['fcp_id'] = 'KE0320';
		$fcp_array[4]['risk'] = 'High';

		//KE0540 array
		$fcp_array[5]['fcp_id'] = 'KE0540';
		$fcp_array[5]['risk'] = 'Medium';

		return $fcp_array;
	}

	private function test_dashboard_parameters_model() {
		$dashboard_params = array();

		$dashboard_params[1]['dashboard_parameter_name'] = 'MFR Submitted';
		$dashboard_params[1]['result_method'] = 'callback_mfr_submitted';
		$dashboard_params[1]['is_requested'] = 'no';
		$dashboard_params[1]['display_on_dashboard'] = 'yes';

		$dashboard_params[2]['dashboard_parameter_name'] = 'Bank Statement uploaded';
		$dashboard_params[2]['result_method'] = 'callback_bank_statement_uploaded';
		$dashboard_params[2]['is_requested'] = 'no';
		$dashboard_params[2]['display_on_dashboard'] = 'yes';

		$dashboard_params[3]['dashboard_parameter_name'] = 'Book Bank Balance';
		$dashboard_params[3]['result_method'] = 'callback_book_bank_balance';
		$dashboard_params[3]['is_requested'] = 'no';
		$dashboard_params[3]['display_on_dashboard'] = 'no';

		$dashboard_params[4]['dashboard_parameter_name'] = 'Statement Bank Balance';
		$dashboard_params[4]['result_method'] = 'callback_statement_bank_balance';
		$dashboard_params[4]['is_requested'] = 'no';
		$dashboard_params[4]['display_on_dashboard'] = 'no';

		$dashboard_params[5]['dashboard_parameter_name'] = 'Oustanding Cheques';
		$dashboard_params[5]['result_method'] = 'callback_outstanding_cheques';
		$dashboard_params[5]['is_requested'] = 'no';
		$dashboard_params[5]['display_on_dashboard'] = 'no';

		$dashboard_params[6]['dashboard_parameter_name'] = 'Deposit in transit';
		$dashboard_params[6]['result_method'] = 'callback_deposit_in_transit';
		$dashboard_params[6]['is_requested'] = 'no';
		$dashboard_params[6]['display_on_dashboard'] = 'no';

		$dashboard_params[7]['dashboard_parameter_name'] = 'Bank Reconciliation';
		$dashboard_params[7]['result_method'] = 'callback_bank_reconcile_correct';
		$dashboard_params[7]['is_requested'] = 'no';
		$dashboard_params[7]['display_on_dashboard'] = 'yes';

		$dashboard_params[8]['dashboard_parameter_name'] = 'Cash Received';
		$dashboard_params[8]['result_method'] = 'test_cash_received_in_month_model';
		$dashboard_params[8]['is_requested'] = 'no';
		$dashboard_params[8]['display_on_dashboard'] = 'yes';



		return $dashboard_params;
	}

	private function test_bank_statement_uploaded_model() {

		$bank_statement_uploaded_data = array();

		//KE0200 array
		$bank_statement_uploaded_data[1]['fcp_id'] = 'KE0200';
		$bank_statement_uploaded_data[1]['file_exists'] = true;
		$bank_statement_uploaded_data[1]['closure_date'] = '2019-03-31';

		//KE0215 array
		$bank_statement_uploaded_data[2]['fcp_id'] = 'KE0215';
		$bank_statement_uploaded_data[2]['file_exists'] = false;
		$bank_statement_uploaded_data[2]['closure_date'] = '2019-03-31';

		//KE0300 array
		$bank_statement_uploaded_data[3]['fcp_id'] = 'KE0300';
		$bank_statement_uploaded_data[3]['file_exists'] = false;
		$bank_statement_uploaded_data[3]['closure_date'] = '2019-03-31';

		//KE0320 array
		$bank_statement_uploaded_data[4]['fcp_id'] = 'KE0320';
		$bank_statement_uploaded_data[4]['file_exists'] = true;
		$bank_statement_uploaded_data[4]['closure_date'] = '2019-03-31';

		//KE0540 array
		$bank_statement_uploaded_data[5]['fcp_id'] = 'KE0540';
		$bank_statement_uploaded_data[5]['file_exists'] = true;
		$bank_statement_uploaded_data[5]['closure_date'] = '2019-03-31';

		return $bank_statement_uploaded_data;

	}

	private function test_book_bank_cash_balance_data_model() {

		$bank_cash_balance_data = array();

		//KE0200 array
		$bank_cash_balance_data[1]['fcp_id'] = 'KE0200';
		$bank_cash_balance_data[1]['closure_date'] = '2019-03-31';
		$bank_cash_balance_data[1]['account_type'] = 'BC';
		$bank_cash_balance_data[1]['balance_amount'] = 12509.60;

		//KE0215 array
		$bank_cash_balance_data[2]['fcp_id'] = 'KE0215';
		$bank_cash_balance_data[2]['closure_date'] = '2019-03-31';
		$bank_cash_balance_data[2]['account_type'] = 'BC';
		$bank_cash_balance_data[2]['balance_amount'] = 10000300.52;

		//KE0300 array
		$bank_cash_balance_data[3]['fcp_id'] = 'KE0300';
		$bank_cash_balance_data[3]['closure_date'] = '2019-03-31';
		$bank_cash_balance_data[3]['account_type'] = 'BC';
		$bank_cash_balance_data[3]['balance_amount'] = 757880.12;

		//KE0320 array
		$bank_cash_balance_data[4]['fcp_id'] = 'KE0320';
		$bank_cash_balance_data[4]['closure_date'] = '2019-03-31';
		$bank_cash_balance_data[4]['account_type'] = 'BC';
		$bank_cash_balance_data[4]['balance_amount'] = 376898.02;

		//KE0540 array
		$bank_cash_balance_data[5]['fcp_id'] = 'KE0540';
		$bank_cash_balance_data[5]['closure_date'] = '2019-03-31';
		$bank_cash_balance_data[5]['account_type'] = 'BC';
		$bank_cash_balance_data[5]['balance_amount'] = 476987.00;

		return $bank_cash_balance_data;

	}

	private function test_deposit_in_transit_data_model() {

		$deposit_in_transit_data = array();

		//KE0200 array
		$deposit_in_transit_data[1]['fcp_id'] = 'KE0200';
		$deposit_in_transit_data[1]['deposit_in_transit_amount'] = 3330.49;
		$deposit_in_transit_data[1]['closure_date'] = '2019-03-31';

		//KE0215 array
		$deposit_in_transit_data[2]['fcp_id'] = 'KE0215';
		$deposit_in_transit_data[2]['deposit_in_transit_amount'] = 8987.29;
		$deposit_in_transit_data[2]['closure_date'] = '2019-03-31';

		//KE0300 array
		$deposit_in_transit_data[3]['fcp_id'] = 'KE0300';
		$deposit_in_transit_data[3]['deposit_in_transit_amount'] = 27987.19;
		$deposit_in_transit_data[3]['closure_date'] = '2019-03-31';

		//KE0320 array
		$deposit_in_transit_data[4]['fcp_id'] = 'KE0320';
		$deposit_in_transit_data[4]['deposit_in_transit_amount'] = 4098.89;
		$deposit_in_transit_data[4]['closure_date'] = '2019-03-31';

		//KE0540 array
		$deposit_in_transit_data[5]['fcp_id'] = 'KE0540';
		$deposit_in_transit_data[5]['deposit_in_transit_amount'] = 40456.89;
		$deposit_in_transit_data[5]['closure_date'] = '2019-03-31';

		return $deposit_in_transit_data;

	}

	private function test_mfr_submission_data_model() {

		$mfr_submission_data = array();

		//KE0200 array
		$mfr_submission_data[1]['fcp_id'] = 'KE0200';
		$mfr_submission_data[1]['closure_date'] = '2019-03-31';
		$mfr_submission_data[1]['submitted'] = 1;
		$mfr_submission_data[1]['submission_date'] = '2019-04-05';

		//KE0215 array
		$mfr_submission_data[2]['fcp_id'] = 'KE0215';
		$mfr_submission_data[2]['closure_date'] = '2019-03-31';
		$mfr_submission_data[2]['submitted'] = 0;
		$mfr_submission_data[2]['submission_date'] = '2019-04-10';

		//KE0300 array
		$mfr_submission_data[3]['fcp_id'] = 'KE0300';
		$mfr_submission_data[3]['closure_date'] = '2019-03-31';
		$mfr_submission_data[3]['submitted'] = 1;
		$mfr_submission_data[3]['submission_date'] = '2019-04-02';

		//KE0320 array
		$mfr_submission_data[4]['fcp_id'] = 'KE0320';
		$mfr_submission_data[4]['closure_date'] = '2019-03-31';
		$mfr_submission_data[4]['submitted'] = 1;
		$mfr_submission_data[4]['submission_date'] = '2019-04-03';

		//KE0540 array
		$mfr_submission_data[5]['fcp_id'] = 'KE0540';
		$mfr_submission_data[5]['closure_date'] = '2019-03-31';
		$mfr_submission_data[5]['submitted'] = 0;
		$mfr_submission_data[5]['submission_date'] = '2019-07-04';

		return $mfr_submission_data;
	}

	private function test_outstanding_cheques_data_model() {

		$outstanding_cheques_data = array();

		//KE0200 array
		$outstanding_cheques_data[1]['fcp_id'] = 'KE0200';
		$outstanding_cheques_data[1]['outstanding_cheque_amount'] = 300000.89;
		$outstanding_cheques_data[1]['closure_date'] = '2019-03-31';

		//KE0215 array
		$outstanding_cheques_data[2]['fcp_id'] = 'KE0215';
		$outstanding_cheques_data[2]['outstanding_cheque_amount'] = 17789.34;
		$outstanding_cheques_data[2]['closure_date'] = '2019-03-31';

		//KE0300 array
		$outstanding_cheques_data[3]['fcp_id'] = 'KE0300';
		$outstanding_cheques_data[3]['outstanding_cheque_amount'] = 889750.23;
		$outstanding_cheques_data[3]['closure_date'] = '2019-03-31';

		//KE0320 array
		$outstanding_cheques_data[4]['fcp_id'] = 'KE0320';
		$outstanding_cheques_data[4]['outstanding_cheque_amount'] = 435678.00;
		$outstanding_cheques_data[4]['closure_date'] = '2019-03-31';

		//KE0540 array
		$outstanding_cheques_data[5]['fcp_id'] = 'KE0540';
		$outstanding_cheques_data[5]['outstanding_cheque_amount'] = 29879.70;
		$outstanding_cheques_data[5]['closure_date'] = '2019-03-31';

		return $outstanding_cheques_data;

	}

	// private function test_fcp_local_pc_guideline_data_model() {
// 
		// $fcp_local_pc_guideline_data = array();
// 
		// //KE0200 array
		// $fcp_local_pc_guideline_data[1]['fcp_id'] = 'KE0200';
		// $fcp_local_pc_guideline_data[1]['pc_local_month_expense_limit'] = 0.89;
// 
		// //KE0215 array
		// $fcp_local_pc_guideline_data[2]['fcp_id'] = 'KE0215';
		// $fcp_local_pc_guideline_data[2]['pc_local_month_expense_limit'] = 0.89;
// 
		// //KE0300 array
		// $fcp_local_pc_guideline_data[3]['fcp_id'] = 'KE0300';
		// $fcp_local_pc_guideline_data[3]['pc_local_month_expense_limit'] = 98.09;
// 
		// //KE0320 array
		// $fcp_local_pc_guideline_data[4]['fcp_id'] = 'KE0320';
		// $fcp_local_pc_guideline_data[4]['pc_local_month_expense_limit'] = 17.1;
// 
		// //KE0540 array
		// $fcp_local_pc_guideline_data[5]['fcp_id'] = 'KE0540';
		// $fcp_local_pc_guideline_data[5]['pc_local_month_expense_limit'] = 12.9;
// 
		// return $fcp_local_pc_guideline_data;
// 
	// }

	private function test_statement_bank_balance_data_model() {

		$statement_bank_balance_data = array();

		//KE0200 array
		$statement_bank_balance_data[1]['fcp_id'] = 'KE0200';
		$statement_bank_balance_data[1]['statement_amount'] = 23998.90;
		$statement_bank_balance_data[1]['closure_date'] = '2019-03-31';

		//KE0215 array
		$statement_bank_balance_data[2]['fcp_id'] = 'KE0215';
		$statement_bank_balance_data[2]['statement_amount'] = 100298.60;
		$statement_bank_balance_data[2]['closure_date'] = '2019-03-31';

		//KE0300 array
		$statement_bank_balance_data[3]['fcp_id'] = 'KE0300';
		$statement_bank_balance_data[3]['statement_amount'] = 1619643.16;
		$statement_bank_balance_data[3]['closure_date'] = '2019-03-31';

		//KE0320 array
		$statement_bank_balance_data[4]['fcp_id'] = 'KE0320';
		$statement_bank_balance_data[4]['statement_amount'] = 238989.71;
		$statement_bank_balance_data[4]['closure_date'] = '2019-03-31';

		//KE0540 array
		$statement_bank_balance_data[5]['fcp_id'] = 'KE0540';
		$statement_bank_balance_data[5]['statement_amount'] = 97600.81;
		$statement_bank_balance_data[5]['closure_date'] = '2019-03-31';

		return $statement_bank_balance_data;

	}


	function test_total_for_pc_data_model($month) {

		$total_pc_data = array();

		//KE0200 array
		$total_pc_data[1]['fcp_id'] = 'KE0200';
		$total_pc_data[1]['total'] = 23998.90;
		$total_pc_data[1]['voucher_type'] = 'PC';
		$total_pc_data[1]['transaction_date'] = '2019-03-31';

		//KE0215 array
		$total_pc_data[2]['fcp_id'] = 'KE0215';
		$total_pc_data[2]['total'] = 23998.90;
		$total_pc_data[2]['voucher_type'] = 'PC';
		$total_pc_data[2]['transaction_date'] = '2019-03-31';

		//KE0300 array
		$total_pc_data[3]['fcp_id'] = 'KE0300';
		$total_pc_data[3]['total'] = 23998.90;
		$total_pc_data[3]['voucher_type'] = 'PC';
		$total_pc_data[3]['transaction_date'] = '2019-03-31';

		//KE0320 array
		$total_pc_data[4]['fcp_id'] = 'KE0320';
		$total_pc_data[4]['total'] = 23998.90;
		$total_pc_data[4]['voucher_type'] = 'PC';
		$total_pc_data[4]['transaction_date'] = '2019-03-31';

		//KE0540 array
		$total_pc_data[5]['fcp_id'] = 'KE0540';
		$total_pc_data[5]['total'] = 23998.90;
		$total_pc_data[5]['voucher_type'] = 'PC';
		$total_pc_data[5]['transaction_date'] = '2019-03-31';

		return $transaction_arrays;
	}

    function test_uncleared_cash_recieved_data_model($month) {

		$uncleared_cash_recieved_data = array();

		//KE0200 array
		$uncleared_cash_recieved_data[1]['fcp_id'] = 'KE0200';
		$uncleared_cash_recieved_data[1]['totals'] = 23998.90;

		//KE0215 array
		$uncleared_cash_recieved_data[2]['fcp_id'] = 'KE0215';
		$uncleared_cash_recieved_data[2]['totals'] = 23998.90;

		//KE0300 array
		$uncleared_cash_recieved_data[3]['fcp_id'] = 'KE0300';
		$uncleared_cash_recieved_data[3]['totals'] = 23998.90;

		//KE0320 array
		$uncleared_cash_recieved_data[4]['fcp_id'] = 'KE0320';
		$uncleared_cash_recieved_data[4]['totals'] = 23998.90;

		//KE0540 array
		$uncleared_cash_recieved_data[5]['fcp_id'] = 'KE0540';
		$uncleared_cash_recieved_data[5]['totals'] = 23998.90;

		return $uncleared_cash_recieved_data;
	}

	function test_uncleared_cheques_data_model($month) {

		$uncleared_cheques_data = array();

		//KE0200 array
		$uncleared_cheques_data[1]['fcp_id'] = 'KE0200';
		$uncleared_cheques_data[1]['totals'] = 23998.90;

		//KE0215 array
		$uncleared_cheques_data[2]['fcp_id'] = 'KE0215';
		$uncleared_cheques_data[2]['totals'] = 23998.90;

		//KE0300 array
		$uncleared_cheques_data[3]['fcp_id'] = 'KE0300';
		$uncleared_cheques_data[3]['totals'] = 23998.90;

		//KE0320 array
		$uncleared_cheques_data[4]['fcp_id'] = 'KE0320';
		$uncleared_cheques_data[4]['totals'] = 23998.90;

		//KE0540 array
		$uncleared_cheques_data[5]['fcp_id'] = 'KE0540';
		$uncleared_cheques_data[5]['totals'] = 23998.90;

		return $uncleared_cheques_data;
	}


	 function test_cash_received_in_month_model() {
		$cash_received_in_month_data = array();

		//KE0200 array
		$cash_received_in_month_data[1]['KE0200']['fcp_id'] = 'KE0200';
		$cash_received_in_month_data[1]['KE0200']['cash_received_in_month_amount'] = 23998.90;
		$cash_received_in_month_data[1]['KE0200']['closure_date'] = '2019-03-31';

		//KE0215 array
		$cash_received_in_month_data[2]['KE0215']['fcp_id'] = 'KE0215';
		$cash_received_in_month_data[2]['KE0215']['cash_received_in_month_amount'] = 100298.60;
		$cash_received_in_month_data[2]['KE0215']['closure_date'] = '2019-03-31';

		//KE0300 array
		$cash_received_in_month_data[3]['KE0300']['fcp_id'] = 'KE0300';
		$cash_received_in_month_data[3]['KE0300']['cash_received_in_month_amount'] = 1619643.16;
		$statement_bank_balance_data[3]['KE0300']['closure_date'] = '2019-03-31';

		//KE0320 array
		$cash_received_in_month_data[4]['KE0300']['fcp_id'] = 'KE0320';
		$cash_received_in_month_data[4]['KE0300']['cash_received_in_month_amount'] = 238989.71;
		$cash_received_in_month_data[4]['KE0300']['closure_date'] = '2019-03-31';

		//KE0540 array
		$cash_received_in_month_data[5]['KE0540']['fcp_id'] = 'KE0540';
		$cash_received_in_month_data[5]['KE0540']['cash_received_in_month_amount'] = 97600.81;
		$cash_received_in_month_data[5]['KE0540']['closure_date'] = '2019-03-31';

		return $cash_received_in_month_data;
	}

	public function  test_pc_limit_per_transaction_by_type_model(){
		$pc_per_withdrawal_limit_data = array();

		//KE0200 array
		$pc_per_withdrawal_limit_data[1]['KE0200']['fcp_id'] = 'KE0200';
		$pc_per_withdrawal_limit_data[1]['KE0200']['limit_compliance_flag'] = 'yes';

		//KE0215 array
		$pc_per_withdrawal_limit_data[2]['KE0215']['fcp_id'] = 'KE0215';
		$pc_per_withdrawal_limit_data[2]['KE0215']['limit_compliance_flag'] = 'no';

		//KE0300 array
		$pc_per_withdrawal_limit_data[3]['KE0300']['fcp_id'] = 'KE0300';
		$pc_per_withdrawal_limit_data[3]['KE0300']['limit_compliance_flag'] = 'no';

		//KE0320 array
		$pc_per_withdrawal_limit_data[4]['KE0320']['fcp_id'] = 'KE0320';
		$pc_per_withdrawal_limit_data[4]['KE0320']['limit_compliance_flag'] = 'yes';

		//KE0540 array
		$pc_per_withdrawal_limit_data[5]['KE0540']['fcp_id'] = 'KE0540';
		$pc_per_withdrawal_limit_data[5]['KE0540']['limit_compliance_flag'] = 'yes';

		return $pc_per_withdrawal_limit_data;
	}

	private function  test_project_with_pc_guideline_limits_model(){
		$project_with_pc_guideline_limit_data = array();

		//KE0200 array
		$project_with_pc_guideline_limit['KE0200']['pc_local_withdrawal_limit'] = 15000;
		$project_with_pc_guideline_limit['KE0200']['pc_local_expense_transaction_limit'] = 5000;
		$project_with_pc_guideline_limit['KE0200']['pc_local_month_expense_limit'] = 150000;

		//KE0215 array
		$project_with_pc_guideline_limit['KE0215']['pc_local_withdrawal_limit'] = 16000;
		$project_with_pc_guideline_limit['KE0215']['pc_local_expense_transaction_limit'] = 4000;
		$project_with_pc_guideline_limit['KE0215']['pc_local_month_expense_limit'] = 200000;

		//KE0300 array
		$project_with_pc_guideline_limit['KE0300']['pc_local_withdrawal_limit'] = 10000;
		$project_with_pc_guideline_limit['KE0300']['pc_local_expense_transaction_limit'] = 8000;
		$project_with_pc_guideline_limit['KE0300']['pc_local_month_expense_limit'] = 250000;

		//KE0320 array
		$project_with_pc_guideline_limit['KE0320']['pc_local_withdrawal_limit'] = 15000;
		$project_with_pc_guideline_limit['KE0320']['pc_local_expense_transaction_limit'] = 10000;
		$project_with_pc_guideline_limit['KE0320']['pc_local_month_expense_limit'] = 180000;

		//KE0540 array
		$project_with_pc_guideline_limit['KE0540']['pc_local_withdrawal_limit'] = 20000;
		$project_with_pc_guideline_limit['KE0540']['pc_local_expense_transaction_limit'] = 10000;
		$project_with_pc_guideline_limit['KE0540']['pc_local_month_expense_limit'] = 250000;

		return $project_with_pc_guideline_limit;
	}

	//Prod Models Methods

	private function prod_project_with_pc_guideline_limits_model(){
		$this->benchmark->mark('prod_project_with_pc_guideline_limits_model_start');
		$this->db->select('icpNo as fcp_id');
		$this->db->select(array('pc_local_withdrawal_limit','pc_local_expense_transaction_limit','pc_local_month_expense_limit'));
		$project_with_pc_guideline_limits = $this->db->get_where('projectsdetails',array('status'=>1))->result_array();

		$grouped_by_fcp_id = $this->group_data_by_fcp_id($project_with_pc_guideline_limits);
		
		$this->benchmark->mark('prod_project_with_pc_guideline_limits_model_end');
		
		return $grouped_by_fcp_id;
	}
	
	// function get_pc_local_guide_line_data(){
// 		
		// $month = "2019-03-01";
// 		
		// $types_array = array('per_withdrawal','per_transaction','per_month');
// 		
		// $results = array();
// 			
		// foreach($types_array as $type){
			// $call_statement = 'CALL get_max_pc_withdrawal_transactions("'.date('Y-m-01',strtotime($month)).'","'.date('Y-m-t',strtotime($month)).'","'.$type.'")';
// 		
			// $stmt = $this->db->conn_id->prepare($call_statement);
			// $result = $stmt->execute();
			// $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
// 			
			// $results[$type] = $result;
		// }
// 		
		// return $results;
	// }
	
	// public function prod_pc_limit_per_month_model($month){
		// $project_with_pc_guideline_limits = $this->prod_project_with_pc_guideline_limits_model();
		// $limit_type = "per_month";	
		// $db_call = 'CALL get_max_pc_withdrawal_transactions("'.date('Y-m-01',strtotime($month)).'","'.date('Y-m-t',strtotime($month)).'","'.$limit_type.'")';
// 
		// $pc_withdrawal_result = $this->db->query($db_call)->result_array();
// 
		// $pc_per_withdrawal_limit = array();
// 
		// foreach($pc_withdrawal_result as $pc_withdrawal){
			// $pc_per_withdrawal_limit[$pc_withdrawal['fcp_id']]['fcp_id'] = $pc_withdrawal['fcp_id'];
			// $pc_per_withdrawal_limit[$pc_withdrawal['fcp_id']]['limit_compliance_flag'] = 'No';
// 
			// if(($project_with_pc_guideline_limits[$pc_withdrawal['fcp_id']][$pc_guideline_column_name] <=> 0.00) == 0){
				// $pc_per_withdrawal_limit[$pc_withdrawal['fcp_id']]['limit_compliance_flag'] = 'Not Set';
			// }elseif($project_with_pc_guideline_limits[$pc_withdrawal['fcp_id']][$pc_guideline_column_name] > $pc_withdrawal['cost'] ){
// 
				// $pc_per_withdrawal_limit[$pc_withdrawal['fcp_id']]['limit_compliance_flag'] = 'Yes';
// 
			// }
		// }
// 
		// return $pc_withdrawal_result;
	// }
	
	// public function prod_pc_limit_per_transaction_by_type_model($month,$limit_type = 'per_withdrawal'){
// 			
		// $this->benchmark->mark('prod_pc_limit_per_transaction_by_type_model_start');
// 		
		// $pc_guideline_column_name = 'pc_local_withdrawal_limit';
// 
		// if($limit_type == 'per_transaction'){
			// $pc_guideline_column_name = 'pc_local_expense_transaction_limit';
		// }elseif($limit_type == 'per_month'){
			// $pc_guideline_column_name = 'pc_local_month_expense_limit';
		// }
// 
		// $project_with_pc_guideline_limits = $this->prod_project_with_pc_guideline_limits_model();
// 
		// $db_call = 'CALL get_max_pc_withdrawal_transactions("'.date('Y-m-01',strtotime($month)).'","'.date('Y-m-t',strtotime($month)).'","'.$limit_type.'")';
// 
		// $pc_withdrawal_result = $this->db->query($db_call)->result_array();
		// //$pc_withdrawal_result = $this->pc_local_guide_line_data['per_month'];
// 
		// $pc_per_withdrawal_limit = array();
// 
		// foreach($pc_withdrawal_result as $pc_withdrawal){
			// $pc_per_withdrawal_limit[$pc_withdrawal['fcp_id']]['fcp_id'] = $pc_withdrawal['fcp_id'];
			// $pc_per_withdrawal_limit[$pc_withdrawal['fcp_id']]['limit_compliance_flag'] = 'No';
// 
			// if(($project_with_pc_guideline_limits[$pc_withdrawal['fcp_id']][$pc_guideline_column_name] <=> 0.00) == 0){
				// $pc_per_withdrawal_limit[$pc_withdrawal['fcp_id']]['limit_compliance_flag'] = 'Not Set';
			// }elseif($project_with_pc_guideline_limits[$pc_withdrawal['fcp_id']][$pc_guideline_column_name] > $pc_withdrawal['cost'] ){
// 
				// $pc_per_withdrawal_limit[$pc_withdrawal['fcp_id']]['limit_compliance_flag'] = 'Yes';
// 
			// }
		// }
// 
		// $this->benchmark->mark('prod_pc_limit_per_transaction_by_type_model_end');
// 
		// return $pc_per_withdrawal_limit;
	// }
	
	function test_guideline(){
		return $this->prod_project_with_pc_guideline_limits_model();
	}
	
	function test_pc_guideline_call(){
		$month = "2018-04-01";
		$limit_type = 'per_withdrawal';
		$db_call = 'CALL get_max_pc_withdrawal_transactions("'.date('Y-m-01',strtotime($month)).'","'.date('Y-m-t',strtotime($month)).'","'.$limit_type.'")';

		$pc_withdrawal_result = $this->db->query($db_call)->result_array();
		
		return $pc_withdrawal_result;
	}
	
	public function prod_pc_limit_by_type_model($month){
			
		$this->benchmark->mark('prod_pc_limit_by_type_model_start');
		
		
		$project_with_pc_guideline_limits = $this->prod_project_with_pc_guideline_limits_model();
		
		$type_array = array('per_withdrawal'=>'pc_local_withdrawal_limit','per_month'=>'pc_local_month_expense_limit','per_transaction'=>'pc_local_expense_transaction_limit');
		
		$pc_per_withdrawal_limit = array();
		
		foreach($type_array as $limit_type=>$pc_guideline_column_name){
			//$this->db->cache_on();	
			$db_call = 'CALL get_max_pc_withdrawal_transactions("'.date('Y-m-01',strtotime($month)).'","'.date('Y-m-t',strtotime($month)).'","'.$limit_type.'")';

			$pc_withdrawal_result = $this->db->query($db_call)->result_array();
			//$this->db->cache_off();
	
			foreach($pc_withdrawal_result as $pc_withdrawal){
				$pc_per_withdrawal_limit[$limit_type][$pc_withdrawal['fcp_id']]['fcp_id'] = $pc_withdrawal['fcp_id'];
				$pc_per_withdrawal_limit[$limit_type][$pc_withdrawal['fcp_id']]['limit_compliance_flag'] = 'No';
	
				if(($project_with_pc_guideline_limits[$pc_withdrawal['fcp_id']][$pc_guideline_column_name] <=> 0.00) == 0){
					$pc_per_withdrawal_limit[$limit_type][$pc_withdrawal['fcp_id']]['limit_compliance_flag'] = 'Not Set';
				}elseif($project_with_pc_guideline_limits[$pc_withdrawal['fcp_id']][$pc_guideline_column_name] > $pc_withdrawal['cost'] ){
					
					$pc_per_withdrawal_limit[$limit_type][$pc_withdrawal['fcp_id']]['limit_compliance_flag'] = 'Yes';
	
				}
			}	
		}

		$this->benchmark->mark('prod_pc_limit_by_type_model_end');
		
		return $pc_per_withdrawal_limit;
	}

	public function prod_cash_received_in_month_model($month){
		$this->benchmark->mark('prod_cash_received_in_month_model_start');
		$this->db->cache_on();
		$query_conditon = "voucher_header.TDate BETWEEN '".date('Y-m-01',strtotime($month))."' AND '".date("Y-m-t",strtotime($month))."' AND voucher_header.VType='CR'";

		$this->db->select_sum('voucher_body.Cost');
		$this->db->select(array('voucher_header.icpNo'));
		$this->db->where($query_conditon);
		$this->db->where(array('ci_income='=>1));
		$this->db->group_by(array('voucher_header.icpNo'));
		$this->db->join('voucher_body','voucher_body.hID=voucher_header.hID');
		$this->db->join('accounts','accounts.AccNo = voucher_body.AccNo');
		$cash_received_in_month = $this->db->get('voucher_header')->result_object();
		$this->db->cache_off();

		$cr_array = array();

		$cnt = 0;
		foreach($cash_received_in_month as $row){
			$cr_array[$row->icpNo]['fcp_id'] = $row->Cost;
			$cr_array[$row->icpNo]['closure_date'] = $row->Cost;
			$cr_array[$row->icpNo]['cash_received_in_month_amount'] = $row->Cost;

			$cnt++;
		}
		
		$this->benchmark->mark('prod_cash_received_in_month_model_end');
		
		return $cr_array;
	}

	public function prod_fcps_with_risk_model($month,$count_of_fcps,$fcp_in_dashboard) {
		// Check if there is a run, if so check if there is a change and only pouplate the fcps with change
		// Check if the month has a run and update the dashboard_run table
		$month_change = $this->db->get_where('dashboard_change',array('status'=>1,'month'=>$month));
        
        $columned_fcps_with_change = array_column($month_change->result_array(),'projectsdetails_id');
        
		$fcp_array = array();
		$data = [];
		
		// Check if there are any active FCPs that are not in the dashboard and the dashboard for the month has already run
		if(($count_of_fcps > $fcp_in_dashboard) && $fcp_in_dashboard > 0 && $month_change->num_rows() > 0){
            $this->db->join('dashboard_change','dashboard_change.projectsdetails_id=projectsdetails.ID');
		}

		$data = $this -> db -> get_where($this -> table_prefix . 'projectsdetails',array('projectsdetails.status'=>1)) -> result_array();

		foreach ($data as $fcp) {

			$fcp_array[$fcp['ID']]['fcp_id'] = $fcp['icpNo'];
			$fcp_array[$fcp['ID']]['risk'] = $fcp['risk'];
		}

		$active_fcps_missing_in_dashboard = array_values($this->active_fcps_missing_in_dashboard($month));

        if(count($active_fcps_missing_in_dashboard) > 0){
            $this->db->select(array('ID','icpNo','risk'));
            $this->db->where_in('ID',$active_fcps_missing_in_dashboard);
            $fcps_missing_in_dashboard = $this -> db -> get($this -> table_prefix . 'projectsdetails') -> result_array();
            
            foreach ($fcps_missing_in_dashboard as $fcp) {

                $fcp_array[$fcp['ID']]['fcp_id'] = $fcp['icpNo'];
                $fcp_array[$fcp['ID']]['risk'] = $fcp['risk'];
            }
        }

		return $fcp_array;
	}

	function active_fcps_missing_in_dashboard($month){

        if($this->config->item('fcp_test_loops') > 0) $this->db->limit($this->config->item('fcp_test_loops'),0);

        $active_fcps = $this->db-> select('ID as projectsdetails_id') ->get_where($this -> table_prefix . 'projectsdetails',array('projectsdetails.status'=>1)) -> result_array();
        
        $dashboard_fcps = $this->db-> select('projectsdetails_id')->get_where('dashboard_header',array("month"=>$month))->result_array();

        $active_fcps_projectsdetails_id = array_column($active_fcps,'projectsdetails_id');
        $dashboard_fcps_projectsdetails_id = array_column($dashboard_fcps,'projectsdetails_id');

        $active_fcps_missing_in_dashboard = array_diff($active_fcps_projectsdetails_id,$dashboard_fcps_projectsdetails_id);

        //echo json_encode($active_fcps_missing_in_dashboard);
        return $active_fcps_missing_in_dashboard;
    }

	private function prod_bank_statement_uploaded_model($month_bank_statement_uploaded) {
		$this->benchmark->mark('prod_bank_statement_uploaded_model_start');
		$files = array();
		try {
			$dir_path = 'uploads/bank_statements';
			$dir = new DirectoryIterator($dir_path);

			$counter = 1;

			foreach ($dir as $fileinfo) {
				if (!$fileinfo -> isDot()) {

					$file_path = $dir_path . '/' . $fileinfo -> getFilename() . '/' . date('Y-m', strtotime($month_bank_statement_uploaded));

					$yes_no_flag = false;

					if (file_exists($file_path)) {

						if ($this -> checkFolderIsEmptyOrNot($file_path)) {
							$yes_no_flag = true;
						}
					}

					$files[$counter]['fcp_id'] = $fileinfo -> getFilename();
					$files[$counter]['file_exists'] = $yes_no_flag;
					$files[$counter]['closure_date'] = $month_bank_statement_uploaded;

					$counter++;

				}
			}
		} catch(Exception $e) {

		}
		$this->benchmark->mark('prod_bank_statement_uploaded_model_end');
		return $files;

	}

	private function prod_statement_bank_balance_data_model($month) {
		$this->benchmark->mark('prod_statement_bank_balance_data_model_start');
		//$this -> db -> cache_on();
		$statement_bank_balance = $this -> db -> get_where($this -> table_prefix . 'view_funds_statement_balance', array('closure_date' => $month)) -> result_array();
		//$this -> db -> cache_off();
		$this->benchmark->mark('prod_statement_bank_balance_data_model_end');
		return $statement_bank_balance;
	}

	private function prod_book_bank_cash_balance_data_model($month) {
		$this->benchmark->mark('prod_book_bank_cash_balance_data_model_start');
		//$this -> db -> cache_on();
		$bank_cash_balance_data = $this -> db -> get_where($this -> table_prefix . 'view_book_bank_balance', array('closure_date' => $month)) -> result_array();
		//$this -> db -> cache_off();
		$this->benchmark->mark('prod_book_bank_cash_balance_data_model_end');
		return $bank_cash_balance_data;	
	}

	//We will have to pass month aurgumet in prod models
	private function prod_mfr_submission_data_model($month) {
		$this->benchmark->mark('prod_mfr_submission_data_model_start');
		//$this -> db -> cache_on();
		$mfr_submission_data = $this -> db -> get_where($this -> table_prefix . 'view_opening_funds_balance', array('closure_date' => $month)) -> result_array();
		//$this -> db -> cache_off();
		$this->benchmark->mark('prod_mfr_submission_data_model_end');
		return $mfr_submission_data;
	}

	private function prod_dashboard_parameters_model() {
		$this->benchmark->mark('prod_dashboard_parameters_model_start');	
		$dashboard_params = array();

		$data = $this -> db -> get($this -> table_prefix . 'dashboard_parameter') -> result_array();

		foreach ($data as $parameter) {

			$dashboard_params[$parameter['dashboard_parameter_id']]['dashboard_parameter_name'] = $parameter['dashboard_parameter_name'];
			$dashboard_params[$parameter['dashboard_parameter_id']]['result_method'] = $parameter['result_method'];
			$dashboard_params[$parameter['dashboard_parameter_id']]['is_requested'] = $parameter['is_requested'];
			$dashboard_params[$parameter['dashboard_parameter_id']]['display_on_dashboard'] = $parameter['display_on_dashboard'];
		}
		$this->benchmark->mark('prod_mfr_submission_data_model_end');
		return $dashboard_params;
		
	}

	//Switch Environment method for model (prod/test) called in callback methods and build_dashboard_array method

	public function switch_environment(...$args) {

		$this->benchmark->mark('switch_environment_start');	

		$month = array_shift($args);
		$test_method = array_shift($args);
		$prod_method = array_shift($args);
		$extra_args =  !empty($args)?implode(',', $args):"";

		if ($this -> config -> item('environment') == 'test') {
			$this->benchmark->mark('switch_environment_end');
			return $this -> $test_method();
		} elseif ($this -> config -> item('environment') == 'prod') {
			$this->benchmark->mark('switch_environment_end');
			return $this -> $prod_method($month,$extra_args);
		}

	}

	//Transaction methods
	

	function get_uncleared_transactions($month) {
		$this->benchmark->mark('get_uncleared_transactions_start');	
		
		$vtype_array = array('CHQ','CR');
		
		$transaction_array = array();
		
		foreach($vtype_array as $vtype){
			$amount_key = "";
				$table = "";
		
				if ($vtype == 'CHQ') {
					$amount_key = "outstanding_cheque_amount";
					$table = 'view_voucher_with_oustanding_cheques';
				} elseif ('CR') {
					$amount_key = "deposit_in_transit_amount";
					$table = 'view_voucher_with_deposit_deposit_in_transit';
				}
		
				$first_day_of_month = date('Y-m-01', strtotime($month));
				$last_day_of_month = date('Y-m-t', strtotime($month));
		
				//$this -> db -> cache_on();
		
				$this -> db -> select_sum($amount_key);
				$this -> db -> select(array('fcp_id', 'voucher_raised_date', 'clearance_state', 'clearance_date', 'voucher_type'));
				$this -> db -> group_by(array('voucher_type', 'fcp_id'));
		
				$condition_array = array();
		
				//Query string conditions
				$where_string = "(";
				//transactions_raised_in_month_not_cleared
				$where_string .= "(voucher_raised_date BETWEEN '" . $first_day_of_month . "' AND '" . $last_day_of_month . "' AND clearance_state = 0 AND clearance_date = '0000-00-00')";
				//transactions_raised_in_month_cleared_in_future
				$where_string .= " OR (voucher_raised_date BETWEEN '" . $first_day_of_month . "' AND '" . $last_day_of_month . "' AND clearance_state = 1 AND clearance_date > '" . $last_day_of_month . "')";
				//transactions_raised_in_past_cleared_in_future
				$where_string .= " OR (voucher_raised_date <= '" . $first_day_of_month . "' AND clearance_state = 1 AND clearance_date > '" . $last_day_of_month . "')";
				//transactions_raised_in_past_not_cleared
				$where_string .= " OR (voucher_raised_date <= '" . $first_day_of_month . "' AND clearance_state = 0 AND clearance_date = '0000-00-00')";
		
				$where_string .= ")";
		
				$this -> db -> where($where_string);
		
				$transaction_array[$vtype] = $this -> db -> get($this -> table_prefix . $table) -> result_array();
		
				//$this -> db -> cache_off();	
			}
		
		
		
		$this->benchmark->mark('get_uncleared_transactions_end');	
		
		return $transaction_array;

	}
	
	// function get_uncleared_transactions($vtype, $month) {
		// $this->benchmark->mark('get_uncleared_transactions_start');	
		// $amount_key = "";
		// $table = "";
// 
		// if ($vtype == 'CHQ') {
			// $amount_key = "outstanding_cheque_amount";
			// $table = 'view_voucher_with_oustanding_cheques';
		// } elseif ('CR') {
			// $amount_key = "deposit_in_transit_amount";
			// $table = 'view_voucher_with_deposit_deposit_in_transit';
		// }
// 
		// $first_day_of_month = date('Y-m-01', strtotime($month));
		// $last_day_of_month = date('Y-m-t', strtotime($month));
// 
		// $this -> db -> cache_on();
// 
		// $this -> db -> select_sum($amount_key);
		// $this -> db -> select(array('fcp_id', 'voucher_raised_date', 'clearance_state', 'clearance_date', 'voucher_type'));
		// $this -> db -> group_by(array('voucher_type', 'fcp_id'));
// 
		// $condition_array = array();
// 
		// //Query string conditions
		// $where_string = "(";
		// //transactions_raised_in_month_not_cleared
		// $where_string .= "(voucher_raised_date BETWEEN '" . $first_day_of_month . "' AND '" . $last_day_of_month . "' AND clearance_state = 0 AND clearance_date = '0000-00-00')";
		// //transactions_raised_in_month_cleared_in_future
		// $where_string .= " OR (voucher_raised_date BETWEEN '" . $first_day_of_month . "' AND '" . $last_day_of_month . "' AND clearance_state = 1 AND clearance_date > '" . $last_day_of_month . "')";
		// //transactions_raised_in_past_cleared_in_future
		// $where_string .= " OR (voucher_raised_date <= '" . $first_day_of_month . "' AND clearance_state = 1 AND clearance_date > '" . $last_day_of_month . "')";
		// //transactions_raised_in_past_not_cleared
		// $where_string .= " OR (voucher_raised_date <= '" . $first_day_of_month . "' AND clearance_state = 0 AND clearance_date = '0000-00-00')";
// 
		// $where_string .= ")";
// 
		// $this -> db -> where($where_string);
// 
		// $transaction_array = $this -> db -> get($this -> table_prefix . $table) -> result_array();
// 
		// $this -> db -> cache_off();
// 		
		// $this->benchmark->mark('get_uncleared_transactions_end');	
// 		
		// return $transaction_array;
// 
	// }

	function prod_deposit_in_transit_data_model($month) {
		
		$this->benchmark->mark('prod_deposit_in_transit_data_model_start');
		
		$transaction_arrays = array();

		$get_uncleared_transactions = $this -> get_uncleared_transactions($month)['CR'];

		foreach ($get_uncleared_transactions as $hid => $transaction) {
			$transaction_arrays[$transaction['fcp_id']]['fcp_id'] = $transaction['fcp_id'];
			$transaction_arrays[$transaction['fcp_id']]['closure_date'] = $month;
			$transaction_arrays[$transaction['fcp_id']]['deposit_in_transit_amount'] = $transaction['deposit_in_transit_amount'];
		}
		
		$this->benchmark->mark('prod_deposit_in_transit_data_model_end');
		
		return $transaction_arrays;
	}

	function prod_outstanding_cheques_data_model($month) {
		$this->benchmark->mark('prod_outstanding_cheques_data_model_start');
		$transaction_arrays = array();

		$get_uncleared_transactions = $this -> get_uncleared_transactions($month)['CHQ'];

		$fcps_array = array_column($get_uncleared_transactions, 'fcp_id');

		foreach ($get_uncleared_transactions as $row_key => $transaction) {

			$transaction_arrays[$transaction['fcp_id']]['fcp_id'] = $transaction['fcp_id'];
			$transaction_arrays[$transaction['fcp_id']]['closure_date'] = $month;
			$transaction_arrays[$transaction['fcp_id']]['outstanding_cheque_amount'] = $transaction['outstanding_cheque_amount'];
		}
		$this->benchmark->mark('prod_outstanding_cheques_data_model_end');
		return $transaction_arrays;
	}

	function prod_total_for_pc_data_model($month) {
		$this->benchmark->mark('prod_total_for_pc_data_model_start');
		//Construct the array to dsipla the total transactions from PC
		$total_pc_amount_in_amonth = array();

		$total_pcs = $this -> calculate_pc_chqs_totals('PC', $month);

		foreach ($total_pcs as $row_key => $total_pc) {

			$total_pc_amount_in_amonth[$total_pc['icpNo']]['fcp_id'] = $total_pc['icpNo'];
			$total_pc_amount_in_amonth[$total_pc['icpNo']]['cost'] = $total_pc['cost'];
		}
		$this->benchmark->mark('prod_total_for_pc_data_model_end');
		return $total_pc_amount_in_amonth;
	}

	function prod_total_for_chq_data_model($month) {
		$this->benchmark->mark('prod_total_for_chq_data_model_start');
		$total_chq_amount_in_amonth = array();

		$total_chqs = $this -> calculate_pc_chqs_totals('CHQ', $month);

		foreach ($total_chqs as $row_key => $total_chq) {

			$total_chq_amount_in_amonth[$total_chq['icpNo']]['fcp_id'] = $total_chq['icpNo'];
			$total_chq_amount_in_amonth[$total_chq['icpNo']]['cost'] = $total_chq['cost'];
		}
		$this->benchmark->mark('prod_total_for_chq_data_model_end');
		return $total_chq_amount_in_amonth;
	}

    function prod_uncleared_cash_recieved_data_model($month) {
		$this->benchmark->mark('prod_uncleared_cash_recieved_data_model_start');
		$uncleared_cash_recieved_in_amonth = array();

		$total_uncleared_cash_recieved= $this -> calculate_uncleared_cash_recieved_and_chqs('CR', $month);

		foreach ($total_uncleared_cash_recieved as $row_key => $total_uncleared_cr) {

			$uncleared_cash_recieved_in_amonth[$total_uncleared_cr['icpNo']]['fcp_id'] = $total_uncleared_cr['icpNo'];
			$uncleared_cash_recieved_in_amonth[$total_uncleared_cr['icpNo']]['totals'] = $total_uncleared_cr['totals'];
		}
		$this->benchmark->mark('prod_uncleared_cash_recieved_data_model_end');
		return $uncleared_cash_recieved_in_amonth;
	}

	 function prod_uncleared_cheques_data_model($month) {
		$this->benchmark->mark('prod_uncleared_cheques_data_model_start');
		$uncleared_cheques_in_amonth = array();

		$total_uncleared_cheques= $this -> calculate_uncleared_cash_recieved_and_chqs('CHQ', $month);

		foreach ($total_uncleared_cheques as $row_key => $total_uncleared_chqs) {

			$uncleared_cheques_in_amonth[$total_uncleared_chqs['icpNo']]['fcp_id'] = $total_uncleared_chqs['icpNo'];
			$uncleared_cheques_in_amonth[$total_uncleared_chqs['icpNo']]['totals'] = $total_uncleared_chqs['totals'];
		}
		$this->benchmark->mark('prod_uncleared_cheques_data_model_end');
		return $uncleared_cheques_in_amonth;
	}


	private function calculate_pc_chqs_totals($vtype, $month) {
		$this->benchmark->mark('calculate_pc_chqs_totals_start');
		$total_pc_or_chqs = array();

		//Get the first and last of the month
		$first_day_of_month = date('Y-m-01', strtotime($month));
		$last_day_of_month = date('Y-m-t', strtotime($month));

		//$this -> db -> cache_on();

		$this -> db -> select_sum('voucher_body.cost');
		$this -> db -> select(array('voucher_header.icpNo', 'voucher_header.vtype'));
		$this -> db -> join("voucher_body", "voucher_body.hid=voucher_header.hid");
		$this -> db -> group_by(array('voucher_header.icpNo', 'voucher_header.vtype'));
		$this -> db -> where('voucher_header.vtype', $vtype);
		$this -> db -> where('voucher_header.tdate >= ', $first_day_of_month);
		$this -> db -> where('voucher_header.tdate <= ', $last_day_of_month);

		$total_pc_or_chqs = $this -> db -> get("voucher_header") -> result_array();

		//$this -> db -> cache_off();
		$this->benchmark->mark('calculate_pc_chqs_totals_end');
		return $total_pc_or_chqs;

	}

	private function calculate_uncleared_cash_recieved_and_chqs($vtype, $month) {
		$this->benchmark->mark('calculate_uncleared_cash_recieved_and_chqs_start');
		$count_of_cr_and_chq = array();

		//Get the first and last of the month
		$first_day_of_month = date('Y-m-01', strtotime($month));
		$last_day_of_month = date('Y-m-t', strtotime($month));

		//select icpNo, tdate,chqstate,clrmonth from voucher_header where chqstate=0 limit 10

		//$this -> db -> cache_on();

		$this->db->select(array('icpNo'));
		$this->db->select_sum('totals');
		//$this->db->select('totals');
		$this -> db -> group_by(array('icpNo','VType'));
		// $this -> db -> where('TDate >= ', $first_day_of_month);
		// $this -> db -> where('TDate <= ', $last_day_of_month);
		$this -> db -> where('VType',$vtype);
		$this->db->where(array('chqState'=>0));
		$this->db->where('DATEDIFF("'.$last_day_of_month.'", TDate) >',$this->config->item('allowed_uncleared_days'));
		
		$count_of_cr_and_chq = $this -> db -> get("voucher_header") -> result_array();

        //$this -> db -> cache_off();
		$this->benchmark->mark('calculate_uncleared_cash_recieved_and_chqs_end');
		return $count_of_cr_and_chq;

	}	 
	 /*
	  * End of of finance model code
	  */

	  function get_fcp_primary_key($fcp_id){
		return $this->db->get_where('projectsdetails',array('icpNo'=>$fcp_id))->row()->ID;
	}  

	function get_max_dashboard_run_date(){

		$run_end_date = $this->config->item('dashboard_runs_start_date');;

		$this->db->select(array('closureDate','count(*) as count'));
		$this->db->group_by('closureDate');
		$this->db->order_by('closureDate DESC');
		$result = $this->db->get('opfundsbalheader');
			
		if($result->num_rows() > 0){
			$closure_date_array = $result->result_array();
			for($i = 0;$i < $result->num_rows();$i++){
				if($closure_date_array[$i]['count'] > $this->config->item('fcp_threshold_for_dashboard_run')){
					$run_end_date = $closure_date_array[$i]['closureDate'];
					break;
				}
			}
		}

		return $run_end_date;
	}


	function insert_parameters_dashboard_table($month = '',$grid_array){

		
		// Check if the month has a run and update the dashboard_run table
		$run_obj = $this->db->get_where('dashboard_run',array("month"=>$month));

		// Check if their is a change for the month - Not yet in use here
		//$month_change = $this->db->get_where('dashboard_change',array('status'=>1,'month'=>$month));


		$loops = 1;
		
		foreach($grid_array['fcps_with_risks'] as $fcp_id => $fcp_data){
		
			$new_record = true;
			$dashboard_header_id = 0;

			$projectsdetails_id = $this->get_fcp_primary_key($fcp_id);

			// Check if the FCP in the month is available before insert. If yes, do nothing

			$fcp_dashboard_header_obj = $this->db->get_where('dashboard_header',
			array('projectsdetails_id'=>$projectsdetails_id,'month'=>$month));
			
			
			if($fcp_dashboard_header_obj->num_rows() == 0){
				// Insert if the record doesn't exist and set the dashboard_header_id to the insert_id	
				$to_insert_data['projectsdetails_id'] = $projectsdetails_id;
				$to_insert_data['month'] = $month;

				$this->db->insert('dashboard_header',$to_insert_data);
				
				$dashboard_header_id = $this->db->insert_id();
			
			}else{
				// Just get the dashboard_header_id if the fcp record for the month exists and set the 
				// new_record variable to false
				$dashboard_header_id = $fcp_dashboard_header_obj->row()->dashboard_header_id;
				$new_record = false;
			}

			// Update dashboard change record
			$dashboard_change_obj = $this->db->get_where('dashboard_change',array('month'=>$month,'projectsdetails_id'=>$projectsdetails_id,'status'=>1));

			if($dashboard_change_obj->num_rows() > 0){
				$this->db->where(array('dashboard_change_id'=>$dashboard_change_obj->row()->dashboard_change_id));
				$this->db->update('dashboard_change',array('status'=>0));
			}
			

			$param_count = 0;
			$to_insert_param_data = [];
			
			foreach($fcp_data['params'] as $param_key=>$param_value){
				
				$to_update_param_data = [];

				// Populate the to_insert_param_data array if this is a new dashbaord header record 

				$to_insert_param_data[$param_count]['dashboard_header_id'] = $dashboard_header_id;
				$to_insert_param_data[$param_count]['dashboard_parameter_id'] = $param_key;
				$to_insert_param_data[$param_count]['dashboard_parameter_value'] = $param_value;

				if(!$new_record){
					// Check if the parameter is available and has same/ unchanged value
					$param_record_obj = $this->db->get_where('dashboard_body',array('dashboard_header_id'=>$dashboard_header_id,'dashboard_parameter_id'=>$param_key));
					
					if($param_record_obj->num_rows() > 0){

						$to_update_param_data['dashboard_header_id'] = $dashboard_header_id;
						$to_update_param_data['dashboard_parameter_id'] = $param_key;
						$to_update_param_data['dashboard_parameter_value'] = $param_value;

						$this->db->where(array('dashboard_body_id'=>$param_record_obj->row()->dashboard_body_id));
						$this->db->update('dashboard_body',$to_update_param_data);
						
					}
				}	
				
				$param_count++;
			}

			// Insert only if the to_insert_param_data array has items
			if(count($to_insert_param_data) > 0){
				$this->db->insert_batch('dashboard_body',$to_insert_param_data);
				
				// Update end of loop time
				$this->db->where(array('month'=>$month));
				$this->db->update('dashboard_run',array('run_end_date'=>date('Y-m-d h:i:s')));
			}

			$loops++;
			
		}

		
	}
	
	function display_dashboard($month){
		//$month = date('Y-m-t',1522447200);

		$parameters_array = $this -> switch_environment('', 'test_dashboard_parameters_model', 'prod_dashboard_parameters_model');
	
		$this->db->select(array('icpNo','month','risk','dashboard_body.dashboard_parameter_id as dashboard_parameter_id','is_requested','dashboard_parameter_name','dashboard_parameter_value'));
		$this->db->join('projectsdetails','projectsdetails.ID=dashboard_header.projectsdetails_id');
		$this->db->join('dashboard_body','dashboard_body.dashboard_header_id=dashboard_header.dashboard_header_id');
		$this->db->join('dashboard_parameter','dashboard_parameter.dashboard_parameter_id=dashboard_body.dashboard_parameter_id');
		$result  = $this->db->get_where("dashboard_header",array("month"=>$month,'projectsdetails.status'=>1))->result_array();

		$modified_output_array = [];

		foreach($result as $row){
			$modified_output_array['fcps_with_risks'][$row['icpNo']]['risk'] = $row['risk'];

			$modified_output_array['fcps_with_risks'][$row['icpNo']]['message_sent'] = $this->_check_if_message_been_sent($row['icpNo'],$month);
			
			$modified_output_array['fcps_with_risks'][$row['icpNo']]['params'][$row['dashboard_parameter_id']] = $row['dashboard_parameter_value'];

			// $modified_output_array['fcps_with_risks'][$row['icpNo']]['params'][$row['is_requested']][$row['dashboard_parameter_id']]['parameter_name'] = $row['dashboard_parameter_name'];

			// $modified_output_array['fcps_with_risks'][$row['icpNo']]['params'][$row['is_requested']][$row['dashboard_parameter_id']]['parameter_value'] = $row['dashboard_parameter_value'];
		
		}

		


		foreach ($parameters_array as $key => $value) {
			if ($value['display_on_dashboard'] == 'yes') {
				$modified_output_array['parameters'][$value['is_requested']][$key] = $value['dashboard_parameter_name'];
			}

		}

		return $modified_output_array;
	}

	private function _check_if_message_been_sent($fcp_id,$dashboard_month){
		$projectsdetails_id = $this->db->get_where('projectsdetails',array('icpNo'=>$fcp_id))->row()->ID;
		$count_of_chat = $this->db->get_where('dashboard_messaging',
		array('projectsdetails_id'=>$projectsdetails_id,'dashboard_month'=>$dashboard_month))->num_rows();
		
		if($count_of_chat > 0){
			return "Yes";
		}else{
			return "No";
		}
	}
}