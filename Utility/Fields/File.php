<?php namespace Modules\Dynamicfield\Utility\Fields;

use Collective\Html\FormFacade;
use Log;
use Modules\Dynamicfield\Entities\PageField;
use Request;

class File extends FieldBase
{
    private $file;

    public function __construct($field_info, $entityId, $locale)
    {
        parent:: __construct($field_info, $entityId, $locale);
    }
    public function init($_default= null)
    {
        // get data from db;
        $pageField        = PageField::getPageField($this->_page_id, $this->_field_id) ;
        $this->_model    = $pageField->getValueByLocale($this->_locale);
        $files            = Request::file();
        $this->file        = @$files[$this->_locale]["fields"][$this->_field_id]["value"];

        if (!isset($this->file)) {
            $this->_value    = $this->_model->value;
            if (!$this->_model->id) {
                $this->_value = $this->getOption("default");
            }
        } else {
            $fileName        = $this->_page_id . "_" . $this->_field_id . "." . $this->file->getClientOriginalExtension();
            $this->_value    = $fileName;
        }
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
        $img        =  '';
        $css_class    ="file" ;
        $error_message = "";
        $attrs["id"] = $id;
        if (!$this->_isValid) {
            $error_message = sprintf("<span class='error'>%s</span>", $this->getErrorMessage());
            $css_class .= " error";
        }

        if ($this->getOption("required") == 'true') {
            $css_class .=" required";
        }
        if ($this->_value) {
            $imgPath    =  $this->getDisplayValue();
            $img = sprintf("<img src='%s' width='90' height='90' />", $imgPath);
        }
        $attrs["class"]    = $css_class;

        $html                ="";
        if (!empty($label)) {
            $html .= FormFacade::label($label);
        }
        $html                .= FormFacade::file($name, $attrs) . $error_message;
        $html                .= $img;

        $html  = sprintf($this->_html_item_template, $html);

        return $html ;
    }

    public function getErrorMessage()
    {
        $error ="";
        $error = $this->getOption("error_message") ;

        return $error ;
    }

    public function getDisplayValue()
    {
        $value = $this->_value;
        if ($value) {
            $value = config('asgard.dynamicfield.config.files-path') . $value;
        }

        return $value;
    }

    /* override base method */
    public function save()
    {
        if (isset($this->file)) {
            $fileName        = $this->_page_id . "_" . $this->_field_id . "." . $this->file->getClientOriginalExtension();
            $this->_value    = $fileName;
            $this->file->move(public_path() . config('asgard.dynamicfield.config.files-path'), $fileName);
        }
        parent::save();
    }
    public function loadFieldData()
    {
    }
}
