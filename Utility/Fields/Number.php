<?php

namespace Modules\Dynamicfield\Utility\Fields;

use Collective\Html\FormFacade;

class Number extends FieldBase
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
        $value = $this->getValue();
        $label = $this->getLabel();

        $cssClass = 'number form-control';
        $errorMessage = '';
        $attrs['id'] = $id;
        if (!$this->isValid) {
            $errorMessage = sprintf("<span class='error'>%s</span>", $this->getErrorMessage());
            $cssClass .= ' error';
        }

        $defaultValue = $this->getOption('default_value');
        if (!strlen($value)) {
            $value = $defaultValue;
        }

        if ($this->getOption('required') == 'true') {
            $cssClass .= ' required';
        }
        if ($this->getOption('placeholder')) {
            $attrs['placeholder'] = $this->getOption('placeholder');
        }
        if ($this->getOption('min_value')) {
            $attrs['min'] = $this->getOption('min_value');
        }
        if ($this->getOption('max_value')) {
            $attrs['max'] = $this->getOption('max_value');
        }
        $attrs['class'] = $cssClass;

        $html = '';
        if (!empty($label)) {
            $html .= FormFacade::label($label);
        }

        $html                .= FormFacade::number($name, $value, $attrs) . $errorMessage;
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

    public function loadFieldData()
    {
    }
}
