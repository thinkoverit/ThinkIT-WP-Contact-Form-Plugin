<?php

class TOIT_ContactForm {

	public $id;
	private $form_tag;
	private $form_elements;
	private $element_values;
	private $email;
	private $subject;
	private $top_message;
	private $bottom_message;
	public $validation_errors = array();
	public $validation_success = false;
	public $email_sent = false;

	function __construct($id){
		$this->id = $id;
		$this->form_tag = 'toit-form-' . $id;
		
		$form = get_contact_form($id);
		
		$this->email = $form['email'];
		$this->subject = $form['subject'];
		$this->top_message = $form['top_message'];
		$this->bottom_message = $form['bottom_message'];
		
		$fields = $form['fields'];
		$this->form_elements = usort($fields, "usort_cmp");
		$this->form_elements = $fields;

		if($this->is_submitted()){
			foreach($this->form_elements as $element){
				$name = toitcf_encode_safe($element['label']);
				if($element['field'] == 'button') continue;
				$this->element_values[$name] = toitcf_parse_variable($_POST[$name]);
			}
		}
	}
	public function getID(){
		return $this->id;
	}
	public function get_element_value( $name ) {
		if(isset($this->element_values[$name])){
			return $this->element_values[$name];
		}
	}
	public function is_submitted() {

		if (isset( $_POST['toit-form-tag'] ) && $this->form_tag == $_POST['toit-form-tag'])
			return true;

		return false;
	}
	/* Generating Form HTML */

	public function render_html() {
		$form = '<div class="toit-form" id="' . $this->form_tag . '">';

		$url = get_current_url();

		$form .= '<form action="" method="post" class="toit-form">' . "\n";
		$form .= '<input type="hidden" name="toit-form-id" value="'. esc_attr( $this->id ) . '" />' . "\n";
		$form .= '<input type="hidden" name="toit-form-tag" value="'. esc_attr( $this->form_tag ) . '" />' . "\n";
		
		$form .= $this->_render_elements();


		$form .= '</form>';
		
		$form .= '<div class="toitcf-ajax-result">';
		$form .= $this->get_after_submit_notifications();
		$form .= '</div>';

		$form .= '</div>';

		return $form;
	}

	public function get_after_submit_notifications() {
		if($this->is_submitted() && $this->validation_success){
			if($this->email_sent)
				return  '<div class="toit-success">'.__('Mail sent successfully to Adminitrator').'</div>';
			else
				return  '<div class="toit-error">'.__('Email failed. Please contact system administrator to rectify.').'</div>';
		}
	}

	private function _render_elements() {
		$html = '';

		foreach($this->form_elements as $element){

			switch($element['field']){
				case "textbox":
				case "email":
				case "url":
					$txt = new Textbox($this, $element);
					$html .= $txt->render_html(); 
				break;
				case "textarea":
					$txta = new TextArea($this, $element);
					$html .= $txta->render_html(); 
				break;
				case "checkbox":
					$txt = new CheckBox($this, $element);
					$html .= $txt->render_html(); 
				break;
				case "button":
					$html .= '<div class="toit-wrapper-btn"><label></label><input type="submit" name="toit-submit-form" class="toit-form-submit-button '.$element['class'].'" value="'.$element['label'].'" /></div>';
				break;
			}
		}
		return $html;
	}

	/* Validate */

	public function validate() {

		$result = array( 'valid' => true, 'reason' => array() );

		foreach($this->form_elements as $element){
			switch($element['field']){
				case "textbox":
				case "email":
				case "url":
					$txt = new Textbox($this, $element);
					$txt->validate(); 
				break;
				case "textarea":
					$txta = new TextArea($this, $element);
					$txta->validate(); 
				break;
				case "checkbox":
					$txt = new CheckBox($this, $element);
					$txt->validate(); 
				break;
			}
		}
	
		if(empty($this->validation_errors))
			$this->validation_success = true;
		return $this->validation_errors;
	}
	public function get_validation_error( $name ) {
		if ( $this->is_submitted() && isset($this->validation_errors[$name]) )
			return $this->validation_errors[$name];

		return '';
	}
	public function set_validation_error($name, $error){
		$this->validation_errors[$name] = $error;
	}
	public function send_mail() {
		if ( $this->validation_success ){
			if($this->_compose_and_send_mail( )){
				$this->email_sent = true;
				return true;
			}
		}
		return false;
	}

	private function _compose_and_send_mail(  ) {

		$body = $this->top_message. "\n\n";
		
		foreach($this->form_elements as $element){
			$name = toitcf_encode_safe($element['label']);
			switch($element['field']){
				case "textarea":
				case "textbox":
				case "email":
				case "url":
					$name = toitcf_encode_safe($element['label']);
					$body .= $element['label']." : ".$this->element_values[$name]."\n";
				break;
				case "checkbox":
					if($this->element_values[$name])
						$body .= $element['label']." : Checked\n";
				break;
			}
		}

		$body .= "\n".$this->bottom_message. "\n";
		$body .= "\nThis Email is sent by ".TOIT_PLUGIN_TITLE." Installed at ".get_bloginfo('wpurl');
		
		if(isset($_SERVER['REMOTE_ADDR']))
			$body .= " from " .$_SERVER['REMOTE_ADDR'];
		
		$headers = "From: ".get_bloginfo('admin_email')."\n";
		//$headers .= "Content-Type: text/html\n";

		$emails = explode(",", $this->email);
		foreach($emails as $email){
			if(!empty($email))
				@wp_mail( $email, $this->subject, $body, $headers );
		}
		return true;
	}
}
?>