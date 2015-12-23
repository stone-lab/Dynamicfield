<?php namespace Modules\Dynamicfield\Utility\Fields;

use Collective\Html\FormFacade;
use Modules\Media\Image\Imagy;
use Log;

class Image extends FieldBase
{
    private $file;

    public function __construct($field_info, $type_id, $locale)
    {
        parent:: __construct($field_info, $type_id, $locale);
    }
	public function valid(){
		$bResult = false;
		if($this->getOption("required")=='true'){
			$value = $this->getValue();
			if(empty($value)){
				$this->_isValid = false;
				Log::info($this->getFieldName());
			}else{
				$bResult = true;
			}
		}else{
			$bResult = true;
		}
		return $bResult ;
	}
	public function render(){
		$attrs		= array();
		$id			= $this->getHtmlId();
		$name		= $this->getHtmlName();
		$value		= $this->getValue() ;
		$label		= $this->getLabel() ;
		$img		=  '';
		$css_class 	="file" ;
		$error_message = "";
		$attrs["id"] = $id;
		if(!$this->_isValid){
			$error_message = sprintf("<span class='error'>%s</span>",$this->getErrorMessage());
			$css_class .= " error";
		}
		
		
		if($this->getOption("required") == 'true'){
			$css_class .=" required";
		}
		$value = $this->_value ;
		if($this->_value){
	
			$imgPath 	=  $this->getDisplayValue();
			$img = sprintf("<img src='%s' width='90' height='90' />",$imgPath);
		}
		$attrs["class"] 	= $css_class;
		
		$html				="";
		if(!empty($label)){
			$html .= FormFacade::label($label);
		}
		//$html				.= FormFacade::file($name, $attrs).$error_message;
		
		$className = get_class($this->_model);
		//$className 	= str_replace("\\","\\\\",$className);
		$entityClass 	= "Modules\\Dynamicfield\\Entities\\Field";
		$entityId 		= $this->_field_id;
		
		$zone 			= "coverimage";
	
		$coverimage 	= $this->_model->findFileByZoneForEntity($zone);

		$html 			.= view('dynamicfield::admin.dynamicfield.media-link',compact('name','id','value'))->render();
		/* $html			.= $img; */
		
		$html  = sprintf($this->_html_item_template,$html);
		return $html ;
	}
	
	public function getErrorMessage(){
		$error ="";
		$error = $this->getOption("error_message") ;
		return $error ;
	}
	
	public function getDisplayValue(){
		$value = $this->_value;
		return $value;
	}

    public function loadFieldData()
    {
    }
}
