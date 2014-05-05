<?php
function toitcf_has_admin_edit_cap() {
	return current_user_can( 'edit_posts' );
}
function get_all_contact_forms(){
	
	global $wpdb; 
	$table_name = get_toitcf_table_name();
		
	$ret = $wpdb->query( " SELECT * FROM $table_name " );
			
	$form = '';
	if($ret){
		foreach($wpdb->last_result as $key=>$row){
		
			
			$form[$key]['form_id'] = $row->form_id;
			$form[$key]['email'] = $row->email;
			$form[$key]['subject'] = $row->subject;
			$form[$key]['top_message'] = $row->top_message;
			$form[$key]['bottom_message'] = $row->bottom_message;
			$form[$key]['fields'] = maybe_unserialize($row->form_fields);

		}
	}
	return $form;
}
function get_contact_form($toitcf_form_id){
	global $wpdb; 
	$form = array();
	
	$table_name = get_toitcf_table_name();
	$ret = $wpdb->query( $wpdb->prepare(" SELECT * FROM $table_name WHERE form_id =%d", $toitcf_form_id) );
		
	if($ret){
		foreach($wpdb->last_result as $row){
			$form['form_id'] = $row->form_id;
			$form['email'] = $row->email;
			$form['subject'] = $row->subject;
			$form['top_message'] = $row->top_message;
			$form['bottom_message'] = $row->bottom_message;
			$form['fields'] = maybe_unserialize($row->form_fields);
		}
	}
	return $form;
}
function delete_contact_form($toitcf_form_id){
	global $wpdb; 
	
	$table_name = get_toitcf_table_name();
	$wpdb->query( $wpdb->prepare(" DELETE FROM $table_name WHERE form_id =%d", $toitcf_form_id) );
	return true;
}
function toitcf_admin_url( $query = array() ) {
	global $plugin_page;

	if ( ! isset( $query['page'] ) )
		$query['page'] = $plugin_page;
	$path = 'admin.php';
	if ( $query = build_query( $query ) )
		$path .= '?' . $query;
	$url = admin_url( $path );
	return esc_url_raw( $url );
}
function usort_cmp($a, $b)
{
    return $a['order']-$b['order'];
}
function get_current_url(){

	return esc_url_raw($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
}
function toitcf_plugin_url( $path = '' ) {
	return plugins_url( $path, TOIT_PLUGIN_BASENAME );
}
function toitcf_parse_variable($var){
	if(!empty($var)){
		$var = trim($var);
		$var = strip_tags($var);
		
		return $var;
	}
	return '';
}
function toitcf_isValidURL($url)
{
	return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}
function toitcf_encode_safe($label){
	return preg_replace("/[^a-zA-Z]+/", "", $label);
}
function get_toitcf_table_name() {
	global $wpdb;

	return $wpdb->prefix . "thinkit_contact_form";
}
function check_toitcf_table_exists() {
	global $wpdb;

	$table_name = get_toitcf_table_name();

	return strtolower( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) ) == strtolower( $table_name );
}
function toitcf_admin_show_message() {
	global $esc_notification;
		
	$message= '';
	if(isset($esc_notification) && !empty($esc_notification))
		$message = $esc_notification;
	else if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == true ||
		isset($_GET['updated']) && $_GET['updated'] == true){
		if(isset($_GET['toitcf_current_id']))
			$message = __( "Contact form updated.", 'toit' );
		else{
			$msg = ' [thinkit-wp-contact-form '.get_last_added_form_id().'] Please add this code in post,page where you want to place this form.';
			$message = __( "Contact form saved.".$msg, 'toit' );
		}
	}else if(isset($_GET['message']) && $_GET['message'] == 'deleted')
		$message = __( "Contact form is deleted", 'toit' );
		
	if ( ! $message )
		return;

?>
<div id="message" class="updated fade"><p><?php echo esc_html( $message ); ?></p></div>
<?php
}
?>
