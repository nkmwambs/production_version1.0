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

class Facilitator extends CI_Controller
{


	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->load->library('zip');
		//$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));

		/*cache control*/
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
		//$this->db->cache_delete_all();
		//$this->output->cache(60); 

	}

	/***default functin, redirects to login page if no admin logged in yet***/
	public function index()
	{
		if ($this->session->userdata('admin_login') != 1)
			redirect(base_url() . 'admin.php/login', 'refresh');
	}

	/***Load Default Pages***/
	function dashboard($param1 = "", $param2 = "", $param3 = "")
	{
		if ($this->session->userdata('admin_login') != 1)
			redirect(base_url(), 'refresh');

		$page_data['tym']  = strtotime(date('Y-m-d'));

		if ($param1 !== "" && $param2 === "") {

			$page_data['tym']  = $param1;
		}

		if ($param3 === "prev" || $param3 === "next") {
			$sign = '+';

			if ($param3 === 'prev') {
				$sign = '-';
			}

			$page_data['tym']  = strtotime($sign . $param2 . ' months', $param1);
		}

		$cluster_name = $this->session->cluster;

		$page_data['dashboard_financial_report'] = $this->dashboard_financial_report_check_grid(date('Y-m-t', $page_data['tym']), $cluster_name);
		$page_data['cluster_fcps'] = $this->cluster_fcps();
		$page_data['dashboard_budget'] = $this->dashboard_budget_check_grid($cluster_name, date('Y-m-t', $page_data['tym']));

		$page_data['page_name']  = 'dashboard';
		$page_data['page_title'] = get_phrase('finance_dashboard');
		$this->load->view('backend/index', $page_data);
	}

	function dashboard_financial_report_check_grid($month, $cluster_name)
	{

		$cluster_financial_report_data =  $this->finance_model->cluster_financial_report_data($month, $cluster_name);

		$financial_report = [];

		foreach ($cluster_financial_report_data as $month_report) {
			$fcp_number = array_shift($month_report);
			$financial_report[$fcp_number] = $month_report;
		}

		return $financial_report;
	}

	function dashboard_budget_check_grid($cluster_name, $month)
	{
		$budget_items = $this->finance_model->cluster_unapproved_budget_items($cluster_name, $month);

		$items = [];

		foreach ($budget_items as $budget_item) {
			$fcp_number = array_shift($budget_item);
			$items[$fcp_number][$budget_item['status_code']] = $budget_item['totalCost'];
		}

		return $items;
	}

	function cash_journal($timestamp = '', $fcp_number = '', $param3 = '')
	{
		if ($this->session->userdata('admin_login') != 1)
			redirect(base_url() . 'admin.php', 'refresh');


		$page_data['fcp_number'] = $fcp_number;
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

		$page_data['fcp_number'] = $project;
		$page_data['page_name']  = 'cash_journal';
		$page_data['page_title'] = get_phrase('cash_journal');
		$this->load->view('backend/index', $page_data);
	}

	public function delete_voucher($project = "", $voucher_id = "", $month = "")
	{
		//echo "Voucher ID ".$voucher_id." delete successful";

		$mfr_submitted = $this->finance_model->mfr_submitted($project, date('Y-m-t', $month), 1);

		$voucher_number  = $this->db->get_where('voucher_header', array('hID' => $voucher_id))->row()->VNumber;

		$this->db->where("hID >= ", $voucher_id);
		$this->db->where("TDate <= ", date('Y-m-t', $month));
		$this->db->where("icpNo", $project);

		$set_of_vouchers = $this->db->order_by('hID')->get('voucher_header')->result_object();

		$msg = "Could not delete the voucher number " . $voucher_number;

		if ($mfr_submitted === '0') {

			$this->db->trans_start();

			$this->db->delete('voucher_header', array('hID' => $voucher_id));

			$this->db->delete('voucher_body', array('hID' => $voucher_id));

			$deleted_voucher = array_shift($set_of_vouchers);

			if (count($set_of_vouchers) > 0) {

				foreach ($set_of_vouchers as $rename) {

					$current_voucher_pointer = $this->db->get_where('voucher_header', array('hID' => $rename->hID))->row()->VNumber;

					$voucher_frame = substr($current_voucher_pointer, 0, 4); // Year and Month of the voucher number

					$voucher_serial = substr($current_voucher_pointer, 4) - 1; // Resetting the voucher serials with 1 digit backward

					$new_voucher_pointer =  $voucher_serial > 9 ? $voucher_frame . $voucher_serial . " " : $voucher_frame . '0' . $voucher_serial . " ";

					$data['VNumber'] = $new_voucher_pointer;

					$this->db->update('voucher_header', $data, array('hID' => $rename->hID));

					$this->db->update('voucher_body', $data, array('hID' => $rename->hID));
				}
			}

			$this->remove_fund_balance_records_if_month_has_no_voucher($project, $month);

			$this->db->trans_commit();

			if ($this->db->trans_status() == true) {
				$msg = "Voucher " . $deleted_voucher->VNumber . " deleted. " . count($set_of_vouchers) . " vouchers re-numbered";
			}
		}

		echo $msg;
	}

	function remove_duplicate($project = "", $voucher_id = "", $month = "")
	{
		$mfr_submitted = $this->finance_model->mfr_submitted($project, date('Y-m-t', $month), 1);

		$voucher_number  = $this->db->get_where('voucher_header', array('hID' => $voucher_id))->row()->VNumber;

		//Check duplicates

		$duplicates = $this->db->get_where('voucher_header', array('VNumber' => $voucher_number, 'icpNo' => $project))->num_row();

		$msg = "Could not delete the voucher number " . $voucher_number;


		if ($mfr_submitted === '0' && $duplicates > 1) {
			$this->db->trans_start();
			$this->db->delete('voucher_header', array('hID' => $voucher_id));

			$this->db->delete('voucher_body', array('hID' => $voucher_id));

			$this->remove_fund_balance_records_if_month_has_no_voucher($project, $month);

			$this->db->trans_commit();

			if ($this->db->trans_status() == true) {
				$msg = "Voucher " . $voucher_number . " deleted";
			}
		}

		echo $msg;
	}

	public function delete_all_vouchers($project = "", $voucher_id = "", $month = "")
	{

		$mfr_submitted = $this->finance_model->mfr_submitted($project, date('Y-m-t', $month), 1);

		$voucher_number  = $this->db->get_where('voucher_header', array('hID' => $voucher_id))->row()->VNumber;

		$msg = "Could not delete the voucher number " . $voucher_number;

		if ($mfr_submitted === '0') {

			$this->db->trans_start();

			$num_of_vouchers = $this->db->get_where('voucher_header', array('hID>=' => $voucher_id, 'icpNo' => $project))->num_rows();

			$this->db->delete('voucher_header', array('hID>=' => $voucher_id, 'icpNo' => $project));

			$this->db->delete('voucher_body', array('hID>=' => $voucher_id, 'icpNo' => $project));

			$this->remove_fund_balance_records_if_month_has_no_voucher($project, $month);

			$this->db->trans_commit();

			if ($this->db->trans_status() == true) {
				$msg = $num_of_vouchers . " vouchers deleted successful";
			}
		}

		echo $msg;
	}

	private function remove_fund_balance_records_if_month_has_no_voucher($fcp_number, $month_timestamp)
	{
		$month_start_date = date('Y-m-01', $month_timestamp);
		$month_end_date = date('Y-m-t', $month_timestamp);

		$count_vouchers_in_month = $this->db->get_where(
			'voucher_header',
			array('icpNo' => $fcp_number, 'TDate<=' => $month_end_date, 'TDate>=' => $month_start_date)
		)->num_rows();

		if ($count_vouchers_in_month == 0) {


			$condition = array('icpNo' => $fcp_number, 'closureDate' => $month_end_date);

			$balHdID = $this->db->select(array('balHdID'))->get_where('opfundsbalheader', $condition)->row()->balHdID;

			// cashbal, statementbal, opfundsbal to be deleted
			$this->db->where(array('balHdID' => $balHdID));
			$this->db->delete('opfundsbal');

			$this->db->where(array('month' => $month_end_date, 'icpNo' => $fcp_number));
			$this->db->delete('statementbal');

			$this->db->where(array('month' => $month_end_date, 'icpNo' => $fcp_number));
			$this->db->delete('cashbal');

			$this->db->where($condition);
			$this->db->delete('opfundsbalheader');
		}
	}

	public function validate_mfr()
	{
		$post = $this->input->post();

		//extract($post);

		$data['allowEdit'] = "1";
		$data['mfr_validation_date'] = '0000-00-00';
		$new_status_code  = false;

		if (!$post['validated']) {
			$data['allowEdit'] = "0";
			$data['mfr_validation_date'] = date('Y-m-d h:i:s');
			$new_status_code = true;
		}

		$this->db->where(array('icpNo' => $post['fcp'], 'closureDate' => $post['month']));
		$this->db->update("opfundsbalheader", $data);

		// Register dashboard change after a run
		$this->finance_model->register_dashboard_change($post['fcp'], strtotime($post['month']));

		echo $new_status_code;
	}

	function decline_mfr($month = "", $project = "")
	{

		$max_mfr = $this->db->select_max('closureDate')->get_where("opfundsbalheader", array("icpNo" => $project))->row()->closureDate;

		//$message = get_phrase("report_declined_successful");
		$msg = "Error Occurred!";

		//Check if there is a next month MFR present

		$next_mfr = $this->db->get_where("opfundsbalheader", array("icpNo" => $project, "closureDate" => date('Y-m-t', strtotime('last day of next month', $month))))->num_rows();

		$current_mfr = $this->db->get_where("opfundsbalheader", array("icpNo" => $project, "closureDate" => date('Y-m-t', $month)))->num_rows();

		if ($next_mfr !== 0) {
			$msg = get_phrase("decline_failure._first_decline_the_") . $max_mfr . " " . get_phrase("report");
		} elseif ($current_mfr === 0) {
			$msg = get_phrase("current_month_report_not_submitted");
		} else {

			//Delete Cash Balances
			$this->db->delete('cashbal', array('month' => date('Y-m-t', $month), 'icpNo' => $project));

			//Delete Statement Balance
			//$this->db->delete('statementbal',array('month'=>date('Y-m-t',$month),'icpNo'=>$project));	

			//Unlink Bank Statements

			//$this->delete_bank_statement($month, $project);

			//Delete Fund Balances

			$balHdID = $this->db->get_where('opfundsbalheader', array('icpNo' => $project, 'closureDate' => date('Y-m-t', $month)))->row()->balHdID;

			$this->db->delete('opfundsbalheader', array('balHdID' => $balHdID));

			$this->db->delete('opfundsbal', array('balHdID' => $balHdID));


			$msg = date('M Y', $month) . " financial report deleted successfully";
		}
		echo $msg;
	}

	public function delete_bank_statement($param1, $param2)
	{
		//$t= $_POST['name'];
		$storeFolder = 'uploads/bank_statements/' . $param2 . '/' . date('Y-m', $param1) . '/';
		//unlink($storeFolder);
		foreach (glob($storeFolder . "/*.*") as $filename) {
			if (is_file($filename)) {
				unlink($filename);
			}
		}
	}
	function bank_statements($param1 = "", $param2 = "", $param3 = "")
	{
		if ($this->session->userdata('admin_login') != 1)
			redirect(base_url(), 'refresh');


		$page_data['uploaded_bank_statement'] = $this->finance_model->get_uploaded_bank_statement(date('Y-m-t', $param1), $param2);
		$page_data['page_name']  = 'bank_statements_upload';
		$page_data['tym'] = $param1;
		$page_data['project'] = $param2;;
		$page_data['page_title'] = get_phrase('bank_statements');
		$this->load->view('backend/index', $page_data);
	}

	public function bank_statement_download($param1, $param2, $param3)
	{
		force_download('uploads/bank_statements/' . $param3 . '/' . date('Y-m', $param2) . '/' . $param1, NULL);
	}

	function ziparchive($param1 = "", $param2 = "")
	{

		if (file_exists('uploads/bank_statements/' . $param1 . '/' . date('Y-m', $param2) . '/')) {
			$map = directory_map('uploads/bank_statements/' . $param1 . '/' . date('Y-m', $param2) . '/', FALSE, TRUE);

			foreach ($map as $row) :

				$path = 'uploads/bank_statements/' . $param1 . '/' . date('Y-m', $param2) . '/' . $row;

				$data = file_get_contents($path);

				$this->zip->add_data($row, $data);
			endforeach;


			// Write the zip file to a folder on your server. Name it "my_backup.zip"
			$this->zip->archive('downloads/my_backup_' . $this->session->login_user_id . '.zip');

			// Download the file to your desktop. Name it "my_backup.zip"

			$backup_file = 'downloads_' . date("Y_m_d_H_i_s") . '.zip';

			$this->zip->download($backup_file);

			unlink('downloads/' . $backup_file);
		}
	}

	function plans($param1 = '', $param2 = '', $param3 = '', $param4 = '')
	{
		if ($this->session->userdata('admin_login') != 1)
			redirect(base_url(), 'refresh');

		if ($param3 === 'approve') {

			$data['approved'] = '2';

			$this->db->where(array('scheduleID' => $param4))->update('plansschedule', $data);

			if ($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('flash_message', get_phrase('item_approval_successful'));
			} else {
				$this->session->set_flashdata('flash_message', get_phrase('approval_failure'));
			}

			redirect(base_url() . 'ifms.php/facilitator/plans/' . $param1 . '/' . $param2, 'refresh');
		}


		$page_data['fyr'] = get_fy(date('Y-m-d', $param1), $param2);
		$page_data['tym'] = $param1;
		$page_data['project'] = $param2;
		$page_data['page_name']  = 'plans';
		$page_data['page_title'] = get_phrase('project_budget');
		$this->load->view('backend/index', $page_data);
	}

	function action_item($param1 = '')
	{

		$param_arr = explode("-", $param1);

		$action = $param_arr['0'];

		$item = $param_arr['1'];

		$msg = "";

		$content = array();

		$approval = array('New', 'Submitted', 'Approved', 'Declined/allow review', 'Reinstated', 'Unacceptable');

		$data = array();

		if ($action === 'app') {
			$data['approved'] = '2';

			$this->db->where(array('scheduleID' => $item))->update('plansschedule', $data);

			$msg = get_phrase('approval_failure');

			if ($this->db->affected_rows() > 0) {
				$msg = get_phrase('item_approval_successful');
			}

			$content['msg'] = $msg;
			$data['btn'] = 'btn-success';
			$content['status'] = $approval['2'];
		}

		if ($action === 'dec') {

			$data['approved'] = '3';

			$this->db->where(array('scheduleID' => $item))->update('plansschedule', $data);

			$msg = get_phrase('decline_failure');

			if ($this->db->affected_rows() > 0) {
				$msg = get_phrase('item_declined_successful');
			}

			$content['msg'] = $msg;
			$data['btn'] = 'btn-danger';
			$content['status'] = $approval['3'];
		}

		if ($action === 'unc') {
			$data['approved'] = '5';

			$this->db->where(array('scheduleID' => $item, 'approved<>' => '2'))->update('plansschedule', $data);

			//btn-default

			$msg = get_phrase('unacceptable_flag_failure');

			if ($this->db->affected_rows() > 0) {
				$msg = get_phrase('unacceptable_item');
			}

			$content['msg'] = $msg;
			$data['btn'] = 'btn-default';
			$content['status'] = $approval['5'];
		}


		$data['scheduleID'] = $item;
		$content['action'] = $action;
		$content['view'] = $this->load->view('backend/facilitator/load_plans_buttons', $data, true);

		echo json_encode($content);
	}

	function action_comment($param1 = "")
	{

		$content = array();
		//Add Comment 

		$data2['recid'] = $param1;
		$data2['userid'] = $this->session->login_user_id;
		$data2['rson'] = $this->input->post('rson');
		$data2['app_name'] = $this->session->app_name;

		$this->db->insert('detail', $data2);

		$msg = get_phrase('item_commented_successful');

		$data['scheduleID'] = $param1;

		$content['msg'] = $msg;
		$content['view'] = $this->load->view('backend/facilitator/load_body_comments', $data, true);

		echo json_encode($content);
	}

	function budget_comments()
	{
		$arr = explode("-", $_POST['id']);
		$action = $arr[0];
		$id = $arr[1];


		$data['scheduleID'] = $id;
		echo $this->load->view('backend/facilitator/load_comments', $data, true);
	}
	function budget_notes()
	{
		$arr = explode("-", $_POST['id']);
		$action = $arr[0];
		$id = $arr[1];


		$data['scheduleID'] = $id;
		echo $this->load->view('backend/facilitator/load_budget_notes', $data, true);
	}
	function decline_budget_item($param1 = "", $param2 = "", $param3 = "")
	{

		if ($this->db->get_where('plansschedule', array('scheduleID' => $param3))->num_rows() > 0) {
			$data['approved'] = '3';

			$this->db->where(array('scheduleID' => $param3))->update('plansschedule', $data);


			//Add Comment 

			$data2['recid'] = $param3;
			$data2['userid'] = $this->session->login_user_id;
			$data2['rson'] = $this->input->post('rson');
			$data2['app_name'] = $this->session->app_name;

			$this->db->insert('detail', $data2);

			$this->session->set_flashdata('flash_message', get_phrase('item_declined_successful'));
		} else {
			$this->session->set_flashdata('flash_message', get_phrase('decline_failure'));
		}

		redirect(base_url() . 'ifms.php/facilitator/plans/' . $param1 . '/' . $param2, 'refresh');
	}

	function modal_unacceptable_budget_item($param1 = "", $param2 = "", $param3 = "")
	{

		if ($this->db->get_where('plansschedule', array('scheduleID' => $param3, 'approved<>' => '2'))->num_rows() > 0) {
			$data['approved'] = '5';

			$this->db->where(array('scheduleID' => $param3, 'approved<>' => '2'))->update('plansschedule', $data);

			$data2['recid'] = $param3;
			$data2['userid'] = $this->session->login_user_id;
			$data2['rson'] = $this->input->post('rson');
			$data2['app_name'] = $this->session->app_name;

			$this->db->insert('detail', $data2);

			$this->session->set_flashdata('flash_message', get_phrase('unacceptable_item'));
		} else {
			$this->session->set_flashdata('flash_message', get_phrase('unacceptable_flag_failure'));
		}

		redirect(base_url() . 'ifms.php/facilitator/plans/' . $param1 . '/' . $param2, 'refresh');
	}

	function add_budget_item_comments($param1 = "", $param2 = "", $param3 = "")
	{
		$data2['recid'] = $param3;
		$data2['userid'] = $this->session->login_user_id;
		$data2['rson'] = $this->input->post('rson');
		$data2['app_name'] = $this->session->app_name;

		$this->db->insert('detail', $data2);

		$this->session->set_flashdata('flash_message', get_phrase('comment_added_successful'));

		redirect(base_url() . 'ifms.php/facilitator/plans/' . $param1 . '/' . $param2, 'refresh');
	}

	public function multiple_vouchers($tym, $project)
	{
		if ($this->session->userdata('admin_login') != 1)
			redirect(base_url(), 'refresh');

		$post = $this->input->post();

		$page_data['vouchers'] = $post;

		$page_data['page_name']  = 'multiple_vouchers';
		$page_data['project'] = $project;
		$page_data['tym'] = $tym;
		$page_data['page_title'] = get_phrase('multiple_vouchers');
		$this->load->view('backend/index', $page_data);
	}
	function civ_report($param1 = "")
	{
		if ($this->session->userdata('admin_login') != 1)
			redirect(base_url(), 'refresh');

		$page_data['page_name']  = 'civ_report';

		if ($param1 === "closed") {
			$page_data['page_name']  = 'closed_civs_report';
		}
		//$page_data['page_name']  = 'civ_report';
		$page_data['page_title'] = get_phrase('interventions_report');
		$this->load->view('backend/index', $page_data);
	}

	function cluster_fcps()
	{
		$cluster = $this->session->cluster;

		$this->db->select(array('icpNo'));
		$this->db->where(array('clusterName' => $cluster, 'projectsdetails.status' => 1));
		$this->db->join('clusters', 'clusters.clusters_id=projectsdetails.cluster_id');
		$result = $this->db->get('projectsdetails')->result_array();

		return array_column($result, 'icpNo');
	}

	function direct_cash_transfers_report($tym)
	{
		if ($this->session->userdata('admin_login') != 1)
			redirect(base_url(), 'refresh');

		$page_data['tym']  = $tym;
		//$page_data['fcps']  = $this->direct_cash_transfers($this->cluster_fcps(),$tym,'fcp_number');
		$page_data['direct_cash_transfers'] = $this->fcp_grouped_direct_cash_transfers($this->cluster_fcps(), $tym);
		$page_data['page_name']  = 'direct_cash_transfers_report';
		$page_data['page_title'] = get_phrase('direct_cash_transfers_report_for') . ' ' . date('F Y', $tym);
		$this->load->view('backend/index', $page_data);
	}

	function direct_cash_transfer_vouchers($tym, $fcp)
	{
		if ($this->session->userdata('admin_login') != 1)
			redirect(base_url(), 'refresh');

		$page_data['fcp'] = $fcp;
		$page_data['tym'] = $tym;
		$page_data['direct_cash_transfer_vouchers'] = $this->direct_cash_transfers_by_fcp($fcp, $tym);
		$page_data['page_name']  = 'direct_cash_transfer_vouchers';
		$page_data['page_title'] = date('F Y', $tym) . ' ' . get_phrase('direct_cash_transfer_vouchers_for') . ' ' . $fcp;
		$this->load->view('backend/index', $page_data);
	}

	private function direct_cash_transfers(array $list_of_fcps, $reporting_month_stamp, $aggregate_by = 'account_number')
	{

		//aggregate_by = account_number or fcp_number

		$month_start_date = date('Y-m-01', $reporting_month_stamp);
		$month_end_date = date('Y-m-t', $reporting_month_stamp);


		if ($aggregate_by == 'fcp_number') {
			$this->db->select(array('voucher_body.icpNo as icpNo', 'AccText'));
			$this->db->group_by(array('voucher_body.AccNo', 'voucher_body.icpNo'));
		} else {
			$this->db->select(array('AccText'));
			$this->db->group_by(array('voucher_body.AccNo'));
		}

		$this->db->select_sum('Cost');

		$this->db->join('voucher_header', 'voucher_header.hID=voucher_body.hID');
		$this->db->join('accounts', 'accounts.AccNo=voucher_body.AccNo');
		$this->db->where_in('voucher_header.VType', array('UDCTB', 'UDCTC'));
		$this->db->where_in('voucher_header.icpNo', $list_of_fcps);
		$this->db->where(array('voucher_header.TDate>=' => $month_start_date));
		$this->db->where(array('voucher_header.TDate<=' => $month_end_date));
		$result = $this->db->get('voucher_body')->result_array();

		return $result;
	}

	/**
	 * direct_cash_transfers_by_fcp
	 */

	private function direct_cash_transfers_by_fcp($fcp_number, $reporting_month_stamp)
	{

		$month_start_date = date('Y-m-01', $reporting_month_stamp);
		$month_end_date = date('Y-m-t', $reporting_month_stamp);

		$this->db->select(array('voucher_header.hID as hID', 'voucher_header.VNumber as VNumber', 'voucher_header.VType as VType'));
		$this->db->group_by('voucher_body.VNumber', 'voucher_body.icpNo');

		$this->db->select_sum('Cost');

		$this->db->join('voucher_header', 'voucher_header.hID=voucher_body.hID');
		$this->db->join('accounts', 'accounts.AccNo=voucher_body.AccNo');
		$this->db->where_in('voucher_header.VType', array('UDCTB', 'UDCTC'));
		$this->db->where('voucher_header.icpNo', $fcp_number);
		$this->db->where(array('voucher_header.TDate>=' => $month_start_date));
		$this->db->where(array('voucher_header.TDate<=' => $month_end_date));
		$result = $this->db->get('voucher_body')->result_array();

		return $result;
	}

	private function fcp_grouped_direct_cash_transfers(array $list_of_fcps, $reporting_month_stamp)
	{
		$raw_data = $this->direct_cash_transfers($list_of_fcps, $reporting_month_stamp, 'fcp_number');
		// print_r($raw_data);
		// exit;
		$dct_records = [];

		$dct_accounts = [];

		$cnt = 0;
		foreach ($raw_data as $account_expense_for_fcp) {
			$fcp_no = $account_expense_for_fcp['icpNo'];

			unset($account_expense_for_fcp['icpNo']);

			$dct_records[$fcp_no]['spread'][$account_expense_for_fcp['AccText']] = $account_expense_for_fcp['Cost'];

			$dct_records[$fcp_no]['total_dct_expense'] = array_sum($dct_records[$fcp_no]['spread']);
		}

		foreach ($dct_records as $spread) {
			$dct_accounts = array_merge($dct_accounts, array_keys($spread['spread']));
		}

		return ['dct_records' => $dct_records, 'dct_accounts' => array_unique($dct_accounts)];
	}

	// public function dct_documents_download($fcp_number,$tym,$vnumber){
	// 	force_download('uploads/dct_documents/'.$fcp_number.'/'.date('Y-m',$tym).'/'.$vnumber,NULL);
	// }

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

	function list_funds_transfer_requests()
	{
		if ($this->session->userdata('admin_login') != 1)
			redirect(base_url(), 'refresh');


		$page_data['transfer_requests'] = $this->finance_model->get_funds_transfer_requests($this->cluster_fcps());
		$page_data['page_name']  = 'funds_transfer_requests';
		$page_data['page_title'] = get_phrase('funds_transfer_requests');
		$this->load->view('backend/index', $page_data);
	}

	function decline_request($request_id)
	{
		$this->db->where(array('reqID' => $request_id));
		$this->db->update('fundstransfersrequests', ['accepted' => 0]);

		$message = "Decline failed";

		if ($this->db->affected_rows() > 0) {
			$message = "Decline successfully";
		}

		echo $message;
	}

	function view_funds_transfer_request($fcp_id, $request_id)
	{
		if ($this->session->userdata('admin_login') != 1)
			redirect(base_url(), 'refresh');


		$page_data['transfer_request'] = $this->finance_model->get_funds_transfer_requests([$fcp_id], $request_id);
		$page_data['page_name']  = 'view_funds_transfer_request';
		$page_data['page_title'] = get_phrase('funds_transfer_request');
		$this->load->view('backend/index', $page_data);
	}
}
