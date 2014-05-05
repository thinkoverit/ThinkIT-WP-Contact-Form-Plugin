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

define( 'TOIT_CURRENT_VERSION', '0.2' );
define( 'TOIT_LOGPATH', str_replace('\\', '/', WP_CONTENT_DIR).'/toit-logs/');


require_once TOIT_PLUGIN_DIRECTORY . '/functions.php';
require_once TOIT_PLUGIN_DIRECTORY . '/contactform.php';
require_once TOIT_PLUGIN_DIRECTORY . '/elements/textbox.php';
require_once TOIT_PLUGIN_DIRECTORY . '/elements/checkbox.php';
require_once TOIT_PLUGIN_DIRECTORY . '/elements/textarea.php';
require_once TOIT_PLUGIN_DIRECTORY . '/elements/captcha.php';


add_action( 'admin_menu', 'toitcf_create_menu_pages', 9 );

function toitcf_create_menu_pages() {
	global $toitcf_current_id;

	if(toitcf_has_admin_edit_cap() && isset($_POST['toit-add-update']) ){


  check_admin_referer( 'toitcf_nonce_action_update', 'toitcf_nonce_update');
 
		$toitcf_current_id = isset($_POST['toitcf_current_id']) ? toitcf_parse_variable($_POST['toitcf_current_id']) : '';
		
		$toitcf_form_email = isset($_POST['toitcf_form_email']) ? toitcf_parse_variable($_POST['toitcf_form_email']) : '';
		$toitcf_form_subject = isset($_POST['toitcf_form_subject']) ? toitcf_parse_variable($_POST['toitcf_form_subject']) : '';
		$toitcf_form_message_top = isset($_POST['toitcf_form_message_top']) ? toitcf_parse_variable($_POST['toitcf_form_message_top']) : '';
		$toitcf_form_message_bottom = isset($_POST['toitcf_form_message_bottom']) ? toitcf_parse_variable($_POST['toitcf_form_message_bottom']) : '';
		
		$toitcf_fields_count = isset($_POST['toitcf_fields_count']) ? toitcf_parse_variable($_POST['toitcf_fields_count']) : 5;

		$emails = explode(",", $toitcf_form_email);
		foreach($emails as $email){
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				global $toitcf_notification;
				$toitcf_notification = "Please add a valid recipient Email ID.";
				return;
			}
		}
			
			
		$fields = array();

		for($i=1;$i<=$toitcf_fields_count;$i++){ 
			$label = isset($_POST['toitcf_label'.$i]) ? toitcf_parse_variable($_POST['toitcf_label'.$i]) : '';
			$field = isset($_POST['toitcf_field'.$i]) ? toitcf_parse_variable($_POST['toitcf_field'.$i]) : '';
   if(empty($field)) continue;
			$order = isset($_POST['toitcf_order'.$i]) ? toitcf_parse_variable($_POST['toitcf_order'.$i]) : '';
			$class = isset($_POST['toitcf_class'.$i]) ? toitcf_parse_variable($_POST['toitcf_class'.$i]) : '';
			$placeholder = isset($_POST['toitcf_placeholder'.$i]) ? toitcf_parse_variable($_POST['toitcf_placeholder'.$i]) : '';
			$required = isset($_POST['toitcf_required'.$i]) ? toitcf_parse_variable($_POST['toitcf_required'.$i]) : '';
			$fields[] = array("label"=>$label,
								 "field"=>$field,
								 "order"=>$order,
								 "required"=>$required,
								 "class"=>$class,
								 "placeholder"=>$placeholder);
								 
		}

		global $wpdb;

		$table_name = get_toitcf_table_name();

		$params = array("email"=>$toitcf_form_email,
						"subject"=>$toitcf_form_subject,
						"top_message"=>$toitcf_form_message_top,
						"bottom_message"=>$toitcf_form_message_bottom,
						"form_fields"=>serialize($fields));

		if(empty($toitcf_current_id)){

			$result = $wpdb->insert( $table_name, $params );

			if ( $result ) {
				$new_form_id = $wpdb->insert_id;
				$redirect_to = toitcf_admin_url( array( 'message' => 'created'));
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
	if(isset($_GET['toitcf_current_id']) && is_numeric($_GET['toitcf_current_id'])) 
		$toitcf_current_id = $_GET['toitcf_current_id'];

	require_once TOIT_PLUGIN_DIRECTORY . '/lwcf_settings_page.php';
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

  update_option('thinkit_contact_form_version', TOIT_CURRENT_VERSION);
  update_option('thinkit_recptacha_key', '6Lc3GuYSAAAAABPUBoHAA23uC-qrCq5jfAbnq12Z');

	if ( check_toitcf_table_exists() )
		return; //  already exists

	$table_name = get_toitcf_table_name();

	$ret = $wpdb->query( "CREATE TABLE IF NOT EXISTS $table_name (
			form_id bigint(20) unsigned NOT NULL auto_increment,
			email varchar(140) NOT NULL default '',
			subject varchar(140) NOT NULL default '',
			top_message text default '',
			bottom_message text default '',
			form_fields text NOT NULL,
			PRIMARY KEY (form_id));" );

	
	if ( $ret === false )
		return false;

	return TRUE;
}

add_action( 'wp_enqueue_scripts', 'toitcf_enqueue_scripts' );

function toitcf_enqueue_scripts() {


	wp_enqueue_script( 'toit-wp-contact-form', toitcf_plugin_url( 'toit-script.js' ),
		array( 'jquery'), TOIT_CURRENT_VERSION);
  
  
	wp_enqueue_script( 'google-recaptcha_ajax', 'http://www.google.com/recaptcha/api/js/recaptcha_ajax.js');
  
	wp_enqueue_style( 'toit-wp-contact-form', toitcf_plugin_url( 'styles.css' ),
		array(), TOIT_CURRENT_VERSION, 'all' );

	if ( 'rtl' == get_bloginfo( 'text_direction' ) ) {
		wp_enqueue_style( 'toit-wp-contact-form-rtl', toitcf_plugin_url( 'styles-rtl.css' ),
			array(), TOIT_CURRENT_VERSION, 'all' );
	}
}
?>
