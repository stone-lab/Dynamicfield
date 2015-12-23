<?php

namespace Modules\Dynamicfield\Utility\Fields;

use Collective\Html\FormFacade;
use Log;

class Image extends FieldBase
{

    public function __construct($field_info, $type_id, $locale)
    {
        parent:: __construct($field_info, $type_id, $locale);
    }
    public function valid()
    {
        $bResult = false;
        if ($this->getOption('required') == 'true') {
            $value = $this->getValue();
            if (empty($value)) {
                $this->_isValid = false;
            } else {
                $bResult = true;
            }
        } else {
            $bResult = true;
        }

        return $bResult;
    }
    public function render()
    {
        $attrs = array();
        $id = $this->getHtmlId();
        $name = $this->getHtmlName();
        $label = $this->getLabel();
        $css_class = 'file';
        $attrs['id'] = $id;
       
        if ($this->getOption('required') == 'true') {
            $css_class .= ' required';
        }
        $value = $this->_value;
        if ($this->_value) {
            $imgPath = $this->getDisplayValue();
            $img = sprintf("<img src='%s' width='90' height='90' />", $imgPath);
        }
        $attrs['class'] = $css_class;

        $html = '';
        if (!empty($label)) {
            $html .= FormFacade::label($label);
        }
        $html            .= view('dynamicfield::admin.dynamicfield.media-link', compact('name', 'id', 'value'))->render();

        $html = sprintf($this->_html_item_template, $html);

        return $html;
    }

    public function getErrorMessage()
    {
        $error = $this->getOption('error_message');

        return $error;
    }

    public function getDisplayValue()
    {
        $value = $this->_value;

        return $value;
    }

    public function loadFieldData()
    {
    }
}
