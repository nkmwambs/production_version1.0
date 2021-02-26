<?php

	// S3 Configuration
	$config['upload_files_to_s3'] = true;// Decaprecated
	$config['s3_region'] = 'eu-west-1';
	$config['s3_bucket_name'] = 'compassion-fcp-fms-stages';
	$config['temp_files_deletion_limit_hours'] = 0.5; // In hours. Use fractional of while or whole number e.g. 1, 2, 2.5

    $config['attachment_table_name'] = 'attachment';
    $config['attachment_key_column'] = 'attachment_primary_id'; 
	$config['encrypt_file'] = false;

