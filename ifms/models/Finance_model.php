<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Finance_model extends CI_Model {
	
    
    function __construct() {
        parent::__construct();
    }
    
    function months_opening_fund_balances_for_centers($month){
		$this->db->select(array('icpNo','funds','amount'));
		$this->db->join('opfundsbalheader','opfundsbalheader.balHdID=opfundsbal.balHdID');
		$db_result = $this->db->get_where('opfundsbal',
		array('closureDate'=>date('Y-m-t',strtotime('last day of previous month',strtotime($month))),'submitted'=>1))->result_array();

		$return_array = [];

		foreach($db_result as $fund_account_balance){
			$return_array[$fund_account_balance['icpNo']][$fund_account_balance['funds']] = $fund_account_balance['amount'];
		}

		return $return_array;
	}

	function get_civa_record_by_civa_account_number(string $civa_account_number, int $expense_or_income=0)
	{
		$this->db->join('accounts', 'accounts.accID=civa.accID');
		$this->db->where(array('AccGrp' => $expense_or_income));
		$civa = $this->db->get_where('civa', array('AccNoCIVA' => $civa_account_number))->row();

		return $civa;
	}

	function get_voucher_types(){
		$this->db->select(array('voucher_type_id','voucher_type_name','voucher_type_abbrev'));
		return $this->db->get_where('voucher_type',array('voucher_type_is_active'=>1))->result_array();
	}

	function months_income_per_revenue_account_for_centers($month){

		$first_month_day = date('Y-m-01',strtotime($month));
		$last_month_day = date('Y-m-t',strtotime($month));

		$this->db->select(array('voucher_header.icpNo as icpNo','AccNo'));
		$this->db->select_sum('Cost');
		$this->db->group_by('icpNo, AccNo');
		$this->db->join('voucher_header','voucher_header.hID=voucher_body.hID');
		$db_result = $this->db->get_where('voucher_body',
		array('voucher_header.TDate>='=>$first_month_day,'voucher_header.TDate<='=>$last_month_day,'voucher_header.VType'=>'CR'))->result_array();

		$return_array = [];

		foreach($db_result as $account_income){
			$return_array[$account_income['icpNo']][$account_income['AccNo']] = $account_income['Cost'];
		}

		return $return_array;
	}

	function months_expense_per_revenue_account_for_centers($month){
		$first_month_day = date('Y-m-01',strtotime($month));
		$last_month_day = date('Y-m-t',strtotime($month));

		$this->db->select(array('voucher_header.icpNo as icpNo','parentAccID'));
		$this->db->select_sum('Cost');
		$this->db->group_by('icpNo, parentAccID');
		$this->db->join('voucher_header','voucher_header.hID=voucher_body.hID');
		$this->db->join('accounts','accounts.AccNo=voucher_body.AccNo');
		$db_result = $this->db->get_where('voucher_body',
		array('voucher_header.TDate>='=>$first_month_day,'voucher_header.TDate<='=>$last_month_day,'AccGrp'=>0))->result_array();

		$order_accounts = $this->ordered_revenue_accounts();

		$return_array = [];

		foreach($db_result as $account_expense){
			if(isset($order_accounts[$account_expense['parentAccID']])){
				$return_array[$account_expense['icpNo']][$order_accounts[$account_expense['parentAccID']]] = $account_expense['Cost'];
			}
		}

		return $return_array;
	}

	function ordered_revenue_accounts(){
		$this->db->select(array('accID','AccNo'));
		$revenue_accounts = $this->db->get_where('accounts',array('AccGrp'=>1))->result_array();

		$order_accounts = [];

		foreach($revenue_accounts as $revenue_account){
			$order_accounts[$revenue_account['accID']] = $revenue_account['AccNo'];
		}

		return $order_accounts;
	}

    function clear_cache() {
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
    }

	function system_start_date($param1=""){
		
		if($param1!==""){
			return $this->db->get_where('projectsdetails',array('icpNo'=>$param1))->row()->system_start_date;
		}else{
			return $this->db->get_where('projectsdetails',array('icpNo'=>$this->session->center_id))->row()->system_start_date;
		}
 		
 	}

	function project_system_start_date($param1=""){
		$start_date = "";
		if($this->db->get_where('opfundsbalheader',array('icpNo'=>$param1,"systemOpening"=>'1'))->num_rows()>0){
			$start_date = date("Y-m-01",strtotime('first day of next month',strtotime($this->db->get_where('opfundsbalheader',array('icpNo'=>$param1,"systemOpening"=>'1'))->row()->closureDate)));
		} 
 		return $start_date;//date("Y-m-01",strtotime('first day of next month',strtotime($this->db->get_where('opfundsbalheader',array('icpNo'=>$param1,"systemOpening"=>'1'))->row()->closureDate)));
 	}
	
	function project_fy_start_date($param1=""){
 		$start_date = date("Y-m-01",strtotime('first day of next month',strtotime($this->db->get_where('opfundsbalheader',array('icpNo'=>$param1,"systemOpening"=>'1'))->row()->closureDate)));
		
		$start_year = date('Y',strtotime($start_date));
		
		$fy_start_month =  $this->db->get_where("settings",array('type'=>'fy_start_month'))->row()->description;
		
		if(strlen($fy_start_month)===1){
			$fy_start_month = "0".$fy_start_month;
		}
		
		$fy = get_fy($start_date);
		
		if(date("y",strtotime($start_date))>= $fy){
			$start_year = $start_year-1;
			$start_date = $start_year."-".$fy_start_month."-01";
		}else{
			$start_date = $start_year."-".$fy_start_month."-01";
		}	 	
		
		
		$date = new DateTime($start_date);
		
		return $date->format('Y-m-t');;
		
	}
	
	private function generate_voucher_number($date,$next_serial){
		
			$yr = date('y',strtotime($date));
				
			$month = date('n',strtotime($date));
					
			if($month===12){
				$month = 1;
				$yr = $yr+1;
			}
				
			if($month<10){
				$month ='0'.$month;
			}
			
			if($next_serial<10){
				$next_serial = '0'.$next_serial;
			}
	 		
	 		return $yr.$month.$next_serial;		
	}
	
	function next_voucher($project_id){
			
			$voucher_count = $this->db->get_where('voucher_header',array('icpNo'=>$project_id))->num_rows();	
				
			if($voucher_count>0){
				$last_mfr_date = $this->db->select_max('closureDate')->get_where('opfundsbalheader',array('icpNo'=>$project_id))->row()->closureDate;
	 			
				$max_voucher_id = $this->db->select_max('hID')->get_where('voucher_header',array('icpNo'=>$project_id))->row()->hID;
				
				$voucher_date = $this->db->get_where('voucher_header',array('icpNo'=>$project_id,"hID"=>$max_voucher_id))->row()->TDate;
				
				$current_voucher_date = $voucher_date;
				
				$start_month_date = date("Y-m-01",strtotime($voucher_date));
				
				$end_month_date = date("Y-m-t",strtotime($voucher_date));
					
				$current_voucher = $this->db->get_where('voucher_header',array('icpNo'=>$project_id,"hID"=>$max_voucher_id))->row()->VNumber;
				
				if(strtotime($last_mfr_date)< strtotime($start_month_date)){
				
					$vnum = $this->generate_voucher_number($start_month_date,substr($current_voucher,4)+1);
				
				}elseif(strtotime($last_mfr_date) >= strtotime($start_month_date)){
					
					$current_voucher_date = date('Y-m-01',strtotime('first day of next month',strtotime($last_mfr_date)));
						
					$start_month_date = date("Y-m-d",strtotime('first day of next month',strtotime($voucher_date)));
					
					$end_month_date = date("Y-m-t",strtotime('last day of next month',strtotime($voucher_date)));	
					
					$vnum = $this->generate_voucher_number($start_month_date,1);
								
				}
			
			}else{
				
				$current_voucher_date = date("Y-m-01",strtotime('first day of the next month',strtotime($this->project_system_start_date($project_id))));
				
				$start_month_date = $this->project_system_start_date($project_id);
					
				$end_month_date = date("Y-m-t",strtotime($this->project_system_start_date($project_id)));
				
				$current_voucher_date = $start_month_date;
					
				$vnum = $this->generate_voucher_number($start_month_date,1);
			}
			
	 		
		
			$voucher_details['vnum'] = $vnum;		
			$voucher_details['current_voucher_date'] = $current_voucher_date;	
			$voucher_details['start_month_date'] = $start_month_date;
			$voucher_details['end_month_date'] = $end_month_date;	 	
	 		
	 		return (object)$voucher_details;
	 	
	 }

	function check_opening_balances($param1=""){
		//Fund balances, cash balances = 3
		
		$opening_balance = FALSE;
		
		$mfr_count = $this->db->get_where('opfundsbalheader',array('icpNo'=>$param1))->num_rows();
		
		$cash_balance = $this->db->get_where('cashbal',array('icpNo'=>$param1,'accNo'=>"PC"))->num_rows();
		
		$bank_balance = $this->db->get_where('cashbal',array('icpNo'=>$param1,'accNo'=>"BC"))->num_rows();
		
		if($mfr_count>0&&$cash_balance>0&&$bank_balance>0){
			$opening_balance = TRUE;
		}
		
		return $opening_balance;
	}

	

	
	function opening_bank_balance($date,$project){
		

		$bank_balance = 0;
		
		if($this->db->get_where('cashbal',array('icpNo'=>$project))->num_rows()>0){
			
			if(date('n',strtotime($date))==='1'){
				$year = date('Y',strtotime($date)) - 1; 
				$end = mktime(0, 0, 0, 12, 31, $year);
				$bank_balance = 0;	
				
				if($this->db->get_where('cashbal',array('icpNo'=>$project,"accNo"=>"BC","month"=>date("Y-m-d",$end)))->num_rows()>0){
					$bank_balance = $this->db->get_where('cashbal',array('icpNo'=>$project,"accNo"=>"BC","month"=>date("Y-m-d",$end)))->row()->amount;
				}
				
			}
			elseif($this->db->get_where('cashbal',array('icpNo'=>$project,"accNo"=>"BC","month"=>date("Y-m-d",strtotime("last day of previous month",strtotime($date)))))->num_rows()>0){
				$bank_balance = $this->db->get_where('cashbal',array('icpNo'=>$project,"accNo"=>"BC","month"=>date("Y-m-d",strtotime("last day of previous month",strtotime($date)))))->row()->amount;	
			}
			
		}
		
		
		return $bank_balance;
	}
	
	function test_params($date,$project){
		//$year = date('Y',strtotime($date)) - 1;
		//$end = mktime(0, 0, 0, 12, 31, $year);
		
		//$year = date('Y',strtotime($date)) + 1; 
		//$start = mktime(0, 0, 0, 1, 1, $year);
		
				
		return date('n',strtotime($date));
	}
	

	function opening_pc_balance($date,$project){
						
		$pc_balance = 0;
		
		if($this->db->get_where('cashbal',array('icpNo'=>$project))->num_rows()>0){
			
			if(date('n',strtotime($date))==='1'){
				$year = date('Y',strtotime($date)) - 1; 
				$end = mktime(0, 0, 0, 12, 31, $year);
				$pc_balance = 0;	
				
				if($this->db->get_where('cashbal',array('icpNo'=>$project,"accNo"=>"PC","month"=>date("Y-m-d",$end)))->num_rows()>0){
					$pc_balance = $this->db->get_where('cashbal',array('icpNo'=>$project,"accNo"=>"PC","month"=>date("Y-m-d",$end)))->row()->amount;
				}
				
			}//elseif(date('n',strtotime($date))==='12'){
				//$year = date('Y',strtotime($date)) + 1; 
				//$start = mktime(0, 0, 0, 1, 1, $year);
				
				//$pc_balance = 0;
				
				//if($this->db->get_where('cashbal',array('icpNo'=>$project,"accNo"=>"PC","month"=>date("Y-m-d",$start)))->num_rows()>0){
					//$pc_balance = $this->db->get_where('cashbal',array('icpNo'=>$project,"accNo"=>"PC","month"=>date("Y-m-d",$start)))->row()->amount;
				//}		
				
			//}
			elseif($this->db->get_where('cashbal',array('icpNo'=>$project,"accNo"=>"PC","month"=>date("Y-m-d",strtotime("last day of previous month",strtotime($date)))))->num_rows()>0){
				$pc_balance = $this->db->get_where('cashbal',array('icpNo'=>$project,"accNo"=>"PC","month"=>date("Y-m-d",strtotime("last day of previous month",strtotime($date)))))->row()->amount;	
			}
			
		}
		
		
		return $pc_balance;
				
				
	}
	public function revenue_accounts($param1=''){// Redefined
		if($param1!==""){
			$this->db->where(array('status'=>$param1));
		}
		
		$query = $this->db->order_by('AccNo')->get_where('accounts',array('AccGrp'=>'1'))->result_object();
		
		return $query;
	}
	
	function projects(){
		return $this->db->select(array('cname','fname'))->get_where('users',array('userlevel'=>'1','department'=>'0','cname!='=>'Kenya'))->result_object();
	}
	function expense_accounts_association($code=""){
		
		$revenue_id = $this->db->get_where('accounts',array('AccNo'=>$code))->row()->accID;
		
		$cnt_assoc = $this->db->get_where('accounts',array('parentAccID'=>$revenue_id))->num_rows();
		
		$rmk = "0";
		
		if($cnt_assoc>0){
			$rmk = "1";
		}
		
		return $rmk;
	}
	
	/**public function mfr_submit_state($TDate){
		
		$state = (object)array('last_mfr'=>0,'current_mfr'=>0,'next_mfr'=>0);		
		
		//Last MFR
		$last_mfr = $this->db->get_where('opfundsbalheader',array('closureDate'=>date('Y-m-t',strtotime('first day of previous month',strtotime($TDate))),'project_id'=>$this->session->userdata('center_id'),'submitted'=>1))->row();
		
		if(count($last_mfr)>0){
			$state->last_mfr=1;
		}
		
		//Current MFR
		$current_mfr = $this->db->get_where('opfundsbalheader',array('closureDate'=>date('Y-m-t',strtotime($TDate)),'project_id'=>$this->session->userdata('center_id'),'submitted'=>1))->row();
		
		if(count($current_mfr)>0){
			$state->current_mfr=1;
		}
		
		//Next MFR
		$next_mfr = $this->db->get_where('opfundsbalheader',array('closureDate'=>date('Y-m-t',strtotime('first day of next month',strtotime($TDate))),'project_id'=>$this->session->userdata('center_id'),'submitted'=>1))->row();
		
		if(count($next_mfr)>0){
			$state->next_mfr=1;
		}		
		
		return $state;
	}	**/
	
	function current_financial_month($project_id){
			
		$date = date('Y-m-d',strtotime('last month',strtotime($this->project_system_start_date($project_id))));
		
		if($this->db->get_where('opfundsbalheader',array('icpNo'=>$project_id))->num_rows()>0){
			$date = $this->db->select_max('closureDate')->get_where('opfundsbalheader',array('icpNo'=>$project_id))->row()->closureDate;
		}
		
		return date('Y-m-d',strtotime('first day of next month',strtotime($date)));
	}		
		

	public function outstanding_cheques($date, $project, $oc = TRUE)
	{
		$cond_os = "((TDate<='" . date('Y-m-t', strtotime($date)) . "' AND icpNo='" . $project . "' AND ChqState='0' AND VType IN ('CHQ','UDCTB'))";
		$cond_os .= " OR (TDate<='" . date('Y-m-t', strtotime($date)) . "' AND icpNo='" . $project . "' AND ChqState='1' AND clrMonth >'" . date('Y-m-t', strtotime($date)) . "' AND VType IN ('CHQ','UDCTB')))";

		if ($oc === FALSE) {
			$cond_os = "clrMonth>='" . date('Y-m-01', strtotime($date)) . "' AND clrMonth<='" . date('Y-m-t', strtotime($date)) . "' AND icpNo='" . $project . "' AND ChqState='1' AND VType IN ('CHQ','UDCTB')";
		}

		$os_query = $this->db->where($cond_os)->get('voucher_header')->result_array();

		return $os_query;
	}

	public function deposit_transit($date,$project,$dep=TRUE){
		$cond_dep = "((TDate<='".date('Y-m-t',strtotime($date))."' AND icpNo='".$project."' AND (ChqState='0' OR clrMonth>'".date('Y-m-t',strtotime($date))."') AND VType='CR')";				
		$cond_dep .= " OR (TDate<='".date('Y-m-t',strtotime($date))."' AND icpNo='".$project."' AND ChqState='1' AND clrMonth>'".date('Y-m-t',strtotime($date))."' AND VType='CR'))";				
		
		if($dep===FALSE){
			$cond_dep = "clrMonth>='".date('Y-m-01',strtotime($date))."' AND clrMonth<='".date('Y-m-t',strtotime($date))."' AND icpNo='".$project."' AND ChqState='1' AND VType='CR'";	
		}
		
		$dep_query = $this->db->where($cond_dep)->get('voucher_header')->result_array();
		
		return $dep_query;
	}	

	public function last_voucher($param1=""){
			
		$this->db->where('icpNo',$param1);
		$this->db->order_by('VNumber','asc');
		$query = $this->db->get('voucher_header')->last_row();
		return $query;
	}

	public function statement_balance($project_id='',$date=""){
		
		$statement = (object)array('month'=>$this->project_system_start_date($project_id),'icpNo'=>$project_id,'amount'=>0);
		
		if($this->db->get_where('statementbal',array('month'=>date('Y-m-t',strtotime($date)),'icpNo'=>$project_id))->num_rows()>0){
			$statement = $this->db->get_where('statementbal',array('month'=>date('Y-m-t',strtotime($date)),'icpNo'=>$project_id))->row();
		}
		
		return $statement;//$this->db->get_where('statementbal',array('month'=>$param1))->row();
	}	

	public function sum_deposit_transit($date="",$project=""){
		//$cond_sum_dep = "TDate>='".$this->project_system_start_date($project)."' AND TDate<='".date('Y-m-t',strtotime($date))."' AND icpNo='".$project."' AND (clrMonth = '0000-00-00' OR clrMonth > '".date('Y-m-t',strtotime($date))."') AND VType='CR'";				
		$cond_sum_dep = "((TDate<='".date('Y-m-t',strtotime($date))."' AND icpNo='".$project."' AND (ChqState='0' OR clrMonth>'".date('Y-m-t',strtotime($date))."') AND VType='CR')";				
		$cond_sum_dep .= " OR (TDate<='".date('Y-m-t',strtotime($date))."' AND icpNo='".$project."' AND ChqState='1' AND clrMonth>'".date('Y-m-t',strtotime($date))."' AND VType='CR'))";				
		
		$sum_dep_query = 0;
		
		if($this->db->where($cond_sum_dep)->get('voucher_header')->num_rows()>0){
			//$cond_sum_dep = "TDate>='".$this->project_system_start_date($project)."' AND TDate<='".date('Y-m-t',strtotime($date))."' AND icpNo='".$project."' AND (clrMonth = '0000-00-00' OR clrMonth > '".date('Y-m-t',strtotime($date))."') AND VType='CR'";	
			$cond_sum_dep = "((TDate<='".date('Y-m-t',strtotime($date))."' AND icpNo='".$project."' AND (ChqState='0' OR clrMonth>'".date('Y-m-t',strtotime($date))."') AND VType='CR')";				
			$cond_sum_dep .= " OR (TDate<='".date('Y-m-t',strtotime($date))."' AND icpNo='".$project."' AND ChqState='1' AND clrMonth>'".date('Y-m-t',strtotime($date))."' AND VType='CR'))";				
		
			$sum_dep_query = $this->db->select_sum('totals')->where($cond_sum_dep)->get('voucher_header')->row()->totals;	
		}
		
		return $sum_dep_query;
	}
		
	public function sum_outstanding_cheques($date="",$project=""){
		//$cond_sum_os = "TDate>='".$this->project_system_start_date($project)."' AND TDate<='".date('Y-m-t',strtotime($date))."' AND icpNo='".$project."' AND (ChqState='0' OR clrMonth>'".date('Y-m-t',strtotime($date))."') AND VType='CHQ'";		
		//$sum_os_query = 0;//$this->db->select_sum('totals')->where($cond_sum_os)->get('voucher_header')->row()->totals;
		
		//if($this->db->where($cond_sum_os)->get('voucher_header')->num_rows()>0){
			//$cond_sum_os = "TDate>='".$this->project_system_start_date($project)."' AND TDate<='".date('Y-m-t',strtotime($date))."' AND icpNo='".$project."' AND (ChqState='0' OR clrMonth>'".date('Y-m-t',strtotime($date))."') AND VType='CHQ'";		
			
			//$sum_os_query = $this->db->select_sum('totals')->where($cond_sum_os)->get('voucher_header')->row()->totals;					
		//}
		
		//return $sum_os_query;		
		
		$oc = $this->outstanding_cheques($date,$project);
		
		$oc_total = 0;
		foreach($oc as$row):
			$oc_total+=$this->db->select_sum('Cost')->get_where('voucher_body',array('hID'=>$row['hID']))->row()->Cost;
		endforeach;	
		
		return $oc_total;
	}	
	public function adjusted_bank_balance($date='',$project=""){
		$statement = $this->statement_balance($project,$date)->amount;
		$deposit_in_transit = $this->sum_deposit_transit($date,$project);
		$outstanding_cheques = $this->sum_outstanding_cheques($date,$project);
		
		$adj = $statement+($deposit_in_transit-$outstanding_cheques);
		
		return $adj;
	}
	//Perfect
	public function months_bank_income($date,$project){
		//Income
		$cond_bank_income = "TDate>='".date('Y-m-01',strtotime($date))."' AND TDate<='".date('Y-m-t',strtotime($date))."' AND icpNo='".$project."' AND (VType = 'CR' OR VType='PCR')";
		$bank_income = $this->db->select_sum('Cost')->where($cond_bank_income)->get('voucher_body')->row()->Cost;
		
		return $bank_income;		
	}
	
	public function bank_income_to_date($project,$date){
		//Income
		$cond_bank_income = "TDate<'".date('Y-m-01',strtotime($date))."' AND icpNo='".$project."' AND (VType = 'CR' OR VType='PCR')";
		$bank_income = $this->db->select_sum('Cost')->where($cond_bank_income)->get('voucher_body')->row()->Cost;
		
		return $bank_income;		
	}	
	
	// //Perfect
	// public function months_bank_expense($date,$project){
	// 	//Expenses
	// 	$cond_bank_exp = "TDate>='".date('Y-m-01',strtotime($date))."' AND TDate<='".date('Y-m-t',strtotime($date))."' AND icpNo='".$project."' AND (VType = 'CHQ' OR VType='BCHG')";
	// 	$bank_exp = $this->db->select_sum('Cost')->where($cond_bank_exp)->get('voucher_body')->row()->Cost;
		
	// 	return $bank_exp;		
	// }

		//Perfect
		public function months_bank_expense($date, $project)
		{
			//Expenses
			$cond_bank_exp = "TDate>='" . date('Y-m-01', strtotime($date)) . "' AND TDate<='" . date('Y-m-t', strtotime($date)) . "' AND icpNo='" . $project . "' AND (VType = 'CHQ' OR VType='BCHG' OR VType='UDCTB')";
			$bank_exp = $this->db->select_sum('Cost')->where($cond_bank_exp)->get('voucher_body')->row()->Cost;
	
			return $bank_exp;
		}

	// public function bank_expense_to_date($project,$date){
	// 	//Expenses
	// 	$cond_bank_exp = "TDate<'".date('Y-m-01',strtotime($date))."' AND icpNo='".$project."' AND (VType = 'CHQ' OR VType='BCHG')";
	// 	$bank_exp = $this->db->select_sum('Cost')->where($cond_bank_exp)->get('voucher_body')->row()->Cost;
		
	// 	return $bank_exp;		
	// }

	public function bank_expense_to_date($project, $date)
	{
		//Expenses
		$cond_bank_exp = "TDate<'" . date('Y-m-01', strtotime($date)) . "' AND icpNo='" . $project . "' AND (VType = 'CHQ' OR VType='BCHG' OR VType='UDCTB')";
		$bank_exp = $this->db->select_sum('Cost')->where($cond_bank_exp)->get('voucher_body')->row()->Cost;

		return $bank_exp;
	}
		
	public function bank_balance($date="",$project=""){
		$begin_bank = $this->opening_bank_balance($date,$project);
		$bank_income = $this->months_bank_income($date,$project);
		$bank_exp = $this->months_bank_expense($date,$project);
		
		
		$bank_bal = $begin_bank+$bank_income-$bank_exp;
		
		return $bank_bal;
	}	

	public function petty_cash_balance($date,$project){
		$begin_pc = $this->opening_pc_balance($date,$project);
		$pc_income = $this->months_pc_income($project,$date);
		$pc_exp = $this->months_pc_expense($project,$date);
		
		
		$pc_bal = $begin_pc+$pc_income-$pc_exp;
		
		return $pc_bal;
	}	
	
	/**public function petty_cash_balance_test($date,$project){
		$begin_pc = $this->finance_model->opening_pc_balance(date('Y-m-t',strtotime($date)),$project);
		
		$condition = "Month(`TDate`)='".date('m',strtotime($date))."' AND Fy='".get_fy(date('Y-m-d',strtotime($date)),$project)."' AND icpNo='".$project."'";
		$records = $this->db->where($condition)->get('voucher_header')->result_array();						
		$sum_pc_balance = 0;								
		$sum_pc_income = 0;
		$sum_pc_payment = 0;
				foreach($records as $rw):
					$cond_inc = "(AccNo='2000' OR AccNo='2001')  AND hID=".$rw['hID'];
					$sum_pc_income+=$this->db->select_sum('Cost')->where($cond_inc)->get('voucher_body')->row()->Cost;
										
					$cond_pay = "(VType='PC' OR VType='PCR') AND hID=".$rw['hID'];
					$sum_pc_payment+=$this->db->select_sum('Cost')->where($cond_pay)->get('voucher_body')->row()->Cost;
											
				endforeach;
										
		$sum_pc_balance = $begin_pc+$sum_pc_income-$sum_pc_payment;
										
		return $sum_pc_balance; 
	}**/

	public function months_pc_income($project,$date){
		//Income
		$cond_pc_income = "TDate>='".date('Y-m-01',strtotime($date))."' AND TDate<='".date('Y-m-t',strtotime($date))."' AND icpNo='".$project."' AND (AccNo = '2000' OR AccNo = '2001')";
		$pc_income = $this->db->select_sum('Cost')->where($cond_pc_income)->get('voucher_body')->row()->Cost;
		
		return $pc_income;		
	}

	public function pc_income_to_date($project,$date){
		//Income
		$cond_pc_income = "TDate<'".date('Y-m-01',strtotime($date))."' AND icpNo='".$project."' AND (AccNo = '2000' OR AccNo='2001')";
		$pc_income = $this->db->select_sum('Cost')->where($cond_pc_income)->get('voucher_body')->row()->Cost;
		
		return $pc_income;		
	}
		
	// public function months_pc_expense($project,$date){
	// 	//Expenses
	// 	$cond_pc_exp = "TDate>='".date('Y-m-01',strtotime($date))."' AND TDate<='".date('Y-m-t',strtotime($date))."' AND icpNo='".$project."' AND (VType = 'PC' OR VType = 'PCR')";
	// 	$pc_exp = $this->db->select_sum('Cost')->where($cond_pc_exp)->get('voucher_body')->row()->Cost;
		
	// 	return $pc_exp;		
	// }

	public function months_pc_expense($project, $date)
	{
		//Expenses
		$this->db->join('voucher_header','voucher_header.hID=voucher_body.hID');
		$cond_pc_exp = "voucher_header.TDate>='" . date('Y-m-01', strtotime($date)) . "' AND voucher_header.TDate<='" . date('Y-m-t', strtotime($date)) . "' AND voucher_header.icpNo='" . $project . "' AND (voucher_header.VType = 'PC' OR voucher_header.VType = 'PCR' OR voucher_header.VType='UDCTC')";
		$pc_exp = $this->db->select_sum('Cost')->where($cond_pc_exp)->get('voucher_body')->row()->Cost;

		return $pc_exp;
	}
	
	public function pc_expense_to_date($project,$date){
		//Expenses
		$cond_pc_exp = "TDate<'".date('Y-m-01',strtotime($date))."' AND icpNo='".$project."' AND (VType = 'PC' OR VType = 'PCR')";
		$pc_exp = $this->db->select_sum('Cost')->where($cond_pc_exp)->get('voucher_body')->row()->Cost;
		
		return $pc_exp;		
	}	
	
	public function total_cash($date,$project){
		$bank = $this->bank_balance($date,$project);
		$pc = $this->petty_cash_balance($date,$project);
		
		$total_cash = $bank+$pc;
		
		return $total_cash;
	}			

	public function budgeted_revenue_accounts(){
		$rev = $this->db->order_by('AccNo')->get_where('accounts',array('AccGrp'=>'1','budget'=>'1','active'=>'1'))->result_object();
		
		return $rev;
	}
	
	public function check_budgeted_revenue_account($rev_id){
		$state = $this->db->get_where('revenue',array('revenue_id'=>$rev_id))->row()->budgeted;
		
		return $state;
	}	
	
	public function months_income_accounts_utilized($project,$tym){
		return $this->db->select('accounts.AccNo,accounts.AccText')->join('accounts','accounts.AccNo=voucher_body.AccNo')->order_by('accounts.AccNo')->group_by('voucher_body.AccNo')->get_where('voucher_body',array('AccGrp'=>'1',"voucher_body.icpNo"=>$project,"TDate>="=>date('Y-m-01',$tym),"TDate>="=>date('Y-m-01',$tym)))->result_object();
		
	}
	
	public function months_expenses_accounts_utilized($project,$tym){
		return $this->db->select('accounts.AccNo,accounts.AccText')->join('accounts','accounts.AccNo=voucher_body.AccNo')->order_by('accounts.AccNo')->group_by('voucher_body.AccNo')->get_where('voucher_body',array('AccGrp'=>'0',"voucher_body.icpNo"=>$project,"TDate>="=>date('Y-m-01',$tym),"TDate>="=>date('Y-m-01',$tym)))->result_object();
		
	}

	public function expense_accounts($rev_code=""){
		
		if($rev_code!==""){
			$query = $this->db->order_by('AccNo')->get_where('accounts',array('parentAccID'=>$rev_code))->result_object();
		}else{
			$query = $this->db->order_by('AccNo')->get_where('accounts',array('AccGrp'=>'0'))->result_object();
		}
		
		
		return $query;
	}	
	
	function current_fy($project){		
		
		$fy ="";
		
		if($this->db->get_where('voucher_header',array('icpNo'=>$project))->num_rows()>0){
			
			$fy = get_fy($this->db->select_max('TDate')->get_where('voucher_header',array('icpNo'=>$project))->row()->TDate,$project);
			
		}elseif($this->db->get_where('planheader',array('icpNo'=>$project))->num_rows()>0){
			// Use plans	
			$plan_id = $this->db->select_max('planHeaderID')->get_where('planheader',array('icpNo'=>$project))->row()->planHeaderID;
			
			$fy = $this->db->get_where('planheader',array('planHeaderID'=>$plan_id))->row()->fy;
			
		}else{
			// Use system start date 
			$fy = get_fy($this->project_system_start_date($project),$project);
		}
		
		return $fy;
	}
	
	public function months_in_year($date,$show_year=false){
		
		$months = array();
		
		//$date = date('Y-m-d');
		
		//$start_month = $this->db->get_where('projectsdetails',array('project_id'=>$project))->row()->system_start_date;
		
		//$start_month = $this->project_system_start_date($project);
		$start_month = fy_start_date($date);
		
		if($show_year===false){
			$months['month_1_amount'] 	= date('M',strtotime($start_month));
			$months['month_2_amount'] 	= date('M',strtotime('+1 month',strtotime($start_month)));
			$months['month_3_amount'] = date('M',strtotime('+2 month',strtotime($start_month)));
			$months['month_4_amount'] = date('M',strtotime('+3 month',strtotime($start_month)));
			$months['month_5_amount'] = date('M',strtotime('+4 month',strtotime($start_month)));
			$months['month_6_amount'] = date('M',strtotime('+5 month',strtotime($start_month)));
			$months['month_7_amount'] = date('M',strtotime('+6 month',strtotime($start_month)));
			$months['month_8_amount'] = date('M',strtotime('+7 month',strtotime($start_month)));
			$months['month_9_amount'] = date('M',strtotime('+8 month',strtotime($start_month)));
			$months['month_10_amount'] = date('M',strtotime('+9 month',strtotime($start_month)));
			$months['month_11_amount'] = date('M',strtotime('+10 month',strtotime($start_month)));
			$months['month_12_amount'] = date('M',strtotime('+11 month',strtotime($start_month)));			
		}else{
			$months['month_1_amount'] 	= date('M Y',strtotime($start_month));
			$months['month_2_amount'] 	= date('M Y',strtotime('+1 month',strtotime($start_month)));
			$months['month_3_amount'] = date('M Y',strtotime('+2 month',strtotime($start_month)));
			$months['month_4_amount'] = date('M Y',strtotime('+3 month',strtotime($start_month)));
			$months['month_5_amount'] = date('M Y',strtotime('+4 month',strtotime($start_month)));
			$months['month_6_amount'] = date('M Y',strtotime('+5 month',strtotime($start_month)));
			$months['month_7_amount'] = date('M Y',strtotime('+6 month',strtotime($start_month)));
			$months['month_8_amount'] = date('M Y',strtotime('+7 month',strtotime($start_month)));
			$months['month_9_amount'] = date('M Y',strtotime('+8 month',strtotime($start_month)));
			$months['month_10_amount'] = date('M Y',strtotime('+9 month',strtotime($start_month)));
			$months['month_11_amount'] = date('M Y',strtotime('+10 month',strtotime($start_month)));
			$months['month_12_amount'] = date('M Y',strtotime('+11 month',strtotime($start_month)));				
		}

		
		return $months;
	}	

	function system_opening_fund_balances($date,$project){
		
		$bals = array();
		
		if($this->db->get_where('opfundsbalheader',array('closureDate'=>$date,'icpNo'=>$project))->num_rows()>0){
			$balHdID = $this->db->get_where('opfundsbalheader',array('closureDate'=>$date,'icpNo'=>$project))->row()->balHdID;
			
			$bals = $this->db->get_where('opfundsbal',array('balHdID'=>$balHdID))->result_object();			
		}
		

		
		return $bals;	
	}
	
	function get_bank_details($bank_id){
		return $this->db->get_where('banks',array('bankID'=>$bank_id))->row();
	}
	
	function plans_per_account_per_month($fy,$project_id,$expense_id,$month_key){
		$planHeaderID = $this->db->get_where('planheader',array('fy'=>$fy,'icpNo'=>$project_id))->row()->planHeaderID;
		
		$month = 'month_'.$month_key.'_amount';
		
		$result = $this->db->select_sum($month)->get_where('plansschedule',array('planHeaderID'=>$planHeaderID,'AccNo'=>$expense_id))->row()->$month;
		
		return $result;
	}
	
	function plans_per_account($fy,$project_id,$expense_id){
		$total = 0;	
		if(!empty($this->db->get_where('planheader',array('fy'=>$fy,'icpNo'=>$project_id))->row())){	
		$planHeaderID = $this->db->get_where('planheader',array('fy'=>$fy,'icpNo'=>$project_id))->row()->planHeaderID;
		
		$total = $this->db->select_sum('totalCost')->get_where('plansschedule',array('planHeaderID'=>$planHeaderID,'AccNo'=>$expense_id))->row()->totalCost;
		}
		return $total;		
	}
	
	function plans_annual_totals($fy,$project_id,$revenue_id,$approved = ""){
		
		$planHeaderID = $this->db->get_where('planheader',array('fy'=>$fy,'icpNo'=>$project_id))->row()->planHeaderID;
		
		$expense_accounts = $this->expense_accounts($revenue_id);
		
		$all_totals = 0;
		
		foreach($expense_accounts as $acc):
				
				if($this->db->get_where('plansschedule',array('planHeaderID'=>$planHeaderID,'AccNo'=>$acc->AccNo))->row()){	
					
					if($approved===""){
						$all_totals += $this->db->select_sum('totalCost')->get_where('plansschedule',array('planHeaderID'=>$planHeaderID,'AccNo'=>$acc->AccNo))->row()->totalCost;
					}else{
						$all_totals += $this->db->select_sum('totalCost')->get_where('plansschedule',array('planHeaderID'=>$planHeaderID,'AccNo'=>$acc->AccNo,'approved'=>$approved))->row()->totalCost;
					}
				}
							

		endforeach;	
		
		return $all_totals;
	}
	
	function plans_per_month($fy,$project_id,$revenue_id,$month){
		$expense_accounts = $this->expense_accounts($revenue_id);
		
		$adj_month = 'month_'.$month.'_amount';
		
		$all_totals = 0;
		
		foreach($expense_accounts as $acc):
					
				$this->db->where(array('planheader.fy'=>$fy,'planheader.icpNo'=>$project_id,'plansschedule.AccNo'=>$acc->AccNo));
				
				$all_totals += $this->db->select_sum($adj_month)->join('planheader','planheader.planHeaderID=plansschedule.planHeaderID')->get('plansschedule')->row()->$adj_month;				

		endforeach;	
		
		return $all_totals;		
	}
	
	function projects_missing_plans_limit($rev_id,$fy){
		
		$projects = $this->db->get('projectsdetails')->result_object();
		
		$new_arr = array();
		
		foreach($projects as $project):
			if(!$this->db->get_where('plans_limits',array('revenue_id'=>$rev_id,'fy'=>$fy,'project_id'=>$project->project_id))->row())
				$new_arr[] = $project;			
		endforeach;
		
		return $new_arr;
	}
	
	function limits_status_check($rev_id,$project_id,$fy){
		$planHeaderID = $this->db->get_where('planheader',array('fy'=>$fy,'icpNo'=>$project_id))->row()->planHeaderID;
		
		$plan_total = 0;
		
		$expense_accounts = $this->db->get_where('accounts',array('parentAccID'=>$rev_id))->result_object();
		
		foreach($expense_accounts as $expense):
			$plan_total +=$this->db->select_sum('totalCost')->get_where('plansschedule',array('planHeaderID'=>$planHeaderID,'AccNo'=>$expense->AccNo,'approved'=>'2'))->row()->totalCost;
		endforeach;	
		
		$plan_limit = $this->db->get_where('plans_limits',array('fy'=>$fy,'revenue_id'=>$rev_id,'icpNo'=>$project_id))->row()->amount;
		
		$return_arr = array('msg'=>get_phrase('ok'),'color'=>'success','dif'=>0);
		
		$dif = 0;
		
		if($plan_total<$plan_limit){
			$dif = $plan_total - $plan_limit;
			$return_arr = array('msg'=>get_phrase('below_limit'),'color'=>'info','dif'=>$dif);
		}elseif($plan_total>$plan_limit){
			$dif = $plan_total - $plan_limit;
			$return_arr = array('msg'=>get_phrase('above_limit'),'color'=>'warning','dif'=>$dif);
		}
		
		return (object)$return_arr;	
	}

	function months_expenses_per_revenue_vote($project_id,$rev_id,$month){
			
		$total = 0;
		
		$expense_account = $this->expense_accounts($rev_id);
		
		foreach($expense_account as $account){
			$this->db->where(array('icpNo'=>$project_id,'AccNo'=>$account->AccNo,'TDate>='=>date('Y-m-01',strtotime($month)),'TDate<='=>date('Y-m-t',strtotime($month))));
			
			$total += $this->db->select_sum('Cost')->get('voucher_body')->row()->Cost;
		}
		
		return $total;
	}
	
	function months_incomes_per_revenue_vote($project_id,$rev_id,$month){
		
		if($this->db->get_where('accounts',array('AccNo'=>$rev_id))->row()){
			//$rev_ac = $this->db->get_where('accounts',array('AccNo'=>$rev_id))->row()->AccNo;
					
			$this->db->where(array('icpNo'=>$project_id,'AccNo'=>$rev_id,'TDate>='=>date('Y-m-01',strtotime($month)),'TDate<='=>date('Y-m-t',strtotime($month))));
				
			return $this->db->select_sum('Cost')->get('voucher_body')->row()->Cost;
		}else{
			return 0;
		}
	}
	
	function months_opening_fund_balances($project_id,$rev_id,$month){
		
		$month_opening_bal = 0;
			
			if($this->db->get_where('opfundsbalheader',array('icpNo'=>$project_id,'closureDate'=>date('Y-m-t',strtotime('last day of previous month',strtotime($month)))))->num_rows()>0){
				
				$balHd = $this->db->get_where('opfundsbalheader',array('icpNo'=>$project_id,'closureDate'=>date('Y-m-d',strtotime('last day of previous month',strtotime($month)))))->row();
				
				if($this->db->get_where('opfundsbal',array('balHdID'=>$balHd->balHdID,'funds'=>$rev_id))->num_rows()>0){
					$month_opening_bal = $this->db->get_where('opfundsbal',array('balHdID'=>$balHd->balHdID,'funds'=>$rev_id))->row()->amount;	
				}
				
				 
			}		
		
		return $month_opening_bal;
	}

	function sum_months_opening_revenues($project_id,$month){
		$this->db->join("opfundsbalheader","opfundsbalheader.balHdID = opfundsbal.balHdID");
		$this->db->where(array("icpNo"=>$project_id,"closureDate"=>date("Y-m-t",strtotime('last day of previous month',strtotime($month)))));
		return $this->db->select_sum('amount')->get("opfundsbal")->row()->amount;
	}

	function total_months_opening_revenues($project_id,$month){
		$total = 0;
		$rec_accs = $this->db->get_where('accounts',array("AccGrp"=>"1"))->result_object();
			foreach($rec_accs as $row):
				//if($this->finance_model->months_opening_fund_balances($project_id,$row->revenue_id,$rev_id)>'0'){
						$total+=$this->finance_model->months_opening_fund_balances($project_id,$row->AccNo,$month);
				//}
			endforeach;
		
		return $total;
	}
	
	function total_months_incomes($project_id,$month){
		$total = 0;
		$rec_accs = $this->db->get_where('accounts',array("AccGrp"=>"1"))->result_object();
			foreach($rec_accs as $row):
				//if($this->finance_model->months_opening_fund_balances($project_id,$row->revenue_id,$rev_id)>'0'){
						$total+=$this->finance_model->months_incomes_per_revenue_vote($project_id,$row->AccNo,$month);
				//}
			endforeach;
		
		return $total;		
	}
	
	function total_months_expenses($project_id,$month){
		$total = 0;
		$rec_accs = $this->db->get_where('accounts',array("AccGrp"=>"1"))->result_object();
			foreach($rec_accs as $row):
				//if($this->finance_model->months_opening_fund_balances($project_id,$row->revenue_id,$rev_id)>'0'){
						$total+=$this->finance_model->months_expenses_per_revenue_vote($project_id,$row->accID,$month);
				//}
			endforeach;
		
		return $total;		
	}
	
	function total_months_closing_balance($project_id,$month){
		$total = 0;
		$rec_accs = $this->db->get_where('accounts',array("AccGrp"=>"1"))->result_object();
			foreach($rec_accs as $row):
				//if($this->finance_model->months_opening_fund_balances($project_id,$row->revenue_id,$rev_id)>'0'){
						$total+=$this->finance_model->months_closing_fund_balance_per_revenue_vote($project_id,$row->AccNo,$month);
				//}
			endforeach;
		
		return $total;		
	}	
	
		
	
	function months_closing_fund_balance_per_revenue_vote($project_id,$rev_id,$month){
		$open = $this->months_opening_fund_balances($project_id, $rev_id, $month);
		$inc = $this->months_incomes_per_revenue_vote($project_id, $rev_id, $month);
		
		$accID = $this->db->get_where('accounts',array('AccNo'=>$rev_id))->row()->accID;
		
		$exp = $this->months_expenses_per_revenue_vote($project_id, $accID, $month);
		
		return $open + $inc - $exp;
	}
	
	function months_expenses_per_expense_account($project_id,$exp_id,$month){
		
		//$exp = $this->db->get_where('accounts',array('AccNo'=>$exp_id))->row()->AccNo;
		
		$this->db->where(array('voucher_header.icpNo'=>$project_id,'voucher_body.AccNo'=>$exp_id,'voucher_header.TDate>='=>date('Y-m-01',strtotime($month)),'voucher_header.TDate<='=>date('Y-m-t',strtotime($month))));
		$this->db->join('voucher_header','voucher_header.hID=voucher_body.hID');
		return $this->db->select_sum('Cost')->get('voucher_body')->row()->Cost;
	}
	
	function months_expenses_to_date_per_expense_account($project_id,$exp_id,$month){
		//$exp = $this->db->get_where('expense',array('expense_id'=>$exp_id))->row()->code;
		
		$this->db->where(array('voucher_header.icpNo'=>$project_id,'.voucher_body.AccNo'=>$exp_id,'voucher_header.Fy'=>get_fy($month,$project_id),'voucher_header.TDate<='=>date('Y-m-t',strtotime($month))));
		$this->db->join('voucher_header','voucher_header.hID=voucher_body.hID');
		return $this->db->select_sum('Cost')->join('voucher_header','voucher_header.hID=voucher_body.hID')->get('voucher_body')->row()->Cost;		
	}
	
	function months_budget_to_date_per_expense_account($project_id,$fy,$exp_id,$month){
		
		$planHeaderID = $this->db->get_where('planheader',array('icpNo'=>$project_id,'fy'=>$fy))->row()->planHeaderID;
		
		$start_month = fy_start_date($this->project_system_start_date($project_id));
		
		$end_month = date('n',strtotime($month));
		
		
		$fields['month_1_amount'] = date('n',strtotime($start_month));
		$fields['month_2_amount'] = date('n',strtotime('+1 month',strtotime($start_month)));
		$fields['month_3_amount'] = date('n',strtotime('+2 month',strtotime($start_month)));
		$fields['month_4_amount'] = date('n',strtotime('+3 month',strtotime($start_month)));
		$fields['month_5_amount'] = date('n',strtotime('+4 month',strtotime($start_month)));
		$fields['month_6_amount'] = date('n',strtotime('+5 month',strtotime($start_month)));
		$fields['month_7_amount'] = date('n',strtotime('+6 month',strtotime($start_month)));
		$fields['month_8_amount'] = date('n',strtotime('+7 month',strtotime($start_month)));
		$fields['month_9_amount'] = date('n',strtotime('+8 month',strtotime($start_month)));
		$fields['month_10_amount'] = date('n',strtotime('+9 month',strtotime($start_month)));
		$fields['month_11_amount'] = date('n',strtotime('+10 month',strtotime($start_month)));
		$fields['month_12_amount'] = date('n',strtotime('+11 month',strtotime($start_month)));	
		
		$total = 0;		

		foreach($fields as $key=>$value):
				$this->db->where(array('planHeaderID'=>$planHeaderID,'AccNo'=>$exp_id));
				$total += $this->db->select_sum($key)->get('plansschedule')->row()->$key;
			if($value===$end_month){		
				break;
			}
		endforeach;
		
		return $total;
	}
	
	function months_budget_variance_per_expense_account($project_id,$fy,$exp_id,$month){
		$exp_to_date = $this->months_expenses_to_date_per_expense_account($project_id,$exp_id,$month);
		
		$budget_to_date = $this->months_budget_to_date_per_expense_account($project_id,$fy,$exp_id,$month);
		
		return $budget_to_date - $exp_to_date;
	}
	
	function months_budget_variance_percent_per_expense_account($project_id,$fy,$exp_id,$month){
		$exp_to_date = $this->months_expenses_to_date_per_expense_account($project_id,$exp_id,$month);
		
		$budget_to_date = $this->months_budget_to_date_per_expense_account($project_id,$fy,$exp_id,$month);
		
		$variance =  $budget_to_date - $exp_to_date;
		
		if($budget_to_date == 0 && $exp_to_date !== 0){
			return -100;
		}else{			
			return 	@($variance/$budget_to_date)*100;
		}
		
		
	}
	
	function total_expense_to_date_per_revenue_vote($project_id,$rev_id,$month){
		$total = 0;
		
		$expense_account = $this->expense_accounts($rev_id);
		
		foreach($expense_account as $account){
			$this->db->where(array('icpNo'=>$project_id,'AccNo'=>$account->AccNo,'TDate>='=>date('Y-m-01',strtotime(fy_start_date($month,$project_id))),'TDate<='=>date('Y-m-t',strtotime($month))));
			
			$total += $this->db->select_sum('Cost')->get('voucher_body')->row()->Cost;
		}
		
		return $total;
	}

	function get_income_account_with_expenses($project_id,$month){
		$accounts = [];

		$fy_start_date = fy_start_date($month,$project_id);

		$join_query = "SELECT accID as join_income_account_id,AccText as join_account_code,AccName as join_account_name,AccNo as join_account_number FROM accounts WHERE AccGrp = 1";
		
		$this->db->select(array('DISTINCT(accounts.AccNo) as account_number'));

		$this->db->select(array('join_account_code','join_account_name','join_account_number','accID as account_id','parentAccID as parent_account_id',
		'accounts.AccText as account_code','accounts.AccName as account_name'));
		
		

		$this->db->where(array('icpNo'=>$project_id,'AccGrp<>'=>3));
		$this->db->where(array('TDate>='=>date('Y-m-01',strtotime($fy_start_date)),'TDate<='=>date('Y-m-t',strtotime($month))));
		$this->db->join('voucher_body','voucher_body.AccNo=accounts.AccNo');
		$this->db->join('('.$join_query.') as join_accounts','join_accounts.join_income_account_id=accounts.parentAccID','LEFT',NULL);
		$accounts_obj = $this->db->get('accounts');

		if($accounts_obj->num_rows() > 0){
			$accounts = $accounts_obj->result_object();
		}

		$income_accounts_array = [];

		foreach($accounts as $account){
			if($account->parent_account_id == 0 && !isset($income_accounts_array[$account->account_id])){
				$income_accounts_array[$account->account_number] = ['account_code'=>$account->account_code,'account_name'=>$account->account_name];
			}else{
				$income_accounts_array[$account->join_account_number] = ['account_code'=>$account->join_account_code,'account_name'=>$account->join_account_name];
			}
		}

		return $income_accounts_array;
	}

	
	function total_revenue_to_date_per_revenue_vote($project_id,$rev_id,$month){
		
		if($this->db->get_where('accounts',array('AccNo'=>$rev_id))->row()){
			$rev_ac = $this->db->get_where('accounts',array('AccNo'=>$rev_id))->row()->AccNo;
			
			$this->db->where(array('icpNo'=>$project_id,'AccNo'=>$rev_ac,'TDate<='=>date('Y-m-t',strtotime($month))));
			
			return $this->db->select_sum('Cost')->get('voucher_body')->row()->Cost;
		
		}else{
			return 0;
		}
		
	}
	
	function unapproved_budget_items($project="",$month="",$statusCode=""){
		
		$fy = get_fy($month,$project);
		
		$items = 0;
		
		if($this->db->get_where('planheader',array('icpNo'=>$project,'fy'=>$fy))->num_rows()>0){
			$planHeaderID =  $this->db->get_where('planheader',array('icpNo'=>$project,'fy'=>$fy))->row()->planHeaderID;
				
				if($this->db->select_sum('totalCost')->get_where('plansschedule',array('planHeaderID'=>$planHeaderID,'approved<>'=>'2','AccNo<>'=>0))->num_rows()>0){
					if($statusCode===""){
						$items =  $this->db->select_sum('totalCost')->get_where('plansschedule',array('planHeaderID'=>$planHeaderID,'approved<>'=>'2','AccNo<>'=>0))->row()->totalCost;
					}else{
						$items =  $this->db->select_sum('totalCost')->get_where('plansschedule',array('planHeaderID'=>$planHeaderID,'approved'=>$statusCode,'AccNo<>'=>0))->row()->totalCost;
					}
				
				}
		}
		
		return $items;
	}
	
	function total_budget_to_date_per_revenue_vote($project_id,$fy,$rev_id,$month){
		$planHeaderID = $this->db->get_where('planheader',array('icpNo'=>$project_id,'fy'=>$fy))->row()->planHeaderID;
		
		//$start_month = $this->project_system_start_date($project_id);
		$start_month = fy_start_date($month,$project_id);
		
		$end_month = date('n',strtotime($month));
		
		
		$fields['month_1_amount'] = date('n',strtotime($start_month));
		$fields['month_2_amount'] = date('n',strtotime('+1 month',strtotime($start_month)));
		$fields['month_3_amount'] = date('n',strtotime('+2 month',strtotime($start_month)));
		$fields['month_4_amount'] = date('n',strtotime('+3 month',strtotime($start_month)));
		$fields['month_5_amount'] = date('n',strtotime('+4 month',strtotime($start_month)));
		$fields['month_6_amount'] = date('n',strtotime('+5 month',strtotime($start_month)));
		$fields['month_7_amount'] = date('n',strtotime('+6 month',strtotime($start_month)));
		$fields['month_8_amount'] = date('n',strtotime('+7 month',strtotime($start_month)));
		$fields['month_9_amount'] = date('n',strtotime('+8 month',strtotime($start_month)));
		$fields['month_10_amount'] = date('n',strtotime('+9 month',strtotime($start_month)));
		$fields['month_11_amount'] = date('n',strtotime('+10 month',strtotime($start_month)));
		$fields['month_12_amount'] = date('n',strtotime('+11 month',strtotime($start_month)));	
		
		$total = 0;		
		
		$exp = $this->expense_accounts($rev_id);
		
		foreach($exp as $row):
					foreach($fields as $key=>$value):
							$this->db->where(array('planHeaderID'=>$planHeaderID,'AccNo'=>$row->AccNo));
							$total += $this->db->select_sum($key)->get('plansschedule')->row()->$key;
						if($value===$end_month){		
							break;
						}
					endforeach;
		endforeach;
		
		return $total;//$total;
	}
	
	function total_variance_per_revenue_vote($project_id,$fy,$rev_id,$month){
			
		$budget = $this->total_budget_to_date_per_revenue_vote($project_id,$fy,$rev_id,$month);
		
		$exp = $this->total_expense_to_date_per_revenue_vote($project_id,$rev_id,$month);
		
		return $budget - $exp;
	}
	
	function total_variance_percent_per_revenue_vote($project_id,$fy,$rev_id,$month){
		$budget_to_date = $this->total_budget_to_date_per_revenue_vote($project_id,$fy,$rev_id,$month);
		
// 		$exp_to_date = $this->total_expense_to_date_per_revenue_vote($project_id,$rev_id,$month);
		
		$variance_amount_per_revenue_vote = $this->total_variance_per_revenue_vote($project_id,$fy,$rev_id,$month);
		
		$percentage_variance = number_format(($variance_amount_per_revenue_vote / $budget_to_date) * 100,1);
		
		
		
		return $percentage_variance;
	}
	
	function budget_exists($project_id,$fy){
		$comment = 'no';   
		if($this->db->get_where('planheader',array('icpNo'=>$project_id,'fy'=>$fy))->num_rows()>0){
			$planHeaderID = $this->db->get_where('planheader',array('icpNo'=>$project_id,'fy'=>$fy))->row()->planHeaderID;
			if($this->db->get_where('plansschedule',array('planHeaderID'=>$planHeaderID))->num_rows()>0){
				$comment = 'yes';
			}
		}
		
		return $comment;
	}
	function bank_reconciled($project,$date){
		$rst =  $this->adjusted_bank_balance($date,$project)-$this->bank_balance($date,$project);

		return abs($rst);
	}
	
	function check_bank_statement($project,$date){
		// $path = 'uploads/bank_statements/'.$project.'/'.date('Y-m',strtotime($date));
		
		// return abs(count(glob($path."/*")));

		$projectsdetails_id = 0;

		$this->db->where(array('icpNo'=>$project));
		$projectsdetails_obj = $this->db->get('projectsdetails');

		if($projectsdetails_obj->num_rows() > 0){
			$projectsdetails_id = $projectsdetails_obj->row()->ID;
		}

		$statement_bal_id = 0;

		$this->db->where(array('icpNo'=>$project,'month'=>date('Y-m-t',strtotime($date))));
		$statement_bal_obj = $this->db->get('statementbal');

		if($statement_bal_obj->num_rows() > 0){
			$statement_bal_id = $statement_bal_obj->row()->balID;
		}

		$condition = array('fk_projectsdetails_id'=>$projectsdetails_id,'attachment_primary_id'=>$statement_bal_id);

		$this->db->where($condition);
		$count_of_attachment = $this->db->get('attachment')->num_rows();

		return $count_of_attachment;
	}
	
	function proof_of_cash($project,$date){
		$calc = round($this->total_months_closing_balance($project,date('Y-m-t',strtotime($date))),2) - round($this->total_cash(date('Y-m-t',strtotime($date)),$project),2);
		
		return abs($calc);
	}
	
	function editable($project,$date){

		$flag = 0;
				
		if($this->db->get_where('opfundsbalheader',array('icpNo'=>$project,'closureDate'=>date('Y-m-t',strtotime($date))))->num_rows()>0){
			$this->db->where(array('icpNo'=>$project,'closureDate'=>date('Y-m-t',strtotime($date))));
		
			$state = $this->db->get('opfundsbalheader')->row()->allowEdit;
			
				if($state === '1'){
					$flag = 1;
				}
		}
		
		return $flag;
	}
	
	function count_mfr_submitted($month){
		$submitted = '0';
		
		$opbal = $this->db->get_where('opfundsbalheader',array('closureDate'=>$month));
		
		if($opbal->num_rows()>0){
			$submitted = $opbal->num_rows(); 
		}
		
		return $submitted;		
	}
	
	// function mfr_submitted_datestamp($month){
		// $submitted = $month;
// 		
		// $opbal = $this->db->get_where('opfundsbalheader',array('closureDate'=>$month));
// 		
		// if($opbal->num_rows()>0){
			// $submitted = $opbal->row()->stmp; 
		// }
// 		
		// return $submitted;		
	// }
	
	function count_validated_mfr($month){
		$validated = '0';
		
		$opbal = $this->db->get_where('opfundsbalheader',array('closureDate'=>$month,'allowEdit'=>'0'));
		
		if($opbal->num_rows()>0){
			$validated = $opbal->num_rows(); 
		}
		
		return $validated;				
	}
	
	function mfr_submitted($project,$month,$is_submitted = ''){
		$submitted = '0';
		
		if($is_submitted !== ''){
			$this->db->where(array('icpNo'=>$project,'closureDate'=>$month,'submitted'=>1));
		}else{
			$this->db->where(array('icpNo'=>$project,'closureDate'=>$month));
		}
		
		$opbal = $this->db->get('opfundsbalheader');
		
		if($opbal->num_rows()>0){
			$submitted = '1';
		}
		
		return $submitted;
	}

	function mfr_validated($project,$month){
		$validated = '0';
		
		$opbal = $this->db->get_where('opfundsbalheader',array('icpNo'=>$project,'closureDate'=>$month,'allowEdit'=>'0'));
		
		if($opbal->num_rows()>0){
			$validated = '1';
		}
		
		return $validated;
	}
	
	/**Compassion's Local Methods**/
	
	function civs(){
		
	}
	
	
	public function month_income($project_id,$date,$rev_account){
				//Total Income
			$cond_income = "TDate>='".fy_start_date($date)."' AND TDate<='".date('Y-m-t',strtotime($date))."' AND icpNo='".$project_id."' AND VType = 'CR' AND AccNo=".$rev_account;
			$income = $this->db->select_sum('Cost')->where($cond_income)->get('voucher_body')->row()->Cost;	
			
			return $income;
	}
	
	public function admin_expenses($project_id,$date,$rev_account){
		
		$parentAccID = $this->db->get_where('accounts',array('AccNo'=>$rev_account))->row()->accID;
		
		$admin_acc = $this->db->get_where('accounts',array('parentAccID'=>$parentAccID,'is_admin'=>1))->result_object();
		
		$admin_exp = 0;
		
		foreach($admin_acc as $key => $value):
		
			$cond_admin_exp = "TDate>='".fy_start_date($date)."' AND TDate<='".date('Y-m-t',strtotime($date))."' AND icpNo='".$project_id."' AND VType IN('PC','CHQ') AND AccNo='".$value->AccNo."'";
			$admin_exp += $this->db->select_sum('Cost')->where($cond_admin_exp)->get('voucher_body')->row()->Cost;
		
		endforeach;
		
		return $admin_exp;
	}	
	
	public function operating_ratio($project_id,$date,$rev_account){
		
		//Total Income
		$income = $this->month_income($project_id,$date, $rev_account);
		//$income = months_incomes_per_revenue_vote($project_id,$rev_account,$date);
		
		//Total Admin Expenses
		$admin_exp = $this->admin_expenses($project_id,$date, $rev_account);
		
		$or = 1;
		
		if($income!==0){
			$or = @($admin_exp/$income);
		}
		
		
		return $or;
	}
	
	public function accumulated_fund_ratio($project_id,$rev_acc='100',$month){
		
		//$support_funds_balance = $this->months_closing_fund_balance_per_revenue_vote($project_id,$rev_acc,$month);
		 
		 
		//$income =  $this->month_income($project_id,$date,$rev_acc);
		//$income = months_incomes_per_revenue_vote($project_id,$rev_acc,$month);
		 
		// return @number_format($support_funds_balance/$income,2);
		return @($this->finance_model->months_incomes_per_revenue_vote($project_id,$rev_acc,$month)/$this->finance_model->months_closing_fund_balance_per_revenue_vote($project_id,$rev_acc,$month));
	}
	
/** Finance Dashbaord Model Methods - Begin **/
	
	//General Methods
	
	private function db_cache_on(){
		return $this->config->item('db_cache_on') == true?$this->db->cache_on():null;	
	}
	
	private function db_cache_off(){
		return $this->config->item('db_cache_on') == true?$this->db->cache_off():null;
	}
	
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

		return $total_pc_data;
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
			$this->db_cache_on();
			$db_call = 'CALL get_max_pc_withdrawal_transactions("'.date('Y-m-01',strtotime($month)).'","'.date('Y-m-t',strtotime($month)).'","'.$limit_type.'")';

			$pc_withdrawal_result = $this->db->query($db_call)->result_array();
			$this->db_cache_off();
	
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
		$this->db_cache_on();
		$query_conditon = "voucher_header.TDate BETWEEN '".date('Y-m-01',strtotime($month))."' AND '".date("Y-m-t",strtotime($month))."' AND voucher_header.VType='CR'";

		$this->db->select_sum('voucher_body.Cost');
		$this->db->select(array('voucher_header.icpNo'));
		$this->db->where($query_conditon);
		$this->db->where(array('ci_income='=>1));
		$this->db->group_by(array('voucher_header.icpNo'));
		$this->db->join('voucher_body','voucher_body.hID=voucher_header.hID');
		$this->db->join('accounts','accounts.AccNo = voucher_body.AccNo');
		$cash_received_in_month = $this->db->get('voucher_header')->result_object();
		$this->db_cache_off();

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

	public function prod_fcps_with_risk_model() {
		$this->benchmark->mark('prod_fcps_with_risk_model_start');
		$fcp_array = array();

		$data = $this -> db -> get_where($this -> table_prefix . 'projectsdetails', array('status='=>1)) -> result_array();

		foreach ($data as $fcp) {

			$fcp_array[$fcp['ID']]['fcp_id'] = $fcp['icpNo'];
			$fcp_array[$fcp['ID']]['risk'] = $fcp['risk'];
		}

		return $fcp_array;
		$this->benchmark->mark('prod_fcps_with_risk_model_end');
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
		$this->db_cache_on();
		$statement_bank_balance = $this -> db -> get_where($this -> table_prefix . 'view_funds_statement_balance', array('closure_date' => $month)) -> result_array();
		$this->db_cache_off();
		$this->benchmark->mark('prod_statement_bank_balance_data_model_end');
		return $statement_bank_balance;
	}

	private function prod_book_bank_cash_balance_data_model($month) {
		$this->benchmark->mark('prod_book_bank_cash_balance_data_model_start');
		$this->db_cache_on();
		$bank_cash_balance_data = $this -> db -> get_where($this -> table_prefix . 'view_book_bank_balance', array('closure_date' => $month)) -> result_array();
		$this->db_cache_off();
		$this->benchmark->mark('prod_book_bank_cash_balance_data_model_end');
		return $bank_cash_balance_data;	
	}

	//We will have to pass month aurgumet in prod models
	private function prod_mfr_submission_data_model($month) {
		$this->benchmark->mark('prod_mfr_submission_data_model_start');
		$this->db_cache_on();
		$mfr_submission_data = $this -> db -> get_where($this -> table_prefix . 'view_opening_funds_balance', array('closure_date' => $month)) -> result_array();
		$this->db_cache_off();
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
		
				$this->db_cache_on();
		
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
		
				$this->db_cache_off();
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

		$get_uncleared_transactions = $this -> uncleared_transactions['CR'];

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

		$get_uncleared_transactions = $this -> uncleared_transactions['CHQ'];

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

		$this->db_cache_on();

		$this -> db -> select_sum('voucher_body.cost');
		$this -> db -> select(array('voucher_header.icpNo', 'voucher_header.vtype'));
		$this -> db -> join("voucher_body", "voucher_body.hid=voucher_header.hid");
		$this -> db -> group_by(array('voucher_header.icpNo', 'voucher_header.vtype'));
		$this -> db -> where('voucher_header.vtype', $vtype);
		$this -> db -> where('voucher_header.tdate >= ', $first_day_of_month);
		$this -> db -> where('voucher_header.tdate <= ', $last_day_of_month);

		$total_pc_or_chqs = $this -> db -> get("voucher_header") -> result_array();

		$this->db_cache_off();
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

		$this->db_cache_on();

		$this->db->select(array('icpNo'));
		$this->db->select_sum('totals');
		$this -> db -> group_by(array('icpNo','vtype'));
		$this -> db -> where('TDate >= ', $first_day_of_month);
		$this -> db -> where('TDate <= ', $last_day_of_month);
		$this -> db -> where('VType',$vtype);
		$this->db->where('chqState =',0);
		$this->db->where('DATEDIFF(NOW(), TDate) >',$this->config->item('allowed_uncleared_days'));
		
		$count_of_cr_and_chq = $this -> db -> get("voucher_header") -> result_array();

       $this->db_cache_off();
		$this->benchmark->mark('calculate_uncleared_cash_recieved_and_chqs_end');
		return $count_of_cr_and_chq;

	}
	
	
	/** Finance Dashbaord Model Methods - End **/


	function get_uploaded_bank_statement($reporting_month,$fcp_id = ''){

		$fcp_id = $fcp_id == ''?$this->session->center_id:$fcp_id;

		$statementbal_id = $this->get_bank_statement_id($fcp_id,$reporting_month);

		$this->db->where([
			'item_name'=>'bank_statement',
			'attachment_primary_id' => $statementbal_id
		]);
		
		$attachment_obj = $this->db->get('attachment');

		$attachments = [];

		if($attachment_obj->num_rows() > 0){
			$attachments = $attachment_obj->result_array();
		}

		return $attachments;
	}


	function get_bank_statement_id($fcp_id,$reporting_month){

		$this->db->where(['month'=>$reporting_month,'icpNo'=>$fcp_id]);
		$statementbal_obj = $this->db->get('statementbal');

		$statementbal_id = 0;

		if($statementbal_obj->num_rows() == 0){
			// Insert and Get Id
			$data['month'] = $reporting_month;
			$data['statementDate'] = $reporting_month;
			$data['actualDate'] = $reporting_month;
			$data['icpNo'] = $fcp_id;
			$data['amount'] = 0;

			$this->db->insert('statementbal',$data);

			$statementbal_id = $this->db->insert_id();
		}else{
			// Get Id
			$this->db->where(['month'=>$reporting_month,'icpNo'=>$fcp_id]);
			$statementbal_id = $this->db->get('statementbal')->row()->balID;
		}

		return $statementbal_id;
	}

	function uploaded_bank_statements($fcp_id,$tym){

		$project_id = 0;

        $this->db->where(array('icpNo'=>$fcp_id));
		$project_obj = $this->db->get('projectsdetails');

		if($project_obj->num_rows() > 0){
			$project_id = $project_obj->row()->ID;
		}
            
        $month = date('Y-m',$tym);

		$document_type = 'bank_statements';
        
        $url = 'uploads/'.$document_type.'/'.$fcp_id.'/'.$month;

		$statementbal_id = 0;

		$this->db->where(array('month'=>date('Y-m-t',$tym),'icpNo'=>$fcp_id));
		$statementbal_obj = $this->db->get('statementbal');

		if($statementbal_obj->num_rows() > 0){
			$statementbal_id = $statementbal_obj->row()->balID;
		}

        $this->db->select(array('attachment_id','attachment_name','attachment_url','attachment_created_date','attachment_size'));

        $this->db->where(
            array(
                'item_name'=>$document_type,
                'fk_projectsdetails_id'=>$project_id,
                'attachment_primary_id'=>$statementbal_id,
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

	function get_projectsdetails(){

		$this->db->select(array('ID','icpNo'));
		$this->db->where(array('status'=>1));
		$projectsdetails = $this->db->get('projectsdetails')->result_array();

		$ids = array_column($projectsdetails,'ID');
		$fcp_number = array_column($projectsdetails,'icpNo');

		return array_combine($fcp_number,$ids);
	}

	function get_statement_balance_ids(){

		$this->db->select(array('balID','icpNo'));
		$this->db->select("DATE_FORMAT(month, '%Y-%m') AS month", FALSE);
		$statementbal_array = $this->db->get('statementbal')->result_array();

		$statementbal_ids = [];

		foreach($statementbal_array as $statementbal){
			$statementbal_ids[$statementbal['icpNo']][$statementbal['month']] = $statementbal['balID'];
		}

		return $statementbal_ids;
	}

	function claiming_fcp_projectsdetails(){
		
		$this->db->select(array('rec','ID'));
		$this->db->join('projectsdetails','projectsdetails.icpNo=claims.proNo');
		$claims_obj = $this->db->get('claims')->result_array();

		$project_ids = array_column($claims_obj,'ID');
		$claim_ids = array_column($claims_obj,'rec');

		return array_combine($claim_ids,$project_ids);
	}

	function insert_attachment_records_from_local_file_system(){

		$projectsdetails = $this->get_projectsdetails();

		$bank_statements = $this->get_statement_balance_ids();

		$claiming_fcp_projectsdetails = $this->claiming_fcp_projectsdetails();

		$attachment_insert_array = attachment_insert_array($projectsdetails,$bank_statements,$claiming_fcp_projectsdetails);

		foreach($attachment_insert_array as $attachment_record){
			$this->db->where($attachment_record);
			$attachment_count = $this->db->get('attachment')->num_rows();

			if($attachment_count == 0){
				$attachment_record['attachment_is_historical'] = 1;
				$this->db->insert('attachment',$attachment_record);
			}
		}

		return $attachment_insert_array;
	}


	function list_month_vouchers_for_fcp($fcp_id,$period_time_stamp,$additional_condition_array = []){

		$end_period_date = date("Y-m-t",$period_time_stamp);
		$start_period_date = date("Y-m-01",$period_time_stamp);

		$list_month_vouchers_for_fcp = [];

		$this->db->select(array('voucher_header.hID as voucher_id','voucher_header.TDate as voucher_date','voucher_header.VNumber as voucher_number'));
		$this->db->select(array('voucher_header.Payee as payee','voucher_header.VType as voucher_type'));
		$this->db->select(array('voucher_header.ChqNo as cheque_number','voucher_header.ChqState as clear_state'));
		$this->db->select(array('voucher_header.clrMonth as clear_month','voucher_header.editable as is_editable'));
		$this->db->select(array('voucher_header.TDescription as description'));
		$this->db->select(array('accounts.AccNo as account_number','accounts.AccText as account_code',
		'accounts.AccName as account_name','accounts.AccGrp as account_group'));
		$this->db->select_sum('Cost');
		$this->db->where(array('voucher_header.TDate>='=>$start_period_date,'voucher_header.TDate<='=>$end_period_date));
		$this->db->where(array('voucher_header.icpNo'=>$fcp_id));

		if(!empty($additional_condition_array)){
			$this->db->where($additional_condition_array);
		}

		$this->db->join('voucher_header','voucher_header.hID=voucher_body.hID');
		$this->db->join('accounts','accounts.AccNo=voucher_body.AccNo');
		$this->db->group_by(array('voucher_header.VNumber','voucher_body.AccNo'));
		$vouchers_obj = $this->db->get('voucher_body');

		if($vouchers_obj->num_rows() > 0){
			$list_month_vouchers_for_fcp = $vouchers_obj->result_array();
		}

		return $list_month_vouchers_for_fcp;
	}


	function list_to_date_vouchers_for_fcp($fcp_id,$start_period_date,$end_period_date,$additional_condition_array = []){


		$list_to_date_vouchers_for_fcp = [];

		$this->db->select(array("LAST_DAY(voucher_header.TDate) as voucher_date",
		'accounts.AccNo as account_number','accounts.AccText as account_code',
		'AccGrp as account_group','accID as account_id','parentAccID as parent_account_id'));
		$this->db->select_sum('Cost');
		$this->db->where(array('voucher_header.TDate>='=>$start_period_date,'voucher_header.TDate<='=>$end_period_date));
		$this->db->where(array('voucher_header.icpNo'=>$fcp_id));

		if(!empty($additional_condition_array)){
			$this->db->where($additional_condition_array);
		}

		$this->db->join('voucher_header','voucher_header.hID=voucher_body.hID');
		$this->db->join('accounts','accounts.AccNo=voucher_body.AccNo');
		$this->db->group_by(array("LAST_DAY(voucher_header.TDate)",'voucher_body.AccNo'));
		$vouchers_obj = $this->db->get('voucher_body');

		if($vouchers_obj->num_rows() > 0){
			$list_to_date_vouchers_for_fcp = $vouchers_obj->result_array();
		}

		return $list_to_date_vouchers_for_fcp;
	}

	function list_to_date_vouchers_for_fund_balance($fcp_id,$start_period_date,$end_period_date,$additional_condition_array = []){

		$list_to_date_vouchers_for_fcp = [];
		$join_query = "SELECT accID as join_account_id, AccNo as join_account_number, AccText as join_account_code, AccName as join_account_name FROM accounts WHERE AccGrp = 1";

		$this->db->select(array("LAST_DAY(voucher_header.TDate) as voucher_date",
		'accounts.AccNo as account_number','accounts.AccText as account_code','AccName as account_name',
		'AccGrp as account_group','accID as account_id','parentAccID as parent_account_id',
		'join_account_number','join_account_code','join_account_name'));

		$this->db->select_sum('Cost');
		$this->db->where(array('voucher_header.TDate>='=>$start_period_date,'voucher_header.TDate<='=>$end_period_date));
		$this->db->where(array('voucher_header.icpNo'=>$fcp_id));

		if(!empty($additional_condition_array)){
			$this->db->where($additional_condition_array);
		}

		$this->db->join('voucher_header','voucher_header.hID=voucher_body.hID');
		$this->db->join('accounts','accounts.AccNo=voucher_body.AccNo');
		$this->db->join('('.$join_query.') as join_accounts','join_accounts.join_account_id=accounts.parentAccID','LEFT',NULL);
		$this->db->group_by(array("LAST_DAY(voucher_header.TDate)",'voucher_body.AccNo'));
		$vouchers_obj = $this->db->get('voucher_body');

		if($vouchers_obj->num_rows() > 0){
			$list_to_date_vouchers_for_fcp = $vouchers_obj->result_array();
		}

		return $list_to_date_vouchers_for_fcp;
	}

	function previous_month_closing_fund_balance($fcp_id,$month){

		$month = date('Y-m-t',strtotime('last day of previous month',strtotime($month)));

		$this->db->join("opfundsbalheader","opfundsbalheader.balHdID=opfundsbal.balHdID");
		$this->db->join("accounts","accounts.AccNo=opfundsbal.funds");
		$this->db->select(array("accID as account_id","AccNo as account_number",
		'AccText as account_code','AccName as account_name',"amount"));
		$this->db->where(array("icpNo"=>$fcp_id,"closureDate"=>$month));
		$revenue_balance_obj = $this->db->get("opfundsbal");

		$revenue_balance = [];

		if($revenue_balance_obj->num_rows() > 0){
			$revenue_balance = $revenue_balance_obj->result_array();
		}

		return $revenue_balance;
	}

	function fund_balance_report_grid($fcp_id,$start_period_date,$end_period_date){
		$vouchers = $this->list_to_date_vouchers_for_fund_balance($fcp_id,$start_period_date,$end_period_date);
		$month_closing_fund_balance = $this->previous_month_closing_fund_balance($fcp_id,$end_period_date);

		$fund_balance_report = [];

		foreach($month_closing_fund_balance as $opening_balance){
			$fund_balance_report[$opening_balance['account_number']]['opening_balance'] = $opening_balance['amount'];
			$fund_balance_report[$opening_balance['account_number']]['income_account'] = ['account_code' => $opening_balance['account_code'],'account_name'=>$opening_balance['account_name'],'account_number'=>$opening_balance['account_number']];
		}

		foreach($vouchers as $voucher){

			// Creating an income account elements in the array
			if(!isset($fund_balance_report[$voucher['join_account_number']]['income_account']) && $voucher['join_account_number'] != ''){
				$fund_balance_report[$voucher['join_account_number']]['income_account'] = ['account_code' => $voucher['join_account_code'],'account_name'=>$voucher['join_account_name'],'account_number'=>$voucher['join_account_number']];
			}elseif($voucher['account_group'] == 1){
				$fund_balance_report[$voucher['account_number']]['income_account'] = ['account_code' => $voucher['account_code'],'account_name'=>$voucher['account_name'],'account_number'=>$voucher['account_number']];
			}

			if($voucher['account_group'] == 0){
				if(isset($fund_balance_report[$voucher['join_account_number']]['expense'])){
					$fund_balance_report[$voucher['join_account_number']]['expense'] += $voucher['Cost'];
				}else{
					$fund_balance_report[$voucher['join_account_number']]['expense'] = $voucher['Cost']; 
				}
			}elseif($voucher['account_group'] == 1){
				if(isset($fund_balance_report[$voucher['account_number']]['income'])){
					$fund_balance_report[$voucher['account_number']]['income'] += $voucher['Cost']; 
				}else{
					$fund_balance_report[$voucher['account_number']]['income']  = $voucher['Cost']; 
				}
			}

		}

		$fund_balance_report['utilized_income_accounts'] = array_column($fund_balance_report,'income_account');

		return $fund_balance_report;
	}

	function approved_fcp_year_budget_to_date($fy,$fcp_number,$income_account_id){

		$this->db->select(array('plansschedule.AccNo as account_number'));
		$this->db->select(array('month_1_amount','month_2_amount','month_3_amount','month_4_amount'));
		$this->db->select(array('month_5_amount','month_6_amount','month_7_amount','month_8_amount'));
		$this->db->select(array('month_9_amount','month_10_amount','month_11_amount','month_12_amount'));
		$this->db->where(array('fy'=>$fy,'icpNo'=>$fcp_number,'parentAccID'=>$income_account_id,'approved'=>2));
		$this->db->join('planheader','planheader.planHeaderID=plansschedule.planHeaderID');
		$this->db->join('accounts','accounts.AccNo=plansschedule.AccNo');
		$budget_items_obj = $this->db->get('plansschedule');

		$budget_items = [];

		if($budget_items_obj->num_rows() > 0){
			$budget_items = $budget_items_obj->result_array();
		}

		return $budget_items;
	}

	function fcp_year_budget_to_date_for_all_income_accounts($fy,$fcp_number){

		$this->db->select(array('income_accounts.income_account_number as income_account_number',
		'accounts.AccText as expense_account_code','plansschedule.AccNo as expense_account_number'));
		$this->db->select(array('month_1_amount','month_2_amount','month_3_amount','month_4_amount'));
		$this->db->select(array('month_5_amount','month_6_amount','month_7_amount','month_8_amount'));
		$this->db->select(array('month_9_amount','month_10_amount','month_11_amount','month_12_amount'));
		$this->db->where(array('fy'=>$fy,'icpNo'=>$fcp_number));
		$this->db->join('planheader','planheader.planHeaderID=plansschedule.planHeaderID');
		$this->db->join('accounts','accounts.AccNo=plansschedule.AccNo');
		$this->db->join('(select accID as account_id, AccNo as income_account_number,AccText as income_account_code FROM accounts WHERE AccGrp = 1) as income_accounts','income_accounts.account_id=accounts.parentAccID','LEFT',NULL);
		$budget_items_obj = $this->db->get('plansschedule');

		$budget_items = [];

		if($budget_items_obj->num_rows() > 0){
			$budget_items = $budget_items_obj->result_array();
		}

		return $budget_items;
	}

	function budget_spread_grid_by_income_accounts($fy,$fcp_number, $month = ''){
		
		$month_number = $month != '' ? date('n',strtotime($month)) : 0;

		$budget_items = $this->fcp_year_budget_to_date_for_all_income_accounts($fy,$fcp_number);
		$budget_spread = [];

		foreach($budget_items as $spread){
			$income_account_number = array_shift($spread);
			$expense_account_number = array_shift($spread);
			$budget_spread[$income_account_number][$expense_account_number][] = $spread; 
		}

		$sum_budget_spread = [];

		foreach($budget_spread as $income_account_number => $expense_sum_spread){

			foreach($expense_sum_spread as $expense_account_number => $sum_spread){
				$sum_array = [];

				$month_range = order_of_months_in_fy();//[7,8,9,10,11,12,1,2,3,4,5,6];

				$cnt = 1;
				foreach($month_range as $month){

					$sum_array[$month] = array_sum(array_column($sum_spread,'month_'.$cnt.'_amount'));
					
					$cnt++;
					
					if($month_number > 0 && $month_number == $month) break;
				}

				$sum_budget_spread[$income_account_number][$expense_account_number] = $sum_array;
				$sum_budget_spread[$income_account_number][$expense_account_number]['total_cost'] = array_sum($sum_array);
			}

		}

		return $sum_budget_spread;
	}

	function budget_spread_grid($fy,$fcp_number,$income_account_id,$month = ''){

		$month_number = $month != '' ? date('n',strtotime($month)) : 0;

		$budget_spread = [];
		$budget_spread_grid = $this->approved_fcp_year_budget_to_date($fy,$fcp_number,$income_account_id);

		foreach($budget_spread_grid as $spread){
			$account_number = array_shift($spread);
			$budget_spread[$account_number][] = $spread; 
		}

		$sum_budget_spread = [];

		foreach($budget_spread as $account_number => $sum_spread){

			$sum_array = [];
 
			$month_range = order_of_months_in_fy();//[7,8,9,10,11,12,1,2,3,4,5,6];

			$cnt = 1;

			foreach($month_range as $month){

				$sum_array[$month] = array_sum(array_column($sum_spread,'month_'.$cnt.'_amount'));
				
				$cnt++;
				
				if($month_number > 0 && $month_number == $month) break;
			}

			$sum_budget_spread[$account_number] = $sum_array;
			$sum_budget_spread[$account_number]['total_cost'] = array_sum($sum_array);
		}

		return $sum_budget_spread;
	}


	
}
