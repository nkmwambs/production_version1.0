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
                'onclick' => "showAjaxModal('".base_url()."ifms.php/modal/popup/modal_outstanding_cheques/".date('Y-m-01',$month)."/".$fcp_id."')"
            ],
            'deposits_in_transit' => [
                'href'=> "#",
                'onclick' => "showAjaxModal('".base_url()."ifms.php/modal/popup/modal_transit_deposits/".date('Y-m-01',$month)."/".$fcp_id."')"
            ],
            'bank_reconciliation' => [
                'href'=> "#",
                'onclick' => "showAjaxModal('".base_url()."ifms.php/modal/popup/modal_bank_reconcile/".date('Y-m-01',$month)."/".$fcp_id."')"
            ],
            'variance_explanation' => [
                'href'=> "#",
                'onclick' => "showAjaxModal('".base_url()."ifms.php/modal/popup/modal_variance_explanation/".date('Y-m-01',$month)."/".$fcp_id."')"
            ],
            'proof_of_cash' => [
                'href'=> "#",
                'onclick' => "showAjaxModal('".base_url()."ifms.php/modal/popup/modal_proof_of_cash/".date('Y-m-01',$month)."/".$fcp_id."')"
            ],
            'fund_balance_report' => [
                'href'=> "#",
                'onclick' => "showAjaxModal('".base_url()."ifms.php/modal/popup/modal_fund_balances/".date('Y-m-01',$month)."/".$fcp_id."')"
            ],
            'expense_report' => [
                'href'=> "#",
                'onclick' => "showAjaxModal('".base_url()."ifms.php/modal/popup/modal_expense_report/".date('Y-m-01',$month)."/".$fcp_id."')"
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
            // $target = $url_components['href'] != '#'?"target='__blank'":'';
            // $li .= "<li>
            // <a ".$target." href= '".$url_components['href']."' onclick = '".$url_components['onclick']."'>".get_phrase($label)."</a>
            // </li>";

            $li .= "<li><a href='' onclick=''>".ucwords(str_replace('_',' ',$label))."</a></li>";
            $li .= "<li class='divider'></li>";
        }

        $li .= "</ul></div>";
        
        return $li;
    }
}