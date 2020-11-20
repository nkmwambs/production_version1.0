<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Facilitator extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->database();
        $this->load->library('session');

		
       /*cache control*/
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
		
    }
	public function dashboard()
	{
		
		$page_data['record_types'] = $this->db->get('relationships')->result_object();		
        $page_data['page_name']  = __FUNCTION__;
        $page_data['page_title'] = "Reports";
        $this->load->view('backend/index', $page_data);	
	}
	
	public function populate_fields(){
		$record_type_id = $_POST['record_type_id'];
		
		$record_type = $this->db->get_where('relationships',array("relationships_id"=>$record_type_id))->row();
		
		$sql = $this->get_query($record_type)." limit 0,1";
 	
		$result = $this->db->query($sql)->row();
		
		$filters['fields'] = array_keys((array)$result);
		
		echo $this->load->view('backend/admin/get_filters',$filters,true); 

	}

	function covid19_vouchers($cluster_name,$support_mode_name,$account_code){

		$cluster_name = urldecode($cluster_name);
		$support_mode_name =  urldecode($support_mode_name);
		$account_code = urldecode($account_code);

		$this->db->select(array('voucher_header.hID as voucher_id','voucher_header.icpNo as fcp_number','voucher_header.VNumber as voucher_number','voucher_header.TDate as transaction_date'));
		$this->db->select_sum('Cost');
		$this->db->join('projectsdetails','projectsdetails.icpNo=voucher_header.icpNo');
		$this->db->join('clusters','clusters.clusters_id=projectsdetails.cluster_id');
		$this->db->join('voucher_body','voucher_body.hID=voucher_header.hID');
		$this->db->join('accounts','accounts.AccNo=voucher_body.AccNo');
		$this->db->join('support_mode','support_mode.support_mode_id=voucher_body.fk_support_mode_id');
		$this->db->where(array('clusterName'=>$cluster_name));
		$this->db->where(array('support_mode_name'=>$support_mode_name));
		$this->db->where(array('accounts.AccText'=>$account_code));
		$this->db->where(array('Cost > '=> 0));

		$this->db->group_by(array('clusterName','voucher_header.VNumber','support_mode_name','accounts.AccText'));

		$vouchers = $this->db->get('voucher_header')->result_array();

		$page_data['page_name']  = __FUNCTION__;
		$page_data['page_title'] = "Voucher List";
		//$page_data['account_type'] = "admin";
		$page_data['vouchers']  = $vouchers;
		
		$this->load->view('backend/index', $page_data);	
		
	}
	
	public function developsql(){
		
		$record_type_id = array_shift($_POST);
		
		$operations = $_POST;
		
		$record_type = $this->db->get_where('relationships',array("relationships_id"=>$record_type_id))->row();
		
		$sql = $this->get_query($record_type,$operations);
 	
		$result = $this->db->query($sql)->result_object();
		
		//print_r($result);
		
		$data['result'] = $result;
		
		$data['options'] = explode(",", $record_type->yes_no_fields);
		
		$render['sql'] = $sql;

		$render['result'] = $this->load->view('backend/admin/get_result_table',$data,true);
		
		echo json_encode($render);
	}
	
	public function get_query($record_type,$operations=array()){
		
		$sql = "SELECT * FROM `".$record_type->primary_table."` ".$record_type->join_type." `".$record_type->secondary_table."` ON `".$record_type->primary_table."`.`".$record_type->primary_index."` = `".$record_type->secondary_table."`.`".$record_type->secondary_index."`  LIMIT 10";
	
	
		$row_result = array_keys((array)$this->db->query($sql)->row());	
		
		if(!empty($this->get_additional_fields($record_type->secondary_table))){
			$row_result = array_unique(array_merge(array_keys((array)$this->db->query($sql)->row()),$this->get_additional_fields($record_type->secondary_table)));
		}	
		
		$fields = array();
		$str_fields = "*";
		
		if($record_type->fields_exceptions!==""){
			$exceptions = explode(",", $record_type->fields_exceptions);
			
			foreach($row_result as $row){
				if(array_search($row, $exceptions) === FALSE){
					$fields[] = $row;
				}
			}
		
		$str_fields = implode(",", $fields);
		}
		
		if($record_type->show_fields!==""){
			$str_fields = $record_type->show_fields;
		}	
		
		 
		$operate = "";
		if(!empty($operations)){
			for($i=0;$i<sizeof($operations['field']);$i++){
				if($operations['operand'][$i] === 'LIKE'){
					
					if(strpos($operations['value'][$i], ",")!==FALSE){
						$val_arr = explode(",", $operations['value'][$i]);
							$operate .= " (";
						for($j=0;$j<sizeof($val_arr);$j++){
							$operate .= "(".$operations['field'][$i]." ".$operations['operand'][$i]." '%".$val_arr[$j]."%') OR ";
						} 
						
						$operate = substr($operate, 0,-3);
						
						$operate .= ") AND ";

						
					}else{
						$operate .= " ".$operations['field'][$i]." ".$operations['operand'][$i]." '%".$operations['value'][$i]."%' AND ";
					}
					
					
				}else{
					
					
					if(strpos($operations['value'][$i], ",")!==FALSE){
						$val_arr = explode(",", $operations['value'][$i]);
							$operate .= " (";
						for($j=0;$j<sizeof($val_arr);$j++){
							$operate .= "(".$operations['field'][$i].$operations['operand'][$i]."'".$val_arr[$j]."') OR ";
						} 
						
						$operate = substr($operate, 0,-3);
						
						$operate .= ") AND ";
						
						
						
					}else{
						$operate .= " ".$operations['field'][$i].$operations['operand'][$i]."'".$operations['value'][$i]."' AND ";
					}
					
					
				}
					
				
			}
			
			$operate = substr($operate, 0,-4);
		}

		$extra_condition = "";
		if($record_type->extra_condition!==""){
			$extra_condition = str_replace(",", " AND ", $record_type->extra_condition);
		}
		
		$where = " ";
		if($record_type->extra_condition!=="" && !empty($operations)){
			$where .= " WHERE ".$extra_condition." AND ".$operate;
		}elseif($record_type->extra_condition=="" && !empty($operations)){
			$where .=" WHERE ".$operate;	
		}elseif($record_type->extra_condition!=="" && empty($operations)){
			$where .= " WHERE ".$extra_condition;
		}
		
		//return "SELECT ".$str_fields." FROM `".$record_type->primary_table."` ".$record_type->join_type." `".$record_type->secondary_table."` ON `".$record_type->primary_table."`.`".$record_type->primary_index."` = `".$record_type->secondary_table."`.`".$record_type->secondary_index."` ".$this->get_additional_joins($record_type->secondary_table)." ".$where."  "; 
		return "SELECT ".$str_fields." FROM `".$record_type->primary_table."` ".$record_type->join_type." `".$record_type->secondary_table."` ON `".$record_type->primary_table."`.`".$record_type->primary_index."` = `".$record_type->secondary_table."`.`".$record_type->secondary_index."` ".$this->get_additional_joins($record_type->secondary_table)." ".$where."  ";
	}

	function get_additional_joins($secondary_table=""){
			
		$new_join="";
			
		if($this->db->get_where('relationships',array('primary_table'=>$secondary_table))->num_rows()>0){
			
			$related_record_types = $this->db->get_where('relationships',array('primary_table'=>$secondary_table))->result_object(); 
			
			foreach($related_record_types as $record_type){
				 $new_join .= " ".$record_type->join_type." `".$record_type->secondary_table."` ON `".$record_type->primary_table."`.`".$record_type->primary_index."` = `".$record_type->secondary_table."`.`".$record_type->secondary_index."` ";
			}	
		}
		
			
		return $new_join;
	} 
	
	function get_additional_fields($secondary_table=""){

		$fields = array();

		 if($this->db->get_where('relationships',array('primary_table'=>$secondary_table))->num_rows()>0){
				
			$related_record_types = $this->db->get_where('relationships',array('primary_table'=>$secondary_table))->result_object();
			
			foreach($related_record_types as $record_type){
				
				
				$sql = "SELECT * FROM `".$record_type->primary_table."` ".$record_type->join_type." `".$record_type->secondary_table."` ON `".$record_type->primary_table."`.`".$record_type->primary_index."` = `".$record_type->secondary_table."`.`".$record_type->secondary_index."`  LIMIT 0,1";
				
				$keys = array_keys((array)$this->db->query($sql)->row());
				
				foreach($keys as $elem){
					if(!in_array($elem, $fields)){
						$fields[] = $elem;
					}
				}
				
				
				
			}
			
		}
			
		return $fields;		

	}
}
