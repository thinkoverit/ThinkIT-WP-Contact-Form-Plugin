<?php
/**
** A Class for Textbox form element
**/

Class TextBox{

	private $options;
	private $form;
	private $name;
	

	function __construct($form, $options){
		$this->form = $form;
		$this->options = $options;
		$this->name = $this->options['toit_name'];

	}
	public function render_html(){
		$atts = '';
		$id_att = '';
		$class = '';
		$maxlength = '';
		$validate_error_html = '';
		$html = '';
		$type = "text"; 
	
		if ( 'email' == $this->options['toit_field']){
			$class .= ' toit-email';	
			$type = "email"; 
		}else if ( 'url' == $this->options['toit_field'])
			$class .= ' toit-url';
		if ( 'on' == $this->options['toit_required'])
			$class .= ' toit-required';
		if ( !empty($this->options['toit_class']))
			$class .= ' '.$this->options['toit_class'];
	
		$atts = ' class="'.$class.'" id="'.$id.'" maxlength="'.$maxlength.'" ';

		$value = '';
		
		if($this->form->is_submitted()){
			$value = $this->form->get_element_value($this->name);
			
			if ( 'on' == $this->options['toit_required'])
				$validate_error_html = $this->form->get_validation_error($this->name);
		}

		$html = '<p class="toit-wrapper-tag"><label>'.$this->options['toit_label'].'</label> <input type="' . $type . '" name="' . $this->name . '" value="' . esc_attr( $value ) . '"' . $atts . ' />'. $validate_error_html.'</p>';	
		
		return $html;
	}
	public function validate(){
	
		$value = $this->form->get_element_value($this->name);
		if ( 'on' == $this->options['toit_required']){
			if(empty($value))
				$this->form->set_validation_error($this->name, '<p>'.$this->options['toit_label'] .' is required.</p>');
			else{
				if ( 'email' == $this->options['toit_field']){
					if(!is_email($value))
						$this->form->set_validation_error($this->name, '<p>'.$this->options['toit_label'] .' is invalid.</p>');
				}else if ( 'url' == $this->options['toit_field']){
					if(!isValidURL($value))
						$this->form->set_validation_error($this->name, '<p>'.$this->options['toit_label'] .' is invalid.</p>');
				}
			}
		}
	}
}
?>