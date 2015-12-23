<?php namespace Modules\Dynamicfield\Utility\Fields;

use Collective\Html\FormFacade;
use Log;

class Text extends FieldBase
{
    public function __construct($field_info, $entityId, $locale)
    {
        parent:: __construct($field_info, $entityId, $locale);
    }

    public function valid()
    {
        $bResult = false;
        if ($this->getOption("required")=='true') {
            $value = $this->getValue();
            if (empty($value)) {
                $this->_isValid = false;
                Log::info($this->getFieldName());
            } else {
                $bResult = true;
            }
        } else {
            $bResult = true;
        }

        return $bResult ;
    }
    public function render()
    {
        $attrs        = array();
        $id            = $this->getHtmlId();
        $name        = $this->getHtmlName();
        $value        = $this->getValue() ;
        $label        = $this->getLabel() ;

        $css_class ="text form-control" ;
        $error_message = "";
        $attrs["id"] = $id;
        if (!$this->_isValid) {
            $error_message = sprintf("<span class='error'>%s</span>", $this->getErrorMessage());
            $css_class .= " error";
        }

        $defaultValue = $this->getOption("default_value");

        if (!strlen($value)) {
            $value = $defaultValue;
        }

        if ($this->getOption("required") == 'true') {
            $css_class .=" required";
        }
        if ($this->getOption("placeholder")) {
            $attrs["placeholder"] = $this->getOption("placeholder");
        }
        if ($this->getOption("limit")) {
            $attrs["maxlength"] = $this->getOption("limit");
        }
        $attrs["class"]        = $css_class;
        /* $html_controll 		= sprintf("<input class='%s' type='text' id='%s' name='%s' value='%s' %s />%s",$css_class,$id,$name,$value,$attrs,$error_message);
        $html				= sprintf($this->_html_item_template,$label,$html_controll); */
        $html                 ="";
        if (!empty($label)) {
            $html                 .= FormFacade::label($label);
        }
        $html                .= FormFacade::text($name, $value, $attrs) . $error_message;
        $html  = sprintf($this->_html_item_template, $html);

        return $html ;
    }

    public function getErrorMessage()
    {
        $error ="";
        $error = $this->getOption("error_message") ;

        return $error ;
    }

    public function loadFieldData()
    {
    }
}
