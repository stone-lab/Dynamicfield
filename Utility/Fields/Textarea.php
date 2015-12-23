<?php namespace Modules\Dynamicfield\Utility\Fields;

use Collective\Html\FormFacade;
use Log;

class Textarea extends FieldBase
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

        $css_class ="textarea form-control" ;
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
        if ($this->getOption("rows")) {
            $attrs["rows"] = $this->getOption("rows");
        }
        $attrs["class"]        = $css_class;

        $html                ="";
        if (!empty($label)) {
            $html .= FormFacade::label($label);
        }
        $html                .= FormFacade::textarea($name, $value, $attrs) . $error_message;
        $html                = sprintf($this->_html_item_template, $html);

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
