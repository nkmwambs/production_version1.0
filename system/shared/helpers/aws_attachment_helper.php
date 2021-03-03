<?php

if(!function_exists('formatBytes')){
	function formatBytes($bytes, $precision = 2) { 
		$units = array('B', 'KB', 'MB', 'GB', 'TB'); 
	
		$bytes = max($bytes, 0); 
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
		$pow = min($pow, count($units) - 1); 
	
		// Uncomment one of the following alternatives
		$bytes /= pow(1024, $pow);
		// $bytes /= (1 << (10 * $pow)); 
	
		return round($bytes, $precision) . ' ' . $units[$pow]; 
	} 
}

if(!function_exists('list_s3_uploaded_documents')){
    function list_s3_uploaded_documents($uploaded_files,$show_as_table = true){
        $CI =& get_instance();

        $param['uploaded_files'] = $uploaded_files;
        $param['show_as_table'] = $show_as_table;

        return $CI->load->view('backend/list_s3_uploaded_documents.php',$param,true);
    }
}