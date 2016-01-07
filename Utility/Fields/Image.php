<?php

namespace Modules\Dynamicfield\Utility\Fields;

use Collective\Html\FormFacade;

class Image extends FieldBase
{

    public function __construct($fieldInfo, $entityId, $locale)
    {
        parent:: __construct($fieldInfo, $entityId, $locale);
    }
    public function valid()
    {
        $bResult = false;
        if ($this->getOption('required') == 'true') {
            $value = $this->getValue();
            if (empty($value)) {
                $this->isValid = false;
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
        $cssClass = 'file';
        $attrs['id'] = $id;
       
        if ($this->getOption('required') == 'true') {
            $cssClass .= ' required';
        }
        $value = $this->value;
        $attrs['class'] = $cssClass;
        $html = '';
        if (!empty($label)) {
            $html .= FormFacade::label($label);
        }
        $html            .= view('dynamicfield::admin.dynamicfield.media-link', compact('name', 'id', 'value'))->render();

        $html = sprintf($this->htmlItemTemplate, $html);

        return $html;
    }

    public function getErrorMessage()
    {
        $error = $this->getOption('error_message');

        return $error;
    }

    public function getDisplayValue()
    {
        $value = $this->value;

        return $value;
    }

    public function loadFieldData()
    {
    }
}
