<?php
	$msg = get_phrase('status')." : ".date("F Y",$tym)." ".get_phrase('financial_report_not_submitted');
	$color = "style='color:red;'";
	
    if($is_mfr_submitted){
		$msg = get_phrase('status')." : ".date("F Y",$tym)." ".get_phrase('financial_report_submitted');
		$color = "style='color:green;'";
	}	
?>

<div class="col-sm-offset-3 col-sm-6 well well-sm"><h4 <?=$color;?>><?=$msg;?></h3></div>
									