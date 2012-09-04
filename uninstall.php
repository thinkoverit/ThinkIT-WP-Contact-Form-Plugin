<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

function lwcf_uninstall_plugin() {
	global $wpdb;

	$lwcf_form_count = get_option("lwcf_form_count");
	
	delete_option( 'lwcf_form_count' );

	if($lwcf_form_count){
		for($i=1; $i <= $lwcf_form_count; $i++){
			$form = array();
			delete_option("lwcf_form_name_".$i);
			delete_option("lwcf_form_email_".$i);
			delete_option("lwcf_form_subject_".$i);
		
			if(!empty($lwcf_form_name)){

				$lwcf_variable_count = get_option("lwcf_variable_count_".$i);;
				delete_option("lwcf_variable_count_".$i);;
				
				$fields = array();
				for($j=1;$j<=$lwcf_variable_count;$j++){
					delete_option("lwcf_label_".$i.$j);
					delete_option("lwcf_field_".$i.$j);
					delete_option("lwcf_class_".$lwcf_form_id.$j);
					delete_option("lwcf_required_".$lwcf_form_id.$j);
				}
			}
		}
	}
}

lwcf_uninstall_plugin();

?>