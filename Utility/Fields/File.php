<?php

namespace Modules\Dynamicfield\Utility\Fields;

use Collective\Html\FormFacade;
use Modules\Dynamicfield\Entities\Entity;
use Request;

class File extends FieldBase
{
    private $file;

    public function __construct($fieldInfo, $entityId, $locale)
    {
        parent:: __construct($fieldInfo, $entityId, $locale);
    }
    public function init($_default = null)
    {
        // get data from db;
        $entity = Entity::getEntity($this->entityId, $this->entityType, $this->fieldId);
        $this->model = $entity->getFieldByLocale($this->locale);
        $files = Request::file();
        $this->file = @$files[$this->locale]['fields'][$this->fieldId]['value'];

        if (!isset($this->file)) {
            $this->value = $this->model->value;
            if (!$this->model->id) {
                $this->value = $this->getOption('default');
            }
        } else {
            $fileName = $this->entityId.'_'.$this->fieldId.'.'.$this->file->getClientOriginalExtension();
            $this->value = $fileName;
        }
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
        $img = '';
        $cssClass = 'file';
        $errorMessage = '';
        $attrs['id'] = $id;
        if (!$this->isValid) {
            $errorMessage = sprintf("<span class='error'>%s</span>", $this->getErrorMessage());
            $cssClass .= ' error';
        }

        if ($this->getOption('required') == 'true') {
            $cssClass .= ' required';
        }
        if ($this->value) {
            $imgPath = $this->getDisplayValue();
            $img = sprintf("<img src='%s' width='90' height='90' />", $imgPath);
        }
        $attrs['class'] = $cssClass;

        $html = '';
        if (!empty($label)) {
            $html .= FormFacade::label($label);
        }
        $html                .= FormFacade::file($name, $attrs).$errorMessage;
        $html                .= $img;

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
        if ($value) {
            $value = config('asgard.dynamicfield.config.files-path').$value;
        }

        return $value;
    }

    /* override base method */
    public function save()
    {
        if (isset($this->file)) {
            $fileName = $this->entityId.'_'.$this->fieldId.'.'.$this->file->getClientOriginalExtension();
            $this->value = $fileName;
            $this->file->move(public_path().config('asgard.dynamicfield.config.files-path'), $fileName);
        }
        parent::save();
    }
    public function loadFieldData()
    {
    }
}
