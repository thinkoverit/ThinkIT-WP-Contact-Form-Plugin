<?php
/**
** A Class for TextArea form element
**/

Class TextArea{

	private $options;
	private $form;
	private $name;
	

	function __construct($form, $options){
		$this->form = $form;
		$this->options = $options;
		$this->name = toitcf_encode_safe($this->options['label']);
		$this->placeholder = trim($this->options['placeholder']);
		$this->cname = trim($this->options['class']);
	}
	public function render_html(){
		$atts = '';
		$id_att = '';
		$class = '';
		$maxlength = '';
		$validate_error_html = '';
		$html = '';
	

		if ( 'on' == $this->options['required'])
			$class .= ' toit-required';
		if ( !empty($this->options['class']))
			$class .= ' '.$this->options['class'];
	
		$atts = ' class="'.$class.'"  placeholder="'. $this->placeholder .'" maxlength="'.$maxlength.'" ';
 
		$value = '';
		
		if($this->form->is_submitted()){
			$value = $this->form->get_element_value($this->name);
			
			if ( 'on' == $this->options['required'])
				$validate_error_html = $this->form->get_validation_error($this->name);
		}

		$html = '<div class="toit-wrapper-tag toit-'.$this->name.'"><label>'.$this->options['label'].'</label> <textarea name="' . $this->name . '" ' . $atts . ' >' . esc_attr( $value ) . '</textarea>'. $validate_error_html.'</div>';	
		
		return $html;
	}
	public function validate(){

		$value = $this->form->get_element_value($this->name);
		if ( 'on' == $this->options['required']){
			if(empty($value))
				$this->form->set_validation_error($this->name, '<div class="toit-error">'.$this->options['label'] .' is required.</div>');
		}
	}
}
?>