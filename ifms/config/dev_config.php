<?php
$config['table_prefix']='';
$config['environment']='prod';//prod or test
$config['allowed_uncleared_days'] = 30;
$config['fcp_test_loops'] = 0;
$config['dashboard_runs_start_date'] = '2020-02-01';
$config['fcp_threshold_for_dashboard_run'] = 300;
$config['mfr_review_notification_response_time'] = 24; // Time in hours
$config['use_test_recipient_id_mfr_review_notification'] =  0; // e.g. Nicodemus is 371 user id
$config['show_when_null'] = "Not Set";

//$config['redirect_base_url'] = 'http://localhost/toolscke/';

// Voucher configurations
$config['use_dct_detail_row'] = true;
$config['max_voucher_row_uploaded_files_size'] = 5000000; // In KB
$config['max_voucher_row_upload_file_count'] = 5; 
$config['max_voucher_upload_files_size'] = 10000000; // In KB