<?php
/*
Plugin Name: ThinkIT WP Contact Form Plugin
Plugin URI: http://thinkoverit.com/ThinkIT-WP-Contact-Form-Plugin/
Description: Simple, Easy to manage and very light weight Contact form plugin for Wordpress from ThinkOverIT (www.thinkoverit.com)
Version: 0.1
Author: ThinkOverIT
Author URI: http://thinkoverit.com
License: GPL2
*/


// definitions
define( 'TOIT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'TOIT_PLUGIN_NAME', trim( dirname( TOIT_PLUGIN_BASENAME ), '/' ) );
define( 'TOIT_PLUGIN_TITLE', 'ThinkIT WP Contact Form Plugin');
define( 'TOIT_PLUGIN_DIRECTORY', WP_PLUGIN_DIR . '/' . TOIT_PLUGIN_NAME );

define( 'TOIT_CURRENT_VERSION', '0.1' );
define( 'TOIT_LOGPATH', str_replace('\\', '/', WP_CONTENT_DIR).'/toit-logs/');

if ( ! defined( 'TOIT_LOAD_JS' ) )
	define( 'TOIT_LOAD_JS', true );

if ( ! defined( 'TOIT_LOAD_CSS' ) )
	define( 'TOIT_LOAD_CSS', true );

require_once TOIT_PLUGIN_DIRECTORY . '/functions.php';
require_once TOIT_PLUGIN_DIRECTORY . '/contactform.php';
require_once TOIT_PLUGIN_DIRECTORY . '/elements/textbox.php';
require_once TOIT_PLUGIN_DIRECTORY . '/elements/checkbox.php';
require_once TOIT_PLUGIN_DIRECTORY . '/elements/textarea.php';


add_action( 'admin_menu', 'toitcf_create_menu_pages', 9 );

function toitcf_create_menu_pages() {
	global $toitcf_current_id;

	if(toitcf_has_admin_edit_cap() && isset($_POST['toit-add-update'])){

		$toitcf_current_id = isset($_POST['toitcf_current_id']) ? toitcf_parse_variable($_POST['toitcf_current_id']) : '';
		check_admin_referer( 'toitcf_add_update_' . $toitcf_current_id );
		
		$toitcf_form_email = isset($_POST['toitcf_form_email']) ? toitcf_parse_variable($_POST['toitcf_form_email']) : '';
		$toitcf_form_subject = isset($_POST['toitcf_form_subject']) ? toitcf_parse_variable($_POST['toitcf_form_subject']) : '';
		$toitcf_form_message_top = isset($_POST['toitcf_form_message_top']) ? toitcf_parse_variable($_POST['toitcf_form_message_top']) : '';
		$toitcf_form_message_bottom = isset($_POST['toitcf_form_message_bottom']) ? toitcf_parse_variable($_POST['toitcf_form_message_bottom']) : '';
		
		$toitcf_fields_count = isset($_POST['toitcf_fields_count']) ? toitcf_parse_variable($_POST['toitcf_fields_count']) : 4;

	
		$fields = array();

		for($i=0;$i<=$toitcf_fields_count;$i++){ 
			$label = isset($_POST['toitcf_label'.$i]) ? toitcf_parse_variable($_POST['toitcf_label'.$i]) : '';
			if(empty($label)) continue;
			$field = isset($_POST['toitcf_field'.$i]) ? toitcf_parse_variable($_POST['toitcf_field'.$i]) : '';
			$order = isset($_POST['toitcf_order'.$i]) ? toitcf_parse_variable($_POST['toitcf_order'.$i]) : '';
			$required = isset($_POST['toitcf_required'.$i]) ? toitcf_parse_variable($_POST['toitcf_required'.$i]) : '';
			$fields[] = array("label"=>$label,
								 "field"=>$field,
								 "order"=>$order,
								 "required"=>$required);
								 
		}

		global $wpdb;

		$table_name = get_toitcf_table_name();

		
		$params = array("email"=>$toitcf_form_email,
						"subject"=>$toitcf_form_subject,
						"top_message"=>$toitcf_form_message_top,
						"bottom_message"=>$toitcf_form_message_bottom,
						"fields"=>serialize($fields));

		if(empty($toitcf_current_id)){

			$result = $wpdb->insert( $table_name, $params );

			if ( $result ) {
				$new_form_id = $wpdb->insert_id;
				$redirect_to = toitcf_admin_url( array( 'message' => 'created', "toitcf_current_id" => $new_form_id ) );
			} else {
				$redirect_to = toitcf_admin_url( array( 'message' => 'create-failed'));
			}
			wp_redirect( $redirect_to );

		} else { // Update

			$result = $wpdb->update( $table_name, $params,
				array( 'form_id' => absint( $toitcf_current_id ) ) );

			if ( $result ) {
				$redirect_to = toitcf_admin_url( array( 'message' => 'updated', "toitcf_current_id" => $toitcf_current_id ) );
			} else {
				$redirect_to = toitcf_admin_url( array( 'message' => 'update-failed', "toitcf_current_id" => $toitcf_current_id ) );
			}
			wp_redirect( $redirect_to );
		}
	}else if(toitcf_has_admin_edit_cap() && isset($_GET['action']) && $_GET['action'] == "delete" &&isset($_GET['toitcf_current_id'])){

		$toitcf_current_id = $_GET['toitcf_current_id'];
		delete_contact_form($toitcf_current_id);
		$redirect_to = toitcf_admin_url( array( 'message' => 'deleted' ) );
		wp_redirect( $redirect_to );
	}


	add_menu_page( __( 'ThinkIT Contact', 'toitcf' ), __( 'ThinkIT Contact', 'toitcf' ),
		'edit_posts', 'toitcf', 'toitcf_admin_page_render' );

}

function toitcf_admin_page_render()
{
	global $toitcf_current_id;
	if(isset($_GET['toitcf_current_id'])) 
		$toitcf_current_id = $_GET['toitcf_current_id'];

	require_once TOIT_PLUGIN_DIRECTORY . '/lwcf_settings_page.php';
}
add_action( 'admin_init', 'toitcf_register_settings' );

function toitcf_register_settings() {

	
	if(toitcf_has_admin_edit_cap() && isset($_POST['toit-add-update']) && isset($_POST['toitcf_form_count']) && isset($_POST['toitcf_form_id'])){

		$toitcf_form_count = $_POST['toitcf_form_count'];
		$toitcf_current_id = isset($_POST['toitcf_current_id']) ? $_POST['toitcf_current_id'] : 0;

		$toitcf_form_id = isset($_POST['toitcf_form_id']) ? $_POST['toitcf_form_id'] : 0;	

		$validated = true;
		if($toitcf_form_id){
			$toitcf_form_name = isset($_POST['toitcf_form_name_'.$toitcf_form_id]) ? $_POST['toitcf_form_name_'.$toitcf_form_id] : '';
			$toitcf_form_email = isset($_POST['toitcf_form_email_'.$toitcf_form_id]) ? $_POST['toitcf_form_email_'.$toitcf_form_id] : '';
			
			if (!filter_var($toitcf_form_email, FILTER_VALIDATE_EMAIL)) {
				global $toitcf_notification;
				$toitcf_notification = "Please add a valid recipient Email ID.";
				$validated = false;
			}
			if(empty($toitcf_form_name)){
				global $toitcf_notification;
				$toitcf_notification = "Please add a form name to identify.";
				$validated = false;
			}
		}
		if($validated){
			//Edit or New Form creation
			//For edit $toitcf_form_id == $toitcf_current_id
			$toitcf_variable_count = isset($_POST['toitcf_variable_count_'.$toitcf_form_id]) ? $_POST['toitcf_variable_count_'.$toitcf_form_id] : 0;
			
			register_setting( 'toit-contact-form-group', 'toitcf_form_count' );
			register_setting( 'toit-contact-form-group', 'toitcf_variable_count_'.$toitcf_form_id);
			register_setting( 'toit-contact-form-group', 'toitcf_form_name_'.$toitcf_form_id);
			register_setting( 'toit-contact-form-group', 'toitcf_form_email_'.$toitcf_form_id);
			register_setting( 'toit-contact-form-group', 'toitcf_form_subject_'.$toitcf_form_id);
			for($i=1;$i<=$toitcf_variable_count;$i++){
				register_setting( 'toit-contact-form-group', 'toitcf_label_'.$toitcf_form_id.$i );
				register_setting( 'toit-contact-form-group', 'toitcf_field_'.$toitcf_form_id.$i );
				register_setting( 'toit-contact-form-group', 'toitcf_class_'.$toitcf_form_id.$i );
				register_setting( 'toit-contact-form-group', 'toitcf_required_'.$toitcf_form_id.$i );
			}
		}
	}
}

add_shortcode( 'thinkit-wp-contact-form', 'toitcf_contact_form_short_tag' );

function toitcf_contact_form_short_tag( $atts ) {
	global $toitcf_contact_form;

	if ( is_feed() )
		return '[thinkit-wp-contact-form]';

	$atts = (array) $atts;

	$id = (int) array_shift( $atts );

	$form = get_contact_form($id);

	if(!empty($form)){
		
		if(!is_a( $toitcf_contact_form, 'TOIT_ContactForm' ) 
		   || $toitcf_contact_form->getID() != $id ){
				
			$toitcf_contact_form = new TOIT_ContactForm($id);
		}
		
		$form = $toitcf_contact_form->render_html();
		return $form;
	}
	return 'Contact form Not Found';
}

add_action( 'init', 'toitcf_init_form_handling', 11 );

function toitcf_init_form_handling() {

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['toitcf_ajax_call']) && 1 == (int) $_POST['toitcf_ajax_call'] ) {
		toitcf_handle_ajax_submitting();
		exit();
	} elseif ( isset( $_POST['toit-form-id'])  && isset( $_POST['toit-form-tag'])) {
		toitcf_handle_nonajax_submitting();
	}
}

function toitcf_handle_nonajax_submitting() {
	global $toitcf_contact_form;

	if ( ! isset($_POST['toit-form-id'] ) )
		return;

	$id = (int) $_POST['toit-form-id'];

	$toitcf_contact_form = new TOIT_ContactForm($id);

	if ( $toitcf_contact_form->is_submitted()) {
		
		$validation = $toitcf_contact_form->validate();

		if ( $toitcf_contact_form->validation_success && empty($toitcf_contact_form->validation_errors) ) {
			$toitcf_contact_form->send_mail();
		}
	}
}

function toitcf_handle_ajax_submitting(){
	global $esc_form;

	if ( ! isset($_POST['toit-form-id'] ) )
		return;

	$id = (int) $_POST['toit-form-id'];

	$toit_form = new TOIT_ContactForm($id);

	if ( $toit_form->is_submitted()) {
		
		$validation = $toit_form->validate();
		$msg= '';

		if ( $toit_form->validation_success ) {
			$toit_form->send_mail();
			$msg .= $toit_form->get_after_submit_notifications();
			echo json_encode(array("success"=>"1", "message"=>$msg));
			return;
		}else{
			$msg= array();
			foreach($toit_form->validation_errors as $element=>$error){
				$msg[] = array("name"=>$element, "error"=>$error);
			}
			echo json_encode(array("success"=>"0","message"=>$msg));
			return;
		}
	}
	echo json_encode(array("success"=>"0","message"=>"Something went wrong."));
}


register_activation_hook(__FILE__,'toitcf_install');

function toitcf_install() {
	global $wpdb;

	if ( check_toitcf_table_exists() )
		return; //  already exists

	$table_name = get_toitcf_table_name();

	$charset_collate = '';
	if ( $wpdb->has_cap( 'collation' ) ) {
		if ( ! empty( $wpdb->charset ) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty( $wpdb->collate ) )
			$charset_collate .= " COLLATE $wpdb->collate";
	}

	$ret = $wpdb->query( "CREATE TABLE IF NOT EXISTS $table_name (
			form_id bigint(20) unsigned NOT NULL auto_increment,
			email varchar(140) NOT NULL default '',
			subject varchar(140) NOT NULL default '',
			top_message text default '',
			bottom_message text default '',
			fields text NOT NULL,
			PRIMARY KEY (form_id)) $charset_collate;" );

	add_option("thinkit_contact_form_version", "0.1");
	
	if ( $ret === false )
		return false;

	return TRUE;
}


if ( TOIT_LOAD_JS )
	add_action( 'wp_print_scripts', 'toitcf_enqueue_scripts' );

function toitcf_enqueue_scripts() {
	$in_footer = true;
	if ( 'header' === TOIT_LOAD_JS )
		$in_footer = false;

	wp_enqueue_script( 'toit-wp-contact-form', toitcf_plugin_url( 'toit-script.js' ),
		array( 'jquery', 'jquery-form' ), ESC_CURRENT_VERSION, $in_footer );
}

if ( TOIT_LOAD_CSS )
	add_action( 'wp_print_styles', 'toitcf_enqueue_styles' );

function toitcf_enqueue_styles() {
	wp_enqueue_style( 'toit-wp-contact-form', toitcf_plugin_url( 'styles.css' ),
		array(), ESC_CURRENT_VERSION, 'all' );

	if ( 'rtl' == get_bloginfo( 'text_direction' ) ) {
		wp_enqueue_style( 'toit-wp-contact-form-rtl', toitcf_plugin_url( 'styles-rtl.css' ),
			array(), ESC_CURRENT_VERSION, 'all' );
	}
}
?>
