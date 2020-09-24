<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DCT_model extends CI_Model {
	
	function __construct()
    {
        parent::__construct();
	}
	
	function covid19_data_query($reporting_month,$group_report_by='beneficiary'){

		$start_date=date('Y-m-d',strtotime($reporting_month));
		$end_date=date('Y-m-t',strtotime($reporting_month));;

		//$group_report_by='beneficiary';//FCP,Cost, Household
		$group_by_array=array('clusters.clustername','accounts.accno','voucher_body.fk_support_mode_id');

		//$this->db->select(array('support_mode.support_mode_id'));
		$this->db->select(array('clusters.clustername','accounts.acctext','support_mode.support_mode_name'));

		if($group_report_by=='household'){
			//$group_by_array=array_push($group_by_array,'voucher_item_type.voucher_item_type_id');
			$group_by_array=array('voucher_body.fk_voucher_item_type_id','clusters.clustername','accounts.accno','voucher_body.fk_support_mode_id');
			  
			  //$this->db->select_sum('voucher_body.qty');
			  $this->db->select('sum(voucher_body.qty) as '.$group_report_by);
			  $this->db->where(array('voucher_body.fk_voucher_item_type_id'=>2));
		}
		else if($group_report_by=='fcp'){
			
			  $this->db->select('count(distinct voucher_body.icpno) as '.$group_report_by);
		}
		else if($group_report_by=='amount'){
			$this->db->select('sum(voucher_body.cost) as '.$group_report_by);
		}
		else{
			//$group_by_array=array_push($group_by_array,'voucher_item_type.voucher_item_type_id');
			  $group_by_array=array('voucher_body.fk_voucher_item_type_id','clusters.clustername','accounts.accno','voucher_body.fk_support_mode_id');
			  //$this->db->select_sum('voucher_body.qty');
			  $this->db->select('sum(voucher_body.qty) as '.$group_report_by);
			  $this->db->where(array('voucher_body.fk_voucher_item_type_id'=>1));
		}
		
		$this->db->join('projectsdetails', 'projectsdetails.icpno = voucher_body.icpno');
		$this->db->join('clusters','clusters.clusters_id = projectsdetails.cluster_id');
		$this->db->join('accounts','accounts.accno=voucher_body.accno');
		//$this->db->join('accounts_support_mode','accounts_support_mode.fk_accounts_id=accounts.accId');
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


	function dct_report($aggregation_type,$group_by,$month){
		$dct_data = [
			'header_keys'=>$group_by == 1 ? ['E10','E45','E320','E330','E410','E420','E430'] : ($group_by == 2 ? ['EDU-107','WTP-105','DRF-109','DRF-171','HH-112','EDU-124','DRF-119'] : ['M-Pesa','Airtel Money','Normal Transaction','Food Basket','Hygiene Kits','Food Distribution','Food Packs']),
			'KE200'=>[
				'cluster_name'=>'Ishiara',
				'values'=>$aggregation_type == 1 ? [456000,0,654790.32,0,543211,215600,654600] : ($aggregation_type == 2 ? [112,0,89,0,96,154,211]: [23,0,76,0,12,15,26]) 
			],
			'KE201'=>[
				'cluster_name'=>'Ishiara',
				'values'=>$aggregation_type == 1 ? [432780,676150.12,120000,78900,20000,0,65400] : ($aggregation_type == 2 ? [45,12,32,8,0,0,65]: [1,4,66,7,1,5,0])  
			],
			'KE450'=>[
				'cluster_name'=>'Kakamega',
				'values'=>$aggregation_type == 1 ? [23400,2500,0,0,0,18400,45300.23] : ($aggregation_type == 2 ? [150,82,95,117,34,71,108]: [0,0,0,0,9,36,18]) 
			],
			'KE331'=>[
				'cluster_name'=>'Kisumu',
				'values'=>$aggregation_type == 1 ? [0,0,0,0,167500.45,185430,33200] : ($aggregation_type == 2 ? [115,23,104,117,0,0,118]: [32,0,0,0,0,65,34]) 
			],
			'KE782'=>[
				'cluster_name'=>'Kisumu',
				'values'=>$aggregation_type == 1 ? [223400,345600,112340,0,65400,234500,34500] : ($aggregation_type == 2 ? [56,72,52,11,106,86,104]: [46,21,32,57,89,31,55])  
			]
		];

		return $dct_data;
	}
	
}

