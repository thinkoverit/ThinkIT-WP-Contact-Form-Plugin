<?php
/**
** A Class for Captcha form element
**/

Class Captcha{

	private $options;
	private $form;
	private $name;
	

	function __construct($form, $options){
		$this->form = $form;
		$this->options = $options;
		$this->name = toitcf_encode_safe($this->options['label']);
		$this->cname = trim($this->options['class']);
	}
	public function render_html(){
		$atts = '';
		$id_att = '';
		$class = '';
		$maxlength = '';
		$validate_error_html = '';
		$html = '';
		$type = "text"; 
  
  if ( 'on' == $this->options['required'] || $this->options['required'] ==1){
   if ( 'on' == $this->options['required'])
    $class .= ' toit-required';
   if ( !empty($this->options['class']))
    $class .= ' '.$this->options['class'];
  
   $atts = ' class="'.$class.'"  maxlength="'.$maxlength.'" ';

   $value = '';
   
   if($this->form->is_submitted()){
    $value = $this->form->get_element_value($this->name);
    
    if ( 'on' == $this->options['required'])
     $validate_error_html = $this->form->get_validation_error($this->name);
   }

   $num1 = rand(0, 9);
   $num2 = rand(0, 9);
   $result = $num1 + $num2;
   
   $question = '( '. $num1 . ' + ' . $num2 . ' = ? )';
   
   $html = '<div class="toit-wrapper-tag toit-'.$this->name.'"><input type="hidden" id="captcha_validate" name="captcha_validate" value="'.$result.'" />';
   $html .= '<label>'.$this->options['label'].'<span>'.$question.'</span></label> <input type="' . $type . '" placeholder="'.__('Enter ').' '.$question.'" name="' . $this->name . '" value="' . esc_attr( $value ) . '"' . $atts . ' />'. $validate_error_html.'</div>';	


   return $html;
  }
	}
	public function validate(){
	
		$value = $this->form->get_element_value($this->name);
  if ( 'on' == $this->options['required'] || $this->options['required'] ==1){
   if(empty($value))
    $this->form->set_validation_error($this->name, '<div class="toit-error">'.$this->options['label'] .' is required.</div>');
   else if(isset($_POST['captcha_validate'])){
    $code = strip_tags($_POST['captcha_validate']);
    if($code != $value){
     $this->form->set_validation_error($this->name, '<div class="toit-error">'.$this->options['label'] .' is invalid.</div>');
    }
   }
  }
 }
}
?>