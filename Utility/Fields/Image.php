<?php

namespace Modules\Dynamicfield\Utility\Fields;

use Collective\Html\FormFacade;

class Image extends FieldBase
{
    public function __construct($fieldInfo, $entityId, $locale, $dbData = null)
    {
        parent:: __construct($fieldInfo, $entityId, $locale, $dbData);
    }
    /**
     * Check validator with field.
     *
     * @return bool
     */
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

    /**
     * Render html of field.
     *
     * @return string
     */
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

    /**
     * Get error message.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        $error = $this->getOption('error_message');

        return $error;
    }

    /**
     * Show data file when isset.
     *
     * @return string
     */
    public function getDisplayValue()
    {
        $value = $this->value;

        return $value;
    }

    public function loadFieldData()
    {
    }
}
