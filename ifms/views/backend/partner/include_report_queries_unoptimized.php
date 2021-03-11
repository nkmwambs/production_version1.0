<?php

/** Input Parameters **/
//include "include_inputs.php";
/** Input Parameters **/
$month_start = date("Y-m-01",strtotime($month));
$month_end  = $month;
$last_month = date("Y-m-t",strtotime("last day of previous month",strtotime($month)));
$icpNo = $this->session->center_id;
/**Get All accounting vote heads **/
$all_vote_heads = $this->db->get_where("accounts")->result_array();
				
/** All Revenue Accounts **/
$this->db->where("AccGrp",1);
$this->db->select(array("accID","AccNo","AccText","AccName"));
$revenue_accounts = $this->db->get("accounts")->result_array();
$revenue_accounts_acc_id = array_column($revenue_accounts,"accID");
$revenue_accounts_with_acc_id_keys =  array_combine($revenue_accounts_acc_id, $revenue_accounts);

				
/** Last Month Closing Revenue Balances **/				
$this->db->join("opfundsbalheader","opfundsbalheader.balHdID=opfundsbal.balHdID");
$this->db->join("accounts","accounts.AccNo=opfundsbal.funds");
$this->db->select(array("accID","AccNo","amount"));
$this->db->where(array("icpNo"=>$icpNo,"closureDate"=>$last_month));
$previous_month_revenue_balance = $this->db->get("opfundsbal")->result_array();
				
$last_month_acc_id = array_column($previous_month_revenue_balance, "accID");
$last_month_balances_with_acc_id_keys =  array_combine($last_month_acc_id, $previous_month_revenue_balance);
				
/** All Months Transactions Grouped By VType **/
$this->db->where(array("icpNo"=>$icpNo,"TDate>="=>$month_start,"TDate<="=>$month_end));
$this->db->select(array("voucher_body.AccNo","accID","parentAccID","AccGrp","VType"));
$this->db->select_sum("Cost");
$this->db->group_by("voucher_body.VType");
$this->db->join("accounts","accounts.AccNo=voucher_body.AccNo");
$months_transactions_by_vtype = $this->db->get('voucher_body')->result_array();
				
/** All Months Transactions Grouped By AccNo **/
$this->db->where(array("icpNo"=>$icpNo,"TDate>="=>$month_start,"TDate<="=>$month_end));
$this->db->select(array("voucher_body.AccNo","accID","parentAccID","AccGrp","VType"));
$this->db->select_sum("Cost");
$this->db->group_by("voucher_body.AccNo");
$this->db->join("accounts","accounts.AccNo=voucher_body.AccNo");
$months_transactions_by_acc = $this->db->get('voucher_body')->result_array();
				
/** Filter Income Transactions for the month **/
function get_income_transactions($array){
	if($array['AccGrp'] == 1){
		return $array;
	}
}
				
$income_transactions = array_filter($months_transactions_by_acc,"get_income_transactions");
				
$month_income_acc_id = array_column($income_transactions, "accID");
$month_income_with_acc_id_keys =  array_combine($month_income_acc_id, $income_transactions);
		
				
/** Filter Expense Transaction for the month **/
function get_expense_transactions($array){
	if($array['AccGrp'] == 0){
			return $array;
	}
}
				
$expense_transactions = array_filter($months_transactions_by_acc,"get_expense_transactions");				
$expense_transactions_with_parent_id_keys = array();
				
foreach($revenue_accounts_acc_id as $parent_id){
	$cost = 0;	
		foreach ($expense_transactions as $expense) {
						
			if($expense['parentAccID'] == $parent_id){
					$cost +=  $expense['Cost'];
			}	
							
		}
	$expense_transactions_with_parent_id_keys[$parent_id]['Cost'] = $cost;
	$expense_transactions_with_parent_id_keys[$parent_id]['accID'] = $parent_id;
}
				
				
/** Previous Months Cash Balances **/
$last_month_cash_balance = "";
$last_month = date("Y-m-t",strtotime("last day of previous month",strtotime($month)));
$this->db->where(array("icpNo"=>$this->session->center_id,"month"=>$last_month));
$this->db->select(array("accNo","amount"));
$last_month_cash_balance_obj = $this->db->get_where("cashbal");
if($last_month_cash_balance_obj->num_rows()>0){
	$last_month_cash_balance = $last_month_cash_balance_obj->result_array();
}else{
	$last_month_cash_balance = array(
	array('accNo'=>'PC',"amount"=>0),
	array('accNo'=>'BC',"amount"=>0)
);
}

$cash_balance_columns = array_column($last_month_cash_balance, "accNo");
$last_month_cash_balance = array_combine($cash_balance_columns, $last_month_cash_balance);


/** Filter Bank Income : All CR  & PCR **/
function get_bank_income_transactions($array){
	if($array['VType'] == "CR" || $array['VType'] == "PCR"){
		return $array;
	}
}

$bank_income_transactions = array_filter($months_transactions_by_vtype,"get_bank_income_transactions");


/** Filter Bank Expense : All CHQ  & BCHG **/
function get_bank_expense_transactions($array){
	if($array['VType'] == "CHQ" || $array['VType'] == "BCHG"){
		return $array;
	}
}

$bank_expense_transactions = array_filter($months_transactions_by_vtype,"get_bank_expense_transactions");

/** Filter Petty Cash Income **/
function get_petty_income_transactions($array){
	if($array['AccNo'] == "2000" || $array['AccNo'] == "2001"){
		return $array;
	}
}

$petty_income_transactions = array_filter($months_transactions_by_acc,"get_petty_income_transactions");


/** Filter Petty Cash Expense **/
function get_petty_expense_transactions($array){
	if($array['VType'] == "PC" || $array['AccNo'] == "PCR"){
		return $array;
	}
}

$petty_expense_transactions = array_filter($months_transactions_by_vtype,"get_petty_expense_transactions");


/** List months Outstanding Cheques **/

$this->db->where(array("ChqState"=>'0',"voucher_header.icpNo"=>$icpNo,"voucher_header.VType"=>"CHQ",));
$this->db->or_where(array("clrMonth>"=>$month_end));
$this->db->select(array("voucher_header.TDate","voucher_header.ChqNo","TDescription","voucher_header.VNumber"));
$this->db->select_sum("Cost");
$this->db->group_by("VNumber");
$this->db->join("voucher_header","voucher_header.hID=voucher_body.hID");
$month_outstanding_cheques = $this->db->get("voucher_body")->result_array();		

/** List months In Transit Deposit **/

$this->db->where(array("ChqState"=>'0',"voucher_header.icpNo"=>$icpNo,"voucher_header.VType"=>"CR",));
$this->db->or_where(array("clrMonth>"=>$month_end));
$this->db->select(array("voucher_header.TDate","voucher_header.ChqNo","TDescription","voucher_header.VNumber"));
$this->db->select_sum("Cost");
$this->db->group_by("VNumber");
$this->db->join("voucher_header","voucher_header.hID=voucher_body.hID");
$month_in_transit_deposit = $this->db->get("voucher_body")->result_array();			

/**Computations Cash Balance Grid**/

//Opening Balance for the month
$opening_bank_balance_for_the_month = $last_month_cash_balance['BC']['amount'];
$opening_petty_balance_for_the_month = $last_month_cash_balance['PC']['amount'];

//Month Bank
$month_bank_income = array_sum(array_column($bank_income_transactions, "Cost"));
$month_bank_expense = array_sum(array_column($bank_expense_transactions, "Cost"));

//Month Petty
$month_petty_income = array_sum(array_column($petty_income_transactions, "Cost"));
$month_petty_expense = array_sum(array_column($petty_expense_transactions, "Cost"));

//Closing Balance for the month
$closing_bank_balance_for_the_month = $opening_bank_balance_for_the_month+$month_bank_income-$month_bank_expense;
$closing_petty_balance_for_the_month = $opening_petty_balance_for_the_month+$month_petty_income-$month_petty_expense;

$proof_of_cash_grid = array(
	"BC"=>array(
		'ob'=>$opening_bank_balance_for_the_month,
		"mi"=>$month_bank_income,
		"me"=>$month_bank_expense,
		"cb"=>$closing_bank_balance_for_the_month
		),
	"PC"=>array(
		"ob"=>$opening_petty_balance_for_the_month,
		"mi"=>$month_petty_income,
		"me"=>$month_petty_expense,
		"cb"=>$closing_petty_balance_for_the_month
	)
);

		

				
//Construct Report Grid: Add Opening Balances
				
$report_grid = array();/**4 columned grid: ob,mi,me,cb**/
				
foreach($revenue_accounts_acc_id as $revenue_acc_id){
	$ob = array_key_exists($revenue_acc_id, $last_month_balances_with_acc_id_keys)?$last_month_balances_with_acc_id_keys[$revenue_acc_id]['amount']:0;
	$mi = array_key_exists($revenue_acc_id, $month_income_with_acc_id_keys)?$month_income_with_acc_id_keys[$revenue_acc_id]['Cost']:0;
	$me = array_key_exists($revenue_acc_id, $expense_transactions_with_parent_id_keys)?$expense_transactions_with_parent_id_keys[$revenue_acc_id]['Cost']:0;
	$cb = $ob+$mi-$me;
				
	$report_grid[$revenue_acc_id]['ac'] = $revenue_acc_id;
	$report_grid[$revenue_acc_id]['ob'] = $ob;
	$report_grid[$revenue_acc_id]['mi'] = $mi;
	$report_grid[$revenue_acc_id]['me'] = $me;
	$report_grid[$revenue_acc_id]['cb'] = $cb;
}
				
//Removing Empty Rows from grid
				
$removed_empty_rows_in_grid = array();
			
foreach($report_grid as $row_key=>$row_value){
	unset($row_value['ac']);
	if(array_sum($row_value)!== 0){
		$removed_empty_rows_in_grid[$row_key] = $row_value;
	}
}
				
