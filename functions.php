<?php
function toit_has_admin_edit_cap() {
	return current_user_can( 'edit_posts' );
}
function get_all_contact_forms(){
	
	$toit_form_count = get_option("toit_form_count");
	
	$toitforms = '';

	for($i=1; $i <= $toit_form_count; $i++){
		$form = array();
		$toit_form_name = get_option("toit_form_name_".$i);
	
		if(!empty($toit_form_name)){
			$form['toit_form_id'] = $i;
			$form['toit_form_name'] = $toit_form_name;
			$form['toit_form_email'] = get_option("toit_form_email_".$i);
			$form['toit_form_subject'] = get_option("toit_form_subject_".$i);
			$form['toit_variable_count'] = get_option("toit_variable_count_".$i);;
			
			$fields = array();
			for($j=1;$j<=$form['toit_variable_count'];$j++){
				$toit_label = get_option("toit_label_".$i.$j);
				$toit_field = get_option("toit_field_".$i.$j);
				$toit_class = get_option("toit_class_".$toit_form_id.$j);
				$toit_required = get_option("toit_required_".$toit_form_id.$j);

				$fields[] = array("toit_label"=>$toit_label, 
								  "toit_field"=>$toit_field,
								  "toit_class"=>$toit_class,
								  "toit_required"=>$toit_required,
								  "toit_name"=>$toit_field.$i.$j
								  );
				}
			$form['fields'] = $fields;
		}
		$toitforms[] = $form;
	}
	return $toitforms;

}
function get_contact_form($toit_form_id){
	

	$form = array();
	$toit_form_name = get_option("toit_form_name_".$toit_form_id);


	if(!empty($toit_form_name)){
		$form['toit_form_id'] = $toit_form_id;
		$form['toit_form_name'] = $toit_form_name;
		$form['toit_form_email'] = get_option("toit_form_email_".$toit_form_id);
		$form['toit_form_subject'] = get_option("toit_form_subject_".$toit_form_id);
		$form['toit_variable_count'] = get_option("toit_variable_count_".$toit_form_id);;

		$fields = array();
		for($j=1;$j<=$form['toit_variable_count'];$j++){
			$toit_label = get_option("toit_label_".$toit_form_id.$j);
			$toit_field = get_option("toit_field_".$toit_form_id.$j);
			$toit_class = get_option("toit_class_".$toit_form_id.$j);
			$toit_required = get_option("toit_required_".$toit_form_id.$j);
			$fields[] = array("toit_label"=>$toit_label, 
							  "toit_field"=>$toit_field,
							  "toit_class"=>$toit_class,
							  "toit_required"=>$toit_required,
							  "toit_name"=>$toit_field.$i.$j
							  );
		}
		$form['fields'] = $fields;
	}
	return $form;
}
function delete_contact_form($toit_form_id){

	$form = array();
	$toit_form_name = get_option("toit_form_name_".$toit_form_id);
	delete_option( "toit_form_name_".$toit_form_id );
	delete_option( "toit_form_email_".$toit_form_id );
	delete_option("toit_form_subject_".$toit_form_id);


	if(!empty($toit_form_name)){

		$toit_variable_count = get_option("toit_variable_count_".$toit_form_id);;
		delete_option( "toit_variable_count_".$toit_form_id );

		$fields = array();
		for($j=1;$j<=$toit_variable_count;$j++){
			delete_option("toit_label_".$toit_form_id.$j);
			delete_option("toit_field_".$toit_form_id.$j);
			delete_option("toit_class_".$toit_form_id.$j);
			delete_option("toit_required_".$toit_form_id.$j);
		}
	}
	return true;
}
function toit_admin_url( $query = array() ) {
	global $plugin_page;

	if ( ! isset( $query['page'] ) )
		$query['page'] = $plugin_page;
	$path = 'admin.php';
	if ( $query = build_query( $query ) )
		$path .= '?' . $query;
	$url = admin_url( $path );
	return esc_url_raw( $url );
}

function get_current_url(){

	return esc_url_raw($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
}
function toit_plugin_url( $path = '' ) {
	return plugins_url( $path, TOIT_PLUGIN_BASENAME );
}
function parse_variable($var){
	if(!empty($var)){
		$var = trim($var);
		$var = strip_tags($var);
		
		return $var;
	}
	return '';
}
function isValidURL($url)
{
	return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}
function get_last_added_form_id(){
	return get_option("toit_form_count");
}
function toit_admin_show_message() {
	global $esc_notification;

	if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == true ||
		isset($_GET['updated']) && $_GET['updated'] == true){
		if(isset($_GET['toit_current_id']))
			$message = __( "Contact form updated.", 'toit' );
		else{
			$msg = ' [thinkit-wp-contact-form '.get_last_added_form_id().'] Please add this code in post,page where you want to place this form.';
			$message = __( "Contact form saved.".$msg, 'toit' );
		}
	}else if(isset($_GET['message']) && $_GET['message'] == 'deleted')
		$message = __( "Contact form is deleted", 'toit' );
		
	if(isset($esc_notification) && !empty($esc_notification))
		$message .= $esc_notification;		
	if ( ! $message )
		return;

?>
<div id="message" class="updated fade"><p><?php echo esc_html( $message ); ?></p></div>
<?php
}
?>
