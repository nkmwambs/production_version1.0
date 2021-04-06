<?php


if(!function_exists('insert_planheader_id_to_variance_explanation')){
    function insert_planheader_id_to_variance_explanation(){

        $CI =& get_instance();

        $fys = [
            16 => ['2015-07-01','2016-06-30'],
            17 => ['2016-07-01','2017-06-30'],
            18 => ['2017-07-01','2018-06-30'],
            19 => ['2018-07-01','2019-06-30'],
            20 => ['2019-07-01','2020-06-30'],
            21 => ['2020-07-01','2021-06-30'],
        ];

        $CI->db->select(array('icpNo'));
        $projectsdetails = $CI->db->get('projectsdetails')->result_array();

        //$cnt = 0;
        foreach($projectsdetails as $fcp){
            
            //if($cnt == 5) break;

            foreach($fys as $fy => $dates){
                $sql = "UPDATE varjustify SET planHeaderID = (SELECT planHeaderID FROM planheader WHERE icpNo = '".$fcp['icpNo']."' AND fy = $fy)  WHERE varjustify.icpNo = '".$fcp['icpNo']."' AND varjustify.reportMonth >= '".$dates[0]."' AND varjustify.reportMonth <= '".$dates[1]."'";
                
                $CI->db->query($sql);
            }
            
            //$cnt++;
        }
        
    }
}

if(!function_exists('attachment_insert_array')){

    function attachment_insert_array($projectsdetails,$bank_statements,$claiming_fcp_projectsdetails,$document_types = ['bank_statements','dct_documents','medical']){
        
        $CI =& get_instance();

        $path = 'uploads';
    
        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);
        $files = array();
    
        $cnt = 0;

        foreach ($iterator as $info) {
        if (!$info->isDir()) {
            
            $pathinfo = $info->getPathname();
            $attachment_url = str_replace("\\","/",pathinfo($pathinfo,PATHINFO_DIRNAME));
            $attachment_name = pathinfo($pathinfo,PATHINFO_BASENAME); 
            $attachment_url_as_array = explode('/',$attachment_url);
            
            if(count($attachment_url_as_array) < 3) continue;
            if(!in_array($attachment_url_as_array[1],$document_types) && !in_array($attachment_url_as_array[2],$document_types)) continue;
            

            $files['attachment_name'][$cnt] = $attachment_name;
            $files['attachment_url'][$cnt] = $attachment_url;
            $files['attachment_size'][$cnt] = $info->getSize();
            $files['is_upload_to_s3_completed'][$cnt] = 1;
            $files['attachment_file_type'][$cnt] = mime_content_type($pathinfo);
    
            if(isset($attachment_url_as_array[1]) && ($attachment_url_as_array[1] == 'bank_statements' || $attachment_url_as_array[1] == 'dct_documents')){
                $files['fk_projectsdetails_id'][$cnt] = $projectsdetails[$attachment_url_as_array[2]];
                $files['item_name'][$cnt] = $attachment_url_as_array[1];
            }elseif(isset($attachment_url_as_array[2]) && $attachment_url_as_array[2] == 'medical'){
                $files['fk_projectsdetails_id'][$cnt] = isset($claiming_fcp_projectsdetails[$attachment_url_as_array[4]]) ? $claiming_fcp_projectsdetails[$attachment_url_as_array[4]] : 0;
                $files['item_name'][$cnt] = isset($attachment_url_as_array[3]) ? $attachment_url_as_array[3] : 0;
            }
    
            if(isset($attachment_url_as_array[1]) && $attachment_url_as_array[1] == 'bank_statements'){
                $files['attachment_primary_id'][$cnt] = isset($bank_statements[$attachment_url_as_array[2]][$attachment_url_as_array[3]]) ? $bank_statements[$attachment_url_as_array[2]][$attachment_url_as_array[3]] : 0;
            }elseif((isset($attachment_url_as_array[1]) && $attachment_url_as_array[1] == 'dct_documents') || (isset($attachment_url_as_array[2]) && $attachment_url_as_array[2] == 'medical')){
                $files['attachment_primary_id'][$cnt] = isset($attachment_url_as_array[4]) ? $attachment_url_as_array[4] : 0;
            }
    
            $cnt++;

            //echo json_encode($files)."\r\n";

            if($cnt % 100  == 0){
                if(count($files) > 0){
                    // Do insert
                    //$CI->db->insert_batch('attachment', $files);
                    echo json_encode($files);
                    // Empty the array
                    unset($files);
                    break;
                }
            }

        }
        }
    
        return $files;
    }
    
}