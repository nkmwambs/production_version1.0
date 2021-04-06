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
            

            $files[$cnt]['attachment_name'] = $attachment_name;
            $files[$cnt]['attachment_url'] = $attachment_url;
            $files[$cnt]['attachment_size'] = $info->getSize();
            $files[$cnt]['is_upload_to_s3_completed'] = 1;
            $files[$cnt]['attachment_file_type'] = mime_content_type($pathinfo);
    
            if(isset($attachment_url_as_array[1]) && ($attachment_url_as_array[1] == 'bank_statements' || $attachment_url_as_array[1] == 'dct_documents')){
                $files[$cnt]['fk_projectsdetails_id'] = $projectsdetails[$attachment_url_as_array[2]];
                $files[$cnt]['item_name'] = $attachment_url_as_array[1];
            }elseif(isset($attachment_url_as_array[2]) && $attachment_url_as_array[2] == 'medical'){
                $files[$cnt]['fk_projectsdetails_id'] = isset($claiming_fcp_projectsdetails[$attachment_url_as_array[4]]) ? $claiming_fcp_projectsdetails[$attachment_url_as_array[4]] : 0;
                $files[$cnt]['item_name'] = isset($attachment_url_as_array[3]) ? $attachment_url_as_array[3] : 0;
            }
    
            if(isset($attachment_url_as_array[1]) && $attachment_url_as_array[1] == 'bank_statements'){
                $files[$cnt]['attachment_primary_id'] = isset($bank_statements[$attachment_url_as_array[2]][$attachment_url_as_array[3]]) ? $bank_statements[$attachment_url_as_array[2]][$attachment_url_as_array[3]] : 0;
            }elseif((isset($attachment_url_as_array[1]) && $attachment_url_as_array[1] == 'dct_documents') || (isset($attachment_url_as_array[2]) && $attachment_url_as_array[2] == 'medical')){
                $files[$cnt]['attachment_primary_id'] = isset($attachment_url_as_array[4]) ? $attachment_url_as_array[4] : 0;
            }
    
            $cnt++;

            //echo json_encode($files)."\r\n";

            if($cnt % 5  == 0){
                if(count($files) > 0){
                    // Do insert
                    echo json_encode($files);
                    $CI->db->insert_batch('attachment', $files);
                    
                    // Empty the array
                    $files = array();
                    break;
                }
            }

        }
        }
    
        return $files;
    }
    
}