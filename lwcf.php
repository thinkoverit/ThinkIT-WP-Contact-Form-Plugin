<?php
/*
Plugin Name: ThinkIT WP Contact Form Plugin
Plugin URI: http://thinkoverit.com/stoit/
Description: Simple, Easy to manage and very light weight Contact form plugin for Wordpress from ThinkOverIT (www.thinkoverit.com)
Version: 0.1
Author: Pandurang Zambare, pandu@thinkoverit.com
Author URI: http://thinkoverit.com
License: GPL2
*/


// definitions
define( 'TOIT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'TOIT_PLUGIN_NAME', trim( dirname( TOIT_PLUGIN_BASENAME ), '/' ) );
define( 'TOIT_PLUGIN_DIRECTORY', WP_PLUGIN_DIR . '/' . TOIT_PLUGIN_NAME );

define( 'TOIT_CURRENT_VERSION', '0.1' );
define( 'TOIT_LOGPATH', str_replace('\\', '/', WP_CONTENT_DIR).'/toit-logs/');



require_once TOIT_PLUGIN_DIRECTORY . '/functions.php';
require_once TOIT_PLUGIN_DIRECTORY . '/contactform.php';
require_once TOIT_PLUGIN_DIRECTORY . '/elements/textbox.php';
require_once TOIT_PLUGIN_DIRECTORY . '/elements/checkbox.php';


add_action( 'admin_menu', 'toit_create_menu_pages', 9 );

function toit_create_menu_pages() {
	global $toit_current_id;


	if(toit_has_admin_edit_cap() && isset($_POST['toit-delete']) && isset($_POST['toit_current_id'])){

		$toit_current_id = $_POST['toit_current_id'];
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

		register_setting( 'toit-contact-form-group', 'toit_form_count' );
		
		$toit_form_count = $_POST['toit_form_count'];
		$toit_current_id = isset($_POST['toit_current_id']) ? $_POST['toit_current_id'] : 0;


		$toit_form_id = $_POST['toit_form_id'];	

		//Edit or New Form creation
		//For edit $toit_form_id == $toit_current_id
		$toit_variable_count = isset($_POST['toit_variable_count_'.$toit_form_id]) ? $_POST['toit_variable_count_'.$toit_form_id] : 0;
		
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

?>
