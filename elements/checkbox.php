<?php
/**
** A Class for Textbox form element
**/

Class CheckBox{

	private $options;
	private $form;
	private $name;
	

	function __construct($form, $options){
		$this->form = $form;
		$this->options = $options;
		$this->name = toit_encode_safe($this->options['label']);

	}
	public function render_html(){
		$atts = '';
		$id_att = '';
		$class = '';
		$validate_error_html = '';
		$html = '';


		if ( 'on' == $this->options['required'])
			$class .= ' toit-required';
		if ( !empty($this->options['class']))
			$class .= ' '.$this->options['class'];
	
		$atts = ' class="'.$class.'" id="'.$id.'" ';
		
		if($this->form->is_submitted()){
			$value = $this->form->get_element_value($this->name);
			
			if ( 'on' == $this->options['required'])
				$validate_error_html = $this->form->get_validation_error($this->name);
		}

		$html = '<p class="toit-wrapper-tag"><label>'.$this->options['label'].'</label> <input type="checkbox" name="' . $this->name . '" value="1"' . $atts . ' />'. $validate_error_html.'</p>';	
		
		return $html;
	}
	public function validate(){
	
		$value = $this->form->get_element_value($this->name);
		if ( 'on' == $this->options['required']){
			if(empty($value))
				$this->form->set_validation_error($this->name, '<p>'.$this->options['label'] .' is required.</p>');
		}
	}
}
?>