<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DCT_model extends CI_Model {
	
	function __construct()
    {
        parent::__construct();
	}
	
	function covid19_data_query($reporting_month,$group_report_by='beneficiary'){

		$start_date=date('Y-m-d',strtotime($reporting_month));
		$end_date=date('Y-m-t',strtotime($reporting_month));;

		$group_by_array=array('clusters.clustername','accounts.accno','voucher_body.fk_support_mode_id');

		$this->db->select(array('clusters.clustername','accounts.acctext','support_mode.support_mode_name'));

		if($group_report_by=='household'){
			
			$group_by_array=array('voucher_body.fk_voucher_item_type_id','clusters.clustername','accounts.accno','voucher_body.fk_support_mode_id');
			$this->db->select('sum(voucher_body.qty) as '.$group_report_by);
			$this->db->where(array('voucher_body.fk_voucher_item_type_id'=>2));
		}
		else if($group_report_by=='fcp'){
			
			  $this->db->select('count(distinct voucher_body.icpno) as '.$group_report_by);
		}
		else if($group_report_by=='amount'){
			$this->db->select('sum(voucher_body.cost) as '.$group_report_by);
			$this->db->where(array('voucher_body.cost > '=> 0)); // Removed reversed figures
		}else if($group_report_by=='beneficiary_household'){
		
			//$group_by_array=array('voucher_body.fk_voucher_item_type_id','clusters.clustername','accounts.accno','voucher_body.fk_support_mode_id');
			$group_by_array=array('voucher_body.fk_support_mode_id','accounts.accno','clusters.clustername');
			$this->db->select('sum(voucher_body.qty) as '.$group_report_by);
			$this->db->where_in('voucher_body.fk_voucher_item_type_id',[1,2]);

		}else{
			  $group_by_array=array('voucher_body.fk_voucher_item_type_id','clusters.clustername','accounts.accno','voucher_body.fk_support_mode_id');
			  $this->db->select('sum(voucher_body.qty) as '.$group_report_by);
			  $this->db->where(array('voucher_body.fk_voucher_item_type_id'=>1));
		}
		
		$this->db->join('projectsdetails', 'projectsdetails.icpno = voucher_body.icpno');
		$this->db->join('clusters','clusters.clusters_id = projectsdetails.cluster_id');
		$this->db->join('accounts','accounts.accno=voucher_body.accno');
		$this->db->join('support_mode','support_mode.support_mode_id=voucher_body.fk_support_mode_id');
		$this->db->join('voucher_item_type','voucher_item_type.voucher_item_type_id=voucher_body.fk_voucher_item_type_id');

		$this->db->group_by($group_by_array);
		
		$this->db->where(array('TDate >='=>$start_date,'TDate <='=>$end_date,'AccGrp'=>0, 'voucher_body.fk_support_mode_id<>'=>0, 'voucher_body.fk_voucher_item_type_id<>'=>0));
		//$this->db->where(array('voucher_body.icpno'=>'KE202'));
		//$this->db->where_in('voucher_body.icpno',array('KE202','KE206','KE200'));

		//$this->db->limit(5);
		$covid19_report_data=$this->db->get('voucher_body')->result_array();

		//print_r($covid19_report_data); exit();
		return $covid19_report_data;
	}

	function fcp_list($hierarchy_id = 0){

		if($this->session->logged_user_level == 2){// 2 = PF
			$this->db->where(array('clusters.clusterName'=>$this->session->cluster));
		}elseif($this->session->logged_user_level == 4){ // 4 = MOP
			$this->db->where(array('clusters.clusters_id'=>$hierarchy_id));
		}else{
			$this->db->where(array('region.region_id'=>$hierarchy_id));
		}

	}

	function dct_report_data($aggregation_type,$group_by,$month,$hierarchy_id){
		
		$start_month_date = date('Y-m-01',strtotime($month));
		$end_month_date = date('Y-m-t',strtotime($month));

		$select_columns = array('projectsdetails.icpNo as fcp_no','clusterName as cluster_name');


		$group_by_array = array('voucher_header.icpNo');

		if($group_by == 1){ // 1= FCP By Fund
			$group_by_array[] = 'voucher_body.AccNo';
			$select_columns[] = 'accounts.AccText as group_column';

		}elseif($group_by == 2){ // 2 = FCP By CIV
			$group_by_array[] = 'civa.AccNoCIVA';
			$select_columns[] = 'AccNoCIVA as group_column';
		}elseif($group_by == 3){ // 3 = FCP By Support Mode
			$group_by_array[] = 'voucher_body.fk_support_mode_id';
			$select_columns[] = 'support_mode_name as group_column';
		}

		

		$this->db->select($select_columns);

		if($aggregation_type == 1){ // 1 = Amount Spent
			$this->db->select('SUM(Cost) as agg_value');
		}else{
			$this->db->select('SUM(Qty) as agg_value');

			if($aggregation_type == 3){ // 3 = Count of Household
				$this->db->where(array('voucher_body.fk_voucher_item_type_id'=>2));
			}else{ // 2 = Count of Beneficiary
				$this->db->where(array('voucher_body.fk_voucher_item_type_id'=>1));
			}
		}
	
		$this->db->join('support_mode','support_mode.support_mode_id=voucher_body.fk_support_mode_id');
		$this->db->join('accounts','accounts.AccNo=voucher_body.AccNo');

		if($group_by == 2){
			$this->db->join('civa','civa.civaID=voucher_body.civaCode');
		}

		$this->db->join('voucher_header','voucher_header.hID=voucher_body.hID');
		$this->db->join('projectsdetails','projectsdetails.icpNo=voucher_header.icpNo');
		$this->db->join('clusters','clusters.clusters_id=projectsdetails.cluster_id');
		$this->db->join('region','region.region_id=clusters.region_id');
		$this->fcp_list($hierarchy_id);// Where condition
		$this->db->where(array('voucher_body.TDate>='=>$start_month_date,'voucher_body.TDate<='=>$end_month_date));
		$this->db->where(array('support_mode_is_dct'=>1)); // Only to list DCT records
		$this->db->where(array('AccGrp'=>0)); // Enforce retrieving expense records only. A defense code since income voucher lack linkage to support modes and voucher item types
		$this->db->group_by($group_by_array);
		$voucher_obj = $this->db->get('voucher_body');

		return $voucher_obj->result_array();
	}

	function dct_report_headers($month_vouchers){
		$headers = array_unique(array_column($month_vouchers,'group_column'));
		return $headers;
	}


	function dct_report_fcp_amount_and_count($month_vouchers,$headers,$group_by){

		$dct_report_fcp_amount = [];

		$values_array = [];

		foreach($month_vouchers as $vals){
			$values_array[$vals['fcp_no']][$vals['group_column']] = $vals['agg_value'];
		}

		foreach($month_vouchers as $detail){
			$dct_report_fcp_amount[$detail['fcp_no']]['cluster_name'] = $detail['cluster_name'];

			foreach($headers as $header){
				if(array_key_exists($header, $values_array[$detail['fcp_no']])){
					$dct_report_fcp_amount[$detail['fcp_no']]['values'][$header] = $values_array[$detail['fcp_no']][$header];
				}else{
					$dct_report_fcp_amount[$detail['fcp_no']]['values'][$header] = 0;
				}
			}
			
		}

		return $dct_report_fcp_amount;
	}


	function dct_report($aggregation_type,$group_by,$month,$hierarchy_id){
		
		$month_vouchers = $this->dct_report_data($aggregation_type,$group_by,$month,$hierarchy_id);

		$headers = $this->dct_report_headers($month_vouchers);
		
		$fcp_grouped_values = $this->dct_report_fcp_amount_and_count($month_vouchers,$headers,$group_by);

		return array_merge(['header_keys'=>$headers],$fcp_grouped_values);
	}
	
}

