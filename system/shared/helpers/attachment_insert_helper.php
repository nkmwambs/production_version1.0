<?php

if(!function_exists('attachment_insert_array')){

    function attachment_insert_array($projectsdetails,$bank_statements,$document_types = ['bank_statements','dct_documents']){

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

            if(!isset($attachment_url_as_array[1])) continue;
    
            if(!in_array($attachment_url_as_array[1],$document_types)) continue;
    
            $files[$cnt]['attachment_name'] = $attachment_name;
            $files[$cnt]['attachment_url'] = $attachment_url;
            $files[$cnt]['attachment_size'] = $info->getSize();
            $files[$cnt]['is_upload_to_s3_completed'] = 1;
            $files[$cnt]['attachment_file_type'] = mime_content_type($pathinfo);
    
            if($attachment_url_as_array[1] == 'bank_statements' || $attachment_url_as_array[1] == 'dct_documents'){
                $files[$cnt]['fk_projectsdetails_id'] = isset($projectsdetails[$attachment_url_as_array[2]]) ? $projectsdetails[$attachment_url_as_array[2]] : 0;
                $files[$cnt]['item_name'] = isset($attachment_url_as_array[1]) ? $attachment_url_as_array[1] : 0;
            }
    
            if($attachment_url_as_array[1] == 'bank_statements'){
                $files[$cnt]['attachment_primary_id'] = isset($bank_statements[$attachment_url_as_array[2]][$attachment_url_as_array[3]])?$bank_statements[$attachment_url_as_array[2]][$attachment_url_as_array[3]]:0;
            }elseif($attachment_url_as_array[1] == 'dct_documents'){
                $files[$cnt]['attachment_primary_id'] = isset($attachment_url_as_array[4]) ? $attachment_url_as_array[4] : 0;
            }
    
            $cnt++;
        }
        }
    
        return $files;
    }
    
}