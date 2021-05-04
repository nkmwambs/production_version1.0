<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */


if ( ! function_exists('valid_date'))
{
 
 	function valid_date($date) {
    	return (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date));
	}
 
}
if ( ! function_exists('get_fy'))
{
		function get_fy($date = '',$project="") {
		
		$CI =& get_instance();
		
		$CI->load->database();
		
		$fy_start_month = $CI->db->get_where('settings',array('type'=>'fy_start_month'))->row()->description; 
		
		$months = range(1,12);
		
		$range['first'] = range($fy_start_month,12);
		$range['second'] = range(1,$fy_start_month-1);
		
		$fy = date('y',strtotime($date));
		
		if(in_array(date('n',strtotime($date)), $range['first'])){
			$fy = date('y',strtotime($date))+1;
		}

		return $fy; 
	}
}

if ( ! function_exists('fy_start_date'))
{
	function fy_start_date($date = '',$project="") {
				$fy = get_fy($date,$project)-1;
					
				$fy_start_date = '20'.$fy."-07-01";
			    return $fy_start_date;                                               
        	}
 }

if ( ! function_exists('fy_end_date'))
{
	function fy_end_date($date = '',$project="") {
				$fy = get_fy($date,$project);
					
				$fy_end_date = '20'.$fy."-06-30";
			    return $fy_end_date;                                               
        	}
 }


if( ! function_exists('months_elapsed'))
{
	function months_elapsed($date1, $date2)
	{
		$datetime1 = date_create($date1);
		$datetime2 = date_create($date2);
		$interval = date_diff($datetime1, $datetime2);
		
		return $interval->format('%m');
	}	
}

if(! function_exists('validateDate')){
	function validateDate($date)
		{
		    $d = DateTime::createFromFormat('Y-m-d', $date);
		    return $d && $d->format('Y-m-d') === $date;
		}
}

if(! function_exists('checkIsAValidDate')){
	function checkIsAValidDate($myDateString){
    	return (bool)strtotime($myDateString);
	}
}

if(!function_exists('get_related_voucher')){
	function get_related_voucher($voucher_id){
		$CI =& get_instance();
		return $CI->db->get_where('voucher_header',array('hID'=>$voucher_id))->row()->VNumber;
	}
}
// ------------------------------------------------------------------------
/* End of file language_helper.php */
/* Location: ./system/helpers/language_helper.php */