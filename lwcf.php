<?php
/*
Plugin Name: ThinkIT WP Contact Form Plugin
Plugin URI: http://thinkoverit.com/stoit/
Dtoitription: Simple, Easy to manage and very light weight Contact form plugin for Wordpress from ThinkOverIT (www.thinkoverit.com)
Version: 0.1
Author: Pandurang Zambare, pandu@thinkoverit.com
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


add_action( 'admin_menu', 'toit_create_menu_pages', 9 );

function toit_create_menu_pages() {
	global $toit_current_id;

	if(toit_has_admin_edit_cap() && isset($_POST['toit-add-update'])){

		$toit_current_id = isset($_POST['toit_current_id']) ? toit_parse_variable($_POST['toit_current_id']) : '';
		check_admin_referer( 'toit_add_update_' . $toit_current_id );
		
		$toit_form_email = isset($_POST['toit_form_email']) ? toit_parse_variable($_POST['toit_form_email']) : '';
		$toit_form_subject = isset($_POST['toit_form_subject']) ? toit_parse_variable($_POST['toit_form_subject']) : '';
		$toit_form_message_top = isset($_POST['toit_form_message_top']) ? toit_parse_variable($_POST['toit_form_message_top']) : '';
		$toit_form_message_bottom = isset($_POST['toit_form_message_bottom']) ? toit_parse_variable($_POST['toit_form_message_bottom']) : '';
		
		$toit_fields_count = isset($_POST['toit_fields_count']) ? toit_parse_variable($_POST['toit_fields_count']) : 4;

	
		$fields = array();

		for($i=0;$i<=$toit_fields_count;$i++){ 
			$label = isset($_POST['toit_label'.$i]) ? toit_parse_variable($_POST['toit_label'.$i]) : '';
			if(empty($label)) continue;
			$field = isset($_POST['toit_field'.$i]) ? toit_parse_variable($_POST['toit_field'.$i]) : '';
			$order = isset($_POST['toit_order'.$i]) ? toit_parse_variable($_POST['toit_order'.$i]) : '';
			$required = isset($_POST['toit_required'.$i]) ? toit_parse_variable($_POST['toit_required'.$i]) : '';
			$fields[] = array("label"=>$label,
								 "field"=>$field,
								 "order"=>$order,
								 "required"=>$required);
								 
		}

		global $wpdb;

		$table_name = get_toit_table_name();

		
		$params = array("email"=>$toit_form_email,
						"subject"=>$toit_form_subject,
						"top_message"=>$toit_form_message_top,
						"bottom_message"=>$toit_form_message_bottom,
						"fields"=>serialize($fields));

		if(empty($toit_current_id)){

			$result = $wpdb->insert( $table_name, $params );

			if ( $result ) {
				$new_form_id = $wpdb->insert_id;
				$redirect_to = toit_admin_url( array( 'message' => 'created', "toit_current_id" => $new_form_id ) );
			} else {
				$redirect_to = toit_admin_url( array( 'message' => 'create-failed'));
			}
			wp_redirect( $redirect_to );

		} else { // Update

			$result = $wpdb->update( $table_name, $params,
				array( 'form_id' => absint( $toit_current_id ) ) );

			if ( $result ) {
				$redirect_to = toit_admin_url( array( 'message' => 'updated', "toit_current_id" => $toit_current_id ) );
			} else {
				$redirect_to = toit_admin_url( array( 'message' => 'update-failed', "toit_current_id" => $toit_current_id ) );
			}
			wp_redirect( $redirect_to );
		}
	}else if(toit_has_admin_edit_cap() && isset($_GET['action']) && $_GET['action'] == "delete" &&isset($_GET['toit_current_id'])){

		$toit_current_id = $_GET['toit_current_id'];
		delete_contact_form($toit_current_id);
		$redirect_to = toit_admin_url( array( 'message' => 'deleted' ) );
		wp_redirect( $redirect_to );
	}


	add_menu_page( __( 'ThinkIT Contact', 'toit' ), __( 'ThinkIT Contact', 'toit' ),
		'edit_posts', 'toit', 'toit_admin_page_render' );

}

function toit_admin_page_render()
{
	global $toit_current_id;
	if(isset($_GET['toit_current_id'])) 
		$toit_current_id = $_GET['toit_current_id'];

	require_once TOIT_PLUGIN_DIRECTORY . '/lwcf_settings_page.php';
}
add_action( 'admin_init', 'toit_register_settings' );

function toit_register_settings() {

	
	if(toit_has_admin_edit_cap() && isset($_POST['toit-add-update']) && isset($_POST['toit_form_count']) && isset($_POST['toit_form_id'])){

		$toit_form_count = $_POST['toit_form_count'];
		$toit_current_id = isset($_POST['toit_current_id']) ? $_POST['toit_current_id'] : 0;

		$toit_form_id = isset($_POST['toit_form_id']) ? $_POST['toit_form_id'] : 0;	

		$validated = true;
		if($toit_form_id){
			$toit_form_name = isset($_POST['toit_form_name_'.$toit_form_id]) ? $_POST['toit_form_name_'.$toit_form_id] : '';
			$toit_form_email = isset($_POST['toit_form_email_'.$toit_form_id]) ? $_POST['toit_form_email_'.$toit_form_id] : '';
			
			if (!filter_var($toit_form_email, FILTER_VALIDATE_EMAIL)) {
				global $toit_notification;
				$toit_notification = "Please add a valid recipient Email ID.";
				$validated = false;
			}
			if(empty($toit_form_name)){
				global $toit_notification;
				$toit_notification = "Please add a form name to identify.";
				$validated = false;
			}
		}
		if($validated){
			//Edit or New Form creation
			//For edit $toit_form_id == $toit_current_id
			$toit_variable_count = isset($_POST['toit_variable_count_'.$toit_form_id]) ? $_POST['toit_variable_count_'.$toit_form_id] : 0;
			
			register_setting( 'toit-contact-form-group', 'toit_form_count' );
			register_setting( 'toit-contact-form-group', 'toit_variable_count_'.$toit_form_id);
			register_setting( 'toit-contact-form-group', 'toit_form_name_'.$toit_form_id);
			register_setting( 'toit-contact-form-group', 'toit_form_email_'.$toit_form_id);
			register_setting( 'toit-contact-form-group', 'toit_form_subject_'.$toit_form_id);
			for($i=1;$i<=$toit_variable_count;$i++){
				register_setting( 'toit-contact-form-group', 'toit_label_'.$toit_form_id.$i );
				register_setting( 'toit-contact-form-group', 'toit_field_'.$toit_form_id.$i );
				register_setting( 'toit-contact-form-group', 'toit_class_'.$toit_form_id.$i );
				register_setting( 'toit-contact-form-group', 'toit_required_'.$toit_form_id.$i );
			}
		}
	}
}

add_shortcode( 'thinkit-wp-contact-form', 'toit_contact_form_short_tag' );

function toit_contact_form_short_tag( $atts ) {
	global $toit_contact_form;

	if ( is_feed() )
		return '[thinkit-wp-contact-form]';

	$atts = (array) $atts;

	$id = (int) array_shift( $atts );

	$form = get_contact_form($id);

	if(!empty($form)){
		
		if(!is_a( $toit_contact_form, 'TOIT_ContactForm' ) 
		   || $toit_contact_form->getID() != $id ){
				
			$toit_contact_form = new TOIT_ContactForm($id);
		}
		
		$form = $toit_contact_form->render_html();
		return $form;
	}
	return 'Contact form Not Found';
}

add_action( 'init', 'toit_init_form_handling', 11 );

function toit_init_form_handling() {

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['toit_ajax_call']) && 1 == (int) $_POST['toit_ajax_call'] ) {
		toit_handle_ajax_submitting();
		exit();
	} elseif ( isset( $_POST['toit-form-id'])  && isset( $_POST['toit-form-tag'])) {
		toit_handle_nonajax_submitting();
	}
}

function toit_handle_nonajax_submitting() {
	global $toit_contact_form;

	if ( ! isset($_POST['toit-form-id'] ) )
		return;

	$id = (int) $_POST['toit-form-id'];

	$toit_contact_form = new TOIT_ContactForm($id);

	if ( $toit_contact_form->is_submitted()) {
		
		$validation = $toit_contact_form->validate();

		if ( $toit_contact_form->validation_success && empty($toit_contact_form->validation_errors) ) {
			$toit_contact_form->send_mail();
		}
	}
}

function toit_handle_ajax_submitting(){
}


register_activation_hook(__FILE__,'toit_install');

function toit_install() {
	global $wpdb;

	if ( check_toit_table_exists() )
		return; //  already exists

	$table_name = get_toit_table_name();

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

/*
if ( TOIT_LOAD_JS )
	add_action( 'wp_print_scripts', 'toit_enqueue_scripts' );

function toit_enqueue_scripts() {
	$in_footer = true;
	if ( 'header' === TOIT_LOAD_JS )
		$in_footer = false;

	wp_enqueue_script( 'toit-wp-contact-form', toit_plugin_url( 'toit-script.js' ),
		array( 'jquery', 'jquery-form' ), ESC_CURRENT_VERSION, $in_footer );
}
*/
if ( TOIT_LOAD_CSS )
	add_action( 'wp_print_styles', 'toit_enqueue_styles' );

function toit_enqueue_styles() {
	wp_enqueue_style( 'toit-wp-contact-form', toit_plugin_url( 'styles.css' ),
		array(), ESC_CURRENT_VERSION, 'all' );

	if ( 'rtl' == get_bloginfo( 'text_direction' ) ) {
		wp_enqueue_style( 'toit-wp-contact-form-rtl', toit_plugin_url( 'styles-rtl.css' ),
			array(), ESC_CURRENT_VERSION, 'all' );
	}
}
?>
