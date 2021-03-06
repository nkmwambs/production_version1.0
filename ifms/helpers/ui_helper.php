<?php

if(!function_exists('fcp_reports_dropdown')){
    function fcp_reports_dropdown($fcp_id,$month){

        $li = '<div class="btn-group"><button id="btn-'.$fcp_id.'" type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
						    	'.$fcp_id.' <span class="caret"></span>
						    </button>
						    	<ul class="dropdown-menu dropdown-default pull-left" role="menu">
        ';

        $label_array = [
            'bank_statements'=>[
                'href'=> base_url().'ifms.php/accountant/bank_statements/'.$month.'/'.$fcp_id,
                'onclick' => ''
            ],
            'outstanding_cheques' => [
                'href'=> "#",
                'onclick' => "showAjaxModal('".base_url()."ifms.php/modal/popup/modal_outstanding_cheques/".date('Y-m-t',$month)."/".$fcp_id."')"
            ],
            'deposits_in_transit' => [
                'href'=> "#",
                'onclick' => "showAjaxModal('".base_url()."ifms.php/modal/popup/modal_transit_deposits/".date('Y-m-t',$month)."/".$fcp_id."')"
            ],
            'bank_reconciliation' => [
                'href'=> "#",
                'onclick' => "showAjaxModal('".base_url()."ifms.php/modal/popup/modal_bank_reconcile/".date('Y-m-t',$month)."/".$fcp_id."')"
            ],
            'variance_explanation' => [
                'href'=> "#",
                'onclick' => "showAjaxModal('".base_url()."ifms.php/modal/popup/modal_variance_explanation/".date('Y-m-t',$month)."/".$fcp_id."')"
            ],
            'proof_of_cash' => [
                'href'=> "#",
                'onclick' => "showAjaxModal('".base_url()."ifms.php/modal/popup/modal_proof_of_cash/".date('Y-m-t',$month)."/".$fcp_id."')"
            ],
            'fund_balance_report' => [
                'href'=> "#",
                'onclick' => "showAjaxModal('".base_url()."ifms.php/modal/popup/modal_fund_balances/".date('Y-m-t',$month)."/".$fcp_id."')"
            ],
            'expense_report' => [
                'href'=> "#",
                'onclick' => "showAjaxModal('".base_url()."ifms.php/modal/popup/modal_expense_report/".date('Y-m-t',$month)."/".$fcp_id."')"
            ],
            'cash_journal' => [
                'href'=> base_url().'ifms.php/accountant/cash_journal/'.$month.'/'.$fcp_id,
                'onclick' => ""
            ],
            'budget' => [
                'href'=> base_url().'ifms.php/accountant/plans/'.$month.'/'.$fcp_id,
                'onclick' => ""
            ]
        ];

        foreach($label_array as $label => $url_components){
            $target = $url_components['href'] != '#'?"target='__blank'":'';
          
            $li .= '<li><a '.$target.' href="'.$url_components['href'].'" onclick="'.$url_components['onclick'].'">'.ucwords(str_replace('_',' ',$label)).'</a></li>';
            $li .= "<li class='divider'></li>";
        }

        $li .= "</ul></div>";
        
        return $li;
    }
}


if(!function_exists('parameter_cell')){
    function parameter_cell($param_value){

        $color_class = '';
        // $color_class = "success_parameter";

        // if($param_value == 0 || strpos($param_value,"No") == true){
        //    $color_class = "fail_parameter";
        // }

        return '<td class="'.$color_class.'">'.$param_value.'</td>';
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


// if(!function_exists('list_s3_uploaded_document_names')){
//     function list_s3_uploaded_document_names($uploaded_files){
//         $CI =& get_instance();

//         $param['uploaded_files'] = $uploaded_files;
//         $param['show_as_table'] = false;
        
//         //return json_encode($param);
//         return $CI->load->view('backend/list_s3_uploaded_documents.php',$param,true);
//     }
// }