<?php namespace Modules\Dynamicfield\Utility;

use Modules\Dynamicfield\Entities\Field;
use Modules\Dynamicfield\Entities\Group;
use Modules\Dynamicfield\Utility\Fields\File;
use Modules\Dynamicfield\Utility\Fields\Image;
use Modules\Dynamicfield\Utility\Fields\Number;
use Modules\Dynamicfield\Utility\Fields\Repeater;
use Modules\Dynamicfield\Utility\Fields\Text;
use Modules\Dynamicfield\Utility\Fields\Textarea;
use Modules\Dynamicfield\Utility\Fields\Wysiwyg;

class Entity
{
    private $_template;
    private $_field_type;
    private $_field_controls;
    private $_page_id;
    private $_group_value_id;
    private $_locale;
    private $_groupFields;
    private $fieldValues;
    private $_html_item_template = "<div class='panel box box-primary'>
										<div class='box-header'>
											<h4 class='box-title'><a>%s</a></h4>
										</div>
										<div class='panel-collapse'>
											<div class='box-body'>%s</div>
										</div>
									</div>";

    public function __construct($pageId, $template, $locale)
    {
        $this->_template            = $template ;
        $this->_page_id            = $pageId ;
        $this->_locale            = $locale ;
    }
    /* Initial data for group fields  */
    public function init($default = null)
    {
        $groups    = Group::FindByTemplate($this->_template);

        if ($groups->count()) {
            foreach ($groups as $group) {
                $this->initGroup($group, $default);
            }
        }
    }
    //init data per group
    private function initGroup($group, $default=null)
    {
        $group_id        =    $group->id ;
        $group_name        =    $group->name ;
        $fields            =    $group->getListFields();
        $field_data        =    $this->_getFieldPostData($default);

        if ($fields->count()) {
            $controls["name"] = $group_name ;
            foreach ($fields as $field) {
                $fieldValue    = @$field_data['fields'][$field->id];
                $fieldControl    = null;
                switch ($field->type) {
                    case 'text':
                        $fieldControl = new Text($field, $this->_page_id, $this->_locale);

                        break;
                    case 'number':
                        $fieldControl = new Number($field, $this->_page_id, $this->_locale);
                        break;
                    case 'textarea':
                        $fieldControl = new Textarea($field, $this->_page_id, $this->_locale);
                        break;
                    case 'wysiwyg':
                        $fieldControl = new Wysiwyg($field, $this->_page_id, $this->_locale);
                        break;
                    case 'file':
                        $fieldControl = new File($field, $this->_page_id, $this->_locale);
                        break;
                    case 'image':
                        $fieldControl = new Image($field, $this->_page_id, $this->_locale);
                        break;
                    case 'repeater':

                        $fieldControl = new Repeater($field, $this->_page_id, $this->_locale);
                        break;
                }
                $fieldControl->init($fieldValue);
                $controls["fields"][$field->id] = $fieldControl;
                // assign value of each field to get late on fronend
                $this->fieldValues[$field->name] = $fieldControl->getDisplayValue();
            }
            $this->_groupFields[$group_id] = $controls;
        }
    }

    // valid for group field
    public function valid()
    {
        $isValid  = true;

        if (count($this->_groupFields)) {
            foreach ($this->_groupFields as $group) {
                $fields = $group["fields"] ;
                foreach ($fields as $field) {
                    $isValid = $field->valid();
                    if (!$isValid) {
                        break;
                    }
                }
            }
        }

        return $isValid;
    }
    // render for group field
    public function render()
    {
        $html            = "";

        if (count($this->_groupFields)) {
            foreach ($this->_groupFields as $group) {
                $html_control  = "";
                $fields = $group["fields"] ;

                foreach ($fields as $field) {
                    $html_control .= $field->render();
                }
                $label = $group["name"];
                $html .= sprintf($this->_html_item_template, $label, $html_control);
            }
        }

        return $html;
    }
    // save field data to database ;
    public function save()
    {
        $bResult = false;
        $abort_save = false;
        try {
            if (count($this->_groupFields)) {
                foreach ($this->_groupFields as $group) {
                    $fields = $group["fields"] ;
                    foreach ($fields as $field) {
                        $field->save();
                    }
                }
            }
            $bResult = true;
        } catch (\Exception $e) {
            //exception handling
        }

        return $bResult ;
    }

    //get list field value of group ;
    public function values()
    {
        return $this->fieldValues ;
    }
    // get Field Data
    private function _getFieldPostData($data)
    {
        $_arrData = array();

        if (isset($data)) {
            $_arrData = @$data[$this->_locale];
        }

        return $_arrData ;
    }
}
