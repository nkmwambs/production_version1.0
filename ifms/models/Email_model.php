<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Email_model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
	}

	function send_dashboard_notification($from_user_id, $to_user_id, $message)
	{

		$sender = $this->db->get_where('users', array("ID" => $from_user_id))->row();
		$recipient = $this->db->get_where('users', array("ID" => $to_user_id))->row();

		$email_msg		=	"Dear  " . $recipient->userfirstname . " " . $recipient->userlastname . ",<br />";
		$email_msg		.=	"Your have received a MFR review notification from the National Office accounts desk with the  following message: <br />";
		$email_msg		.=	 "</br><div class='well'>" . $message . '</div></br>';
		$email_msg		.=	"Kindly response within " . $this->config->item('mfr_review_notification_response_time') . " hours using my work email address " . $sender->email . "<br />";

		$email_sub		=	"National Office FCP MFR Review Notification";
		$email_to		=	$recipient->email;
		//$email_from     = 	$sender->email;

		$this->do_email($email_msg, $email_sub, $email_to);
	}

	function account_opening_email($account_type = '', $email = '')
	{
		$system_name	=	$this->db->get_where('settings', array('type' => 'system_name'))->row()->description;

		$email_msg		=	"Welcome to " . $system_name . "<br />";
		$email_msg		.=	"Your account type : " . $account_type . "<br />";
		$email_msg		.=	"Your login password : " . $this->db->get_where($account_type, array('email' => $email))->row()->password . "<br />";
		$email_msg		.=	"Login Here : " . base_url() . "<br />";

		$email_sub		=	"Account opening email";
		$email_to		=	$email;

		$this->do_email($email_msg, $email_sub, $email_to);
	}

	function submit_mfr_notification($user_id = "", $month)
	{

		$obj_sender = $this->db->get_where("users", array('ID' => $user_id))->result_object();

		$email = $this->db->get_where("users", array('userlevel' => '2', "cname" => $obj_sender->cname))->row()->email;

		$email_msg = $obj_sender->userfirstname . " " . $obj_sender->userlastname . " has submitted " . $obj_sender->fname . " " . date("M Y", strtotime($month)) . " ICP financial report. <br />";
		$email_msg .= "Kindly log in to the toolkit to review and validate this report";

		$email_sub		=	"ICP Financial report submission";
		$email_to		=	$email;

		$this->do_email($email_msg, $email_sub, $email_to);
	}

	function voucher_cancellation_notification($cancelled_voucher_number, $fcp_no)
	{
		// Get the FCP cluster of the FCP No 
		$this->db->where(array('icpNo' => $fcp_no));
		$this->db->join('clusters', 'clusters.clusters_id=projectsdetails.cluster_id');
		$cluster_name = $this->db->get('projectsdetails')->row()->clusterName;

		// Get the PF of the FCP cluster
		$this->db->select(array('email'));
		$this->db->where(array('userlevel' => '2', "cname" => $cluster_name));
		$email = $this->db->get("users")->row()->email;

		// Create a message and send the PF and notification
		$email_msg = $fcp_no . " has cancelled voucher number " . $cancelled_voucher_number . ". </br>";
		$email_msg .= "Kindly log in to the toolkit to review the cash journal for an query";

		$email_sub		=	"Voucher cancellation";
		$email_to		=	$email;

		$this->do_email($email_msg, $email_sub, $email_to);
	}

	function password_reset_email($new_password = '', $account_type = '', $email = '')
	{
		$query			=	$this->db->get_where($account_type, array('email' => $email));
		if ($query->num_rows() > 0) {

			$email_msg	=	"Your account type is : " . $account_type . "<br />";
			$email_msg	.=	"Your password is : " . $new_password . "<br />";

			$email_sub	=	"Password reset request";
			$email_to	=	$email;
			$this->do_email($email_msg, $email_sub, $email_to);
			return true;
		} else {
			return false;
		}
	}

	function voucher_submitted($voucher_hID = '', $email = '')
	{
		$query	=	$this->db->get_where('voucher_header', array('hID' => $voucher_hID));
		if ($query->num_rows() > 0) {

			$email_msg	=	"You have a voucher submitted by  " . $query->row()->icpNo . " with details as below:<br />";
			$email_msg	.=	"Voucher Number : " . $query->row()->VNumber . "<br />";
			$email_msg	.=	"Voucher Type : " . $query->row()->VType . "<br />";
			$email_msg	.=	"Voucher Payee : " . $query->row()->Payee . "<br />";
			$email_msg	.=	"Voucher Description : " . $query->row()->TDescription . "<br />";
			$email_msg	.=	"Voucher Amount : " . $query->row()->totals . "<br />";


			/**$body = $this->db->get_where('voucher_body')->result_object();
			
			
				$email_msg .= '<table class="table table-bordered table-striped">';
				$email_msg .= '<thead>';
				$email_msg .= '<tr><th colspan="5">'.$query->row()->icpNo.'<br />'.get_phrase('payment_voucher').'</th></tr>';
				$email_msg .= '</thead>';
				$email_msg .= '<tbody>';
				$email_msg .= '<tr>';
				$email_msg .= '<td colspan="2">'.get_phrase('date').': '.$query->row()->TDate.'</td><td colspan="">'.get_phrase('voucher_number').': '.$query->row()->VNumber.'</td>';
				$email_msg .= '</tr>';
				$email_msg .= '<tr>';
				$email_msg .= '<td colspan="5">'.get_phrase('vendor').': '.$query->row()->Payee.'</td>';
				$email_msg .= '</tr>';
				$email_msg .= '<tr>';
				$email_msg .= '<td colspan="2">'.get_phrase('address').': '.$query->row()->Address.'</td><td colspan="">'.get_phrase('voucher_type').': '.$query->row()->VType.'</td>';
				$email_msg .= '</tr>';
				$email_msg .= '<tr>';
				$email_msg .= '<td colspan=""5>'.get_phrase('description').': '.$query->row()->TDescription.'</td>';
				$email_msg .= '</tr>';
				$email_msg .= '<tr>';
				$email_msg .= '<td>'.get_phrase('quantity').'</td>';
				$email_msg .= '<td>'.get_phrase('items').'</td>';
				$email_msg .= '<td>'.get_phrase('unit_cost').'</td>';
				$email_msg .= '<td>'.get_phrase('cost').'</td>';
				$email_msg .= '<td>'.get_phrase('account').'</td>';
				$email_msg .= '</tr>';
						
				foreach($body as $row):
						
				$email_msg .= '<tr>';
				$email_msg .= '<td>'.$row->Qty.'</td>';
				$email_msg .= '<td>'.$row->Details.'</td>';
				$email_msg .= '<td>'.$row->UnitCost.'</td>';
				$email_msg .= '<td>'.$row->Cost.'</td>';
				$email_msg .= '<td>'.$this->db->get_where('accounts',array('AccNo'=>$row->AccNo))->row()->AccText.'</td>';
				$email_msg .= '</tr>';
			
				endforeach;	
						
				$email_msg .= '</tbody>';
				$email_msg .= '</table>';
			
			 **/


			$email_sub	=	"Notification for a Voucher Submitted";
			$email_to	=	$email;
			$this->do_email($email_msg, $email_sub, $email_to);
			return true;
		} else {
			return false;
		}
	}

	/***custom email sender****/
	function do_email($msg = NULL, $sub = NULL, $to = NULL, $from = NULL)
	{

		$config = array();
		$config['useragent']	= "CodeIgniter";
		$config['mailpath']		= "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
		$config['protocol']		= "smtp";
		$config['smtp_host']	= "localhost";
		$config['smtp_port']	= "25";
		$config['mailtype']		= 'html';
		$config['charset']		= 'utf-8';
		$config['newline']		= "\r\n";
		$config['wordwrap']		= TRUE;

		$this->load->library('email');

		$this->email->initialize($config);

		$system_name	=	$this->db->get_where('settings', array('type' => 'system_name'))->row()->description;
		if ($from == NULL)
			$from		=	$this->db->get_where('settings', array('type' => 'system_email'))->row()->description;

		$this->email->from($from, $system_name);
		//$this->email->from($from, $system_name);
		$this->email->to($to);
		$this->email->subject($sub);

		$msg	=	$msg . "<br /><br /><br /><br /><br /><br /><br /><hr /><center><a href=\"https://www.compassionkenya.com/\">&copy; 2017 Compassion Internation Inc</a></center>";
		$this->email->message($msg);

		$this->email->send();

		//echo $this->email->print_debugger();
	}
}
