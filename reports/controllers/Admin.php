<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Admin extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->load->model('dct_model');

		
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
	//Covid19
	public function covid19_report()
	{
		$reporting_month=date('Y-m-01');
		$group_report_by='beneficiary';
		
		if($this->input->post()){
			$group_report_by = $this->input->post('group_name');
			$reporting_month = $this->input->post('reporting_month');
		}	

		$grouping_column = $group_report_by;
		
		$covid19_data=$this->covid19_report_array_test($this->dct_model->covid19_data_query($reporting_month,$group_report_by),$grouping_column);

		$page_data['page_name']  = __FUNCTION__;
		$page_data['group_report_by'] = $group_report_by;
		$page_data['page_title'] = "Reports";
		$page_data['account_type'] = "admin";
		$page_data['covid19_data']=$this->utilised_support_modes($covid19_data)['support_modes_with_utilised_accs'];
		$page_data['report_result']=$covid19_data;
		$page_data['utilised_accounts']=$this->utilised_support_modes($covid19_data)['support_modes_with_utilised_accs'];
		
		if($this->input->post()){
			echo $this->load->view('backend/admin/includes/include_covid19_report', $page_data, true);	
		}else{
			$this->load->view('backend/index', $page_data);	
		}
	}
	function utilised_support_modes($report_result){

		$support_modes_with_utilised_accs=[];
		
        $holder_of_accounts=[];
		foreach($report_result as $support_modes_and_accounts){
		  
		  foreach($support_modes_and_accounts as $support_mode =>$accounts){

			$holder_of_accounts=array_merge($holder_of_accounts,array_keys($accounts));
		
			$support_modes_with_utilised_accs[$support_mode]=array_unique($holder_of_accounts);
		  }
		}

		return ['support_modes_with_utilised_accs'=>$support_modes_with_utilised_accs];

	}
	

	function covid19_report_array_test(Array $covid19_report_result,String $grouping_column){

		$cluster_support_mode_counts=[];

		foreach($covid19_report_result as  $count_array){
			$cluster_support_mode_counts[$count_array['clustername']][$count_array['support_mode_name']][$count_array['acctext']]=$count_array[$grouping_column];
		}
		return $cluster_support_mode_counts;
		//return $this->covid19_report_array();



		
	}

	

	function covid19_report_array(){


		return [
			'Kiambu' => [
				'UDCT Via MPesa' => [
					'E45' => 24,
					'E200' => 560,
					'E320' => 8
				],
				'Food Basket' => [
					'E45' => 2,
					'E365' => 89
					
				],
				'Hygiene Kit' => [
					'E45' => 20,
					'E30' => 56.87,
					'E320' => 102,
					'E300'=>450
				]
			],

			'Lake Basin' => [
				'UDCT Via MPesa' => [
					'E200' => 50.6,
					'E320' => 8.6,
					'E310'=>60,
					'E50'=>77
				],
				'Food Basket' => [
					'E45' => 2.94,
				],
				'Hygiene Kit' => [
					'E365' => 294,
					'E300' => 561,
					'E320' => 86.9
				]
			],

			'Mombasa' => [
				'UDCT Via MPesa' => [
					'E45' => 12,
					'E200' => 52,
					'E320' => 57
				],
				'Food Basket' => [
					'E45' => 24,
					'E40' => 59,
					'E415' => 806.0
				],
				'Hygiene Kit' => [
					'E45' => 21,
					'E30' => 50,
					'E320' => 86.90,
					'E330'=>54
				]
			]
		];
	}


	
	public function populate_fields(){
		$record_type_id = $_POST['record_type_id'];
		
		$record_type = $this->db->get_where('relationships',array("relationships_id"=>$record_type_id))->row();
		
		$sql = $this->get_query($record_type)." limit 0,1";
 	
		$result = $this->db->query($sql)->row();
		
		$filters['fields'] = array_keys((array)$result);
		
		echo $this->load->view('backend/admin/get_filters',$filters,true); 

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

	function ajax_load_dct_expense_report(){
		$aggregation_type = $this->input->post('aggregation_type');
		$group_by = $this->input->post('group_by');
		$month = $this->input->post('month');

		$page_data['data'] = $this->dct_model->dct_report($aggregation_type,$group_by,$month);

		echo $this->load->view('backend/admin/includes/include_dct_expense_report',$page_data,true);
	}

	function dct_report(){
		if ($this->session->userdata('admin_login') != 1)
			redirect(base_url(), 'refresh');


        $page_data['page_name']  = __FUNCTION__;
        $page_data['page_title'] = "DCT Expense Report";
        $this->load->view('backend/index', $page_data);	
	}
}
