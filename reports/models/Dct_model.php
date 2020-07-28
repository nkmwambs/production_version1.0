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

	
}

