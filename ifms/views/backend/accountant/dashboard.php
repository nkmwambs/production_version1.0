<?php 

$grid_array = $this->dashboard_model->display_dashboard(date('Y-m-t',$month));

$none_requested_params = isset($grid_array['parameters']['no']) ? $grid_array['parameters']['no'] : array();

$requested_params = isset($grid_array['parameters']['yes']) ? $grid_array['parameters']['yes'] : array();

print_r($grid_array);

?>

<hr/>
