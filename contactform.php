<?php

class TOIT_ContactForm {

	public $id;
	private $form_tag;
	private $title;
	private $form_elements;
	private $element_values;
	private $email;
	private $subject;
	private $validation_errors = array();
	public $validation_success = false;
	public $email_sent = false;

	function __construct($id){
		$this->id = $id;
		$this->form_tag = 'toit-form-' . $id;
		
		$form = get_contact_form($id);
		
		$this->title = $form['toit_form_name'];
		$this->email = $form['toit_form_email'];
		$this->subject = $form['toit_form_subject'];
		
		$this->form_elements = $form['fields'];
		
		if($this->is_submitted()){
			foreach($this->form_elements as $element)
				$this->element_values[$element['toit_name']] = parse_variable($_POST[$element['toit_name']]);
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

		$form .= '<form action="' . $url . '" method="post" class="toit-form">' . "\n";
		$form .= '<input type="hidden" name="toit-form-id" value="'. esc_attr( $this->id ) . '" />' . "\n";
		$form .= '<input type="hidden" name="toit-form-tag" value="'. esc_attr( $this->form_tag ) . '" />' . "\n";
		
		$form .= $this->_render_elements();


		$form .= '<p class="submit"><input type="submit" name="toit-submit-form" class="button-primary" value="Submit" /></p>';
		$form .= '</form>';
		
		$form .= $this->_render_after_submit_notifications();

		$form .= '</div>';

		return $form;
	}

	private function _render_after_submit_notifications() {
		if($this->email_sent){
			return '<p>Mail sent successfully to Adminitrator</p>';
		}
	}

	private function _render_elements() {
		$html = '';
		foreach($this->form_elements as $element){

			switch($element['toit_field']){
				case "textbox":
				case "email":
				case "url":
					$txt = new Textbox($this, $element);
					$html .= $txt->render_html(); 
				break;
				case "checkbox":
					$txt = new CheckBox($this, $element);
					$html .= $txt->render_html(); 
				break;
			}

		}

		return $html;
	}

	/* Validate */

	public function validate() {

		$result = array( 'valid' => true, 'reason' => array() );

		foreach($this->form_elements as $element){
			switch($element['toit_field']){
				case "textbox":
				case "email":
				case "url":
					$txt = new Textbox($this, $element);
					$txt->validate(); 
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

		$body = "";
		
		foreach($this->form_elements as $element){
			switch($element['toit_field']){
				case "textbox":
				case "email":
				case "url":
					$body .= $element['toit_label']." : ".$this->element_values[$element['toit_name']]."\n";
				break;
				case "checkbox":
					if($this->element_values[$element['toit_name']])
						$body .= $element['toit_label']." : Checked\n";
				break;
			}
		}

		$body .= "\nThis Email is sent by Simple Contact Form Plugin Installed at ".get_bloginfo('wpurl');
		
		$headers = "From: ".get_bloginfo('admin_email')."\n";
		//$headers .= "Content-Type: text/html\n";

		return @wp_mail( $this->email, $this->subject, $body, $headers );
	}
}
?>