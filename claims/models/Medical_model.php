<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Medical_model extends CI_Model {

	var $table = 'claims';
	var $column_order = array(null, 'incidentID','proNo','cluster','childNo','childName','treatDate','date','diagnosis','totAmt','careContr','nhif','amtReim','facName','facClass','type','vnum','rct','refNo','rmks','stmp','incident_type','incident_sub_category_id','illness_id','claimCnt'); //set column field database for datatable orderable
	var $column_search = array('incidentID','proNo','cluster','childNo','childName','treatDate','date','diagnosis','totAmt','careContr','nhif','amtReim','facName','facClass','type','vnum','rct','refNo','rmks','stmp','incident_type','incident_sub_category_id','illness_id','claimCnt'); //set column field database for datatable searchable 
	var $order = array('rec' => 'asc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		
		/**Start of custom filters**/
		$count_of_custom_filters = 0;
		
		if($this->input->post('date_range')!=="" && $this->input->post('start_date')){			
			
			$this->db->where($this->input->post('date_range').'>=', $this->input->post('start_date'));
			$this->db->where($this->input->post('date_range').'<=', $this->input->post('end_date'));
			
			$count_of_custom_filters++;
		}

		foreach($this->column_search as $search):
			if($this->input->post($search))
	        {
				if($search === 'rmks' && $this->input->post($search)!=='-2' && $this->input->post($search)!=='-3' && $this->input->post($search)!=='15' && $this->input->post($search)!=='-4' && $this->input->post($search)!=='-5'){
					switch($this->session->userdata('logged_user_level')):
						case '1':
							$cond = " proNo='".$this->session->userdata('center_id')."' AND ".$search." = '".$this->input->post($search)."' AND reinstatementdate IS NULL";
							$this->db->where($cond);
							//$this->db->where(array($search=> $this->input->post($search),"proNo"=>$this->session->userdata('center_id')));
							break;
						case 2:
							$cond = " cluster='".$this->session->userdata('cluster')."' AND ".$search." = '".$this->input->post($search)."' AND reinstatementdate IS NULL";
							$this->db->where($cond);
							//$this->db->where(array($search=> $this->input->post($search),"cluster"=>$this->session->userdata('cluster')));
							break;	
						default:
							$cond = $search." = '".$this->input->post($search)."' AND reinstatementdate IS NULL";
							$this->db->where($cond);
							//$this->db->where(array($search=> $this->input->post($search)));
								
					endswitch;		
					
				}elseif($search === 'rmks' && $this->input->post($search)==='-2'){

					switch($this->session->userdata('logged_user_level')):
						case '1':
							$cond = " proNo='".$this->session->userdata('center_id')."' AND reinstatementdate IS NOT NULL AND rmks = 1 ";
							$this->db->where($cond);
							break;
						case 2:
							
							$cond = " cluster='".$this->session->userdata('cluster')."' AND reinstatementdate IS NOT NULL AND rmks = 1 ";
							$this->db->where($cond);
							break;	
						default:
							$cond = " reinstatementdate IS NOT NULL AND rmks = 1 ";
							$this->db->where($cond);
								
					endswitch;
										
					
					
				}elseif($search === 'rmks' && $this->input->post($search)==='-3'){
					
					switch($this->session->userdata('logged_user_level')):
						case '1':
							$cond = " proNo='".$this->session->userdata('center_id')."' AND reinstatementdate IS NOT NULL AND rmks = 3 ";
							$this->db->where($cond);
							break;
						case 2:
							
							$cond = " cluster='".$this->session->userdata('cluster')."' AND reinstatementdate IS NOT NULL AND rmks = 3 ";
							$this->db->where($cond);
							break;	
						default:
							$cond = " reinstatementdate IS NOT NULL AND rmks = 3 ";
							$this->db->where($cond);
								
					endswitch;
				}elseif($search === 'rmks' && $this->input->post($search)==='-4'){
					
					switch($this->session->userdata('logged_user_level')):
						case '1':
							$cond = " proNo='".$this->session->userdata('center_id')."' AND reinstatementdate IS NOT NULL AND rmks = 10 ";
							$this->db->where($cond);
							break;
						case 2:
							
							$cond = " cluster='".$this->session->userdata('cluster')."' AND reinstatementdate IS NOT NULL AND rmks = 10 ";
							$this->db->where($cond);
							break;	
						default:
							$cond = " reinstatementdate IS NOT NULL AND rmks = 10 ";
							$this->db->where($cond);
								
					endswitch;
				}elseif($search === 'rmks' && $this->input->post($search)==='-5'){
					switch($this->session->userdata('logged_user_level')):
						case '1':
							$cond = " proNo='".$this->session->userdata('center_id')."' AND reinstatementdate IS NOT NULL ";
							$this->db->where($cond);
							break;
						case 2:
							
							$cond = " cluster='".$this->session->userdata('cluster')."' AND reinstatementdate IS NOT NULL ";
							$this->db->where($cond);
							break;	
						default:
							$cond = " reinstatementdate IS NOT NULL  ";
							$this->db->where($cond);
					endswitch;		
				}elseif($search === 'rmks' && $this->input->post($search)==='15'){
					
					switch($this->session->userdata('logged_user_level')):
						case '1':
							$cond = " proNo='".$this->session->userdata('center_id')."' AND rmks = 0 ";
							$this->db->where($cond);
							break;
						case 2:
							
							$cond = " cluster='".$this->session->userdata('cluster')."'  rmks = 0 ";
							$this->db->where($cond);
							break;	
						default:
							$cond = " rmks = 0 ";
							$this->db->where($cond);
								
					endswitch;		
					
				}else{
					switch($this->session->userdata('logged_user_level')):
						case '1':
								$this->db->like(array($search=> $this->input->post($search),"proNo"=>$this->session->userdata('center_id')));	
							break;
						case '2':
								$this->db->like(array($search=> $this->input->post($search),"cluster"=>$this->session->userdata('cluster')));

							break;	
						default:

								$this->db->like(array($search=> $this->input->post($search)));
							
					endswitch;		
				}
				
				$count_of_custom_filters++;
	        }
		endforeach;
		
		//No filters available
		if($count_of_custom_filters===0){	
			switch($this->session->userdata('logged_user_level')):
				case '1':
	
						//if($count_of_custom_filters===0){
							
							$cond1 = " proNo = '".$this->session->userdata('center_id')."' AND ((rmks= -1  OR rmks = 1 OR rmks = 3) AND reinstatementdate IS NULL) ";
							
							$this->db->where($cond1);	
						//}
					break;				
				case '2':
	
						//if($count_of_custom_filters===0){
							
							$cond2 = " cluster = '".$this->session->userdata('cluster')."' AND ((rmks= -1  OR rmks = 1 OR rmks = 3) AND reinstatementdate IS NULL) ";
							
							$this->db->where($cond2);	
						//}
					break;
				case '5':
						//if($count_of_custom_filters===0){
							$cond3 = " (`rmks` = 0 OR `rmks` = 2) OR ((`rmks` = 1 OR `rmks` = 3 OR `rmks` = 10) AND `reinstatementdate` IS NOT NULL) ";
							
							$this->db->where($cond3);	
						//}
					break;	
			endswitch;
		}
		/**End of custom filters**/	
		
		 
		$this->db->from($this->table);

		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}
	
	function search_datatables($filter="")
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$this->db->where($filter);
		$query = $this->db->get();
		return $query->result();
	}	

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}
	
	public function get_list_clusters()
    {
        $this->db->select('cluster');
        $this->db->from($this->table);
        $this->db->order_by('date','asc');
        $query = $this->db->get();
        $result = $query->result();
 
        $clusters = array();
        foreach ($result as $row) 
        {
            $clusters[] = $row->cluster;
        }
        return $clusters;
    }


	function uploaded_claim_documents($claim_id = "",$document_type){
        
        
        $url = 'uploads/document/medical/'.$document_type.'/'.$claim_id;

        $this->db->select(array('attachment_id','attachment_name','attachment_url','attachment_created_date','attachment_size'));

        $this->db->where(
            array(
                'item_name'=>$document_type,
                'attachment_primary_id'=>$claim_id,
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

}
