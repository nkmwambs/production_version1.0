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

class Claims extends CI_Controller
{
    
    
	function __construct()
	{
		parent::__construct();
		$this->load->database();
        $this->load->library('session');

		
       /*cache control*/
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
		
    }
	
	//Modified by Onduso on 7/29/2020 to resolve SN ticket [INC0162871]

	function get_child_name($param1=""){
		$ben = $this->db->get_where("childdetails",array('childNo'=>$param1))->row_array();
		
		$name = get_phrase('name_not_found');
		
		if(count($ben)>0){
			$name = $ben['childName'];
		}

		echo $name;
	}	

	// function get_child_name($param1=""){
	// 	$ben = $this->db->get_where("childdetails",array('childNo'=>$param1))->row();
		
	// 	$name = get_phrase('name_not_found');
		
	// 	if(sizeof($ben)>0){
	// 		$name = $ben->childName;
	// 	}

	// 	echo $name;
	// }	

	
	
	
}