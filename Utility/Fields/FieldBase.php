<?php namespace Modules\Dynamicfield\Utility\Fields;

use Modules\Dynamicfield\Entities\Entity;
use Modules\Dynamicfield\Entities\Field;
use Modules\Dynamicfield\Entities\RepeaterTranslation;
use Modules\Dynamicfield\Entities\RepeaterValue;

class FieldBase
{
    protected $_field;
    protected $_options;
    protected $_fieldModel;
    protected $_value;
    protected $_field_type;
    protected $_field_label;
    protected $_field_id;
    protected $_repeater_id;
    protected $_translation_id;
    protected $_entity_id;
    protected $_locale;
    protected $_isValid = true;
    protected $_html_name_format    = "%s[fields][%s][value]";
    protected $_html_id_format        = "%s_%s_value";
    protected $_html_item_template = "<div class='form-group'>%s</div>";

    public function __construct($field_info, $pageId, $locale)
    {
        $this->_field    = $field_info;
        $option_data    = (array) json_decode($field_info->data) ;
        $this->_options    = $option_data;
        $this->_entity_id    = $pageId;
        $this->_locale    = $locale;
        $this->_field_id    = $this->_field->id;
    }

    public function init($default= null)
    {

        // get data from db;
        $this->_model = $this->getModel($this->_entity_id, $this->_field_id, $this->_locale);
        if (!isset($default)) {
            $this->_value    = $this->_model->value;
            if (!$this->_model->id) {
                $this->_value = $this->getOption("default");
            }
        } else {
            $this->_value    = $default["value"];
        }
    }

    public function getModel($entity_id, $field_id, $locale)
    {
        $model    = null;

        if (is_a($this->_field, 'Modules\Dynamicfield\Entities\Field')) {
            $entity        = Entity::getEntity($entity_id, $field_id) ;
            $model            = $entity->getFieldByLocale($locale);
        } else {
            $model    = new RepeaterValue();
            if (is_numeric($this->_translation_id)) {
                $repeaterTranslate        = RepeaterTranslation::firstOrNew(array('id'=>$this->_translation_id)) ;
                $model                    = $repeaterTranslate->getFieldValue($field_id);
            }
        }

        return $model ;
    }
    /* get value  */
    public function getValue()
    {
        return  $this->_value;
    }

    public function getDisplayValue()
    {
        return $this->_value;
    }
    /*  */
    public function setValue($value)
    {
        $this->_value    = $value;
    }
    /*  */
    public function valid()
    {
        return true;
    }
    /*  */
    public function getErrorMessage()
    {
    }
    public function render()
    {
    }
    public function loadFieldData()
    {
    }
    /*  */
    public function getFieldId()
    {
        return $this->_field_id;
    }
    /*  */
    public function getFieldName()
    {
        return $this->_field->name;
    }
    /* get Html id */
    public function getHtmlId()
    {
        $strHtmlId = sprintf($this->_html_id_format, $this->_locale, $this->_field_id);

        return $strHtmlId ;
    }
    /* get Html Nam */
    public function getHtmlName()
    {
        $strHtmlName = sprintf($this->_html_name_format, $this->_locale, $this->_field_id);

        return $strHtmlName ;
    }
    public function setHtmlNameFormat($strFormat)
    {
        $this->_html_name_format =  $strFormat ;
    }

    public function setHtmlIdFormat($strFormat)
    {
        $this->_html_id_format =  $strFormat ;
    }
    /* get Html Nam */
    public function getLabel()
    {
        $strLabel    =  $this->getOption("label");

        return $strLabel ;
    }
    public function setLabel($value)
    {
        $this->_options["label"] = $value ;
    }
    public function setRepeaterId($value)
    {
        $this->_repeater_id = $value ;
    }
    public function setTranslateId($value)
    {
        $this->_translation_id = $value ;
    }
    public function getTranslateId()
    {
        return $this->_translation_id  ;
    }
    public function setHtmlItemTemplate($strFormat)
    {
        $this->_html_item_template =  $strFormat ;
    }
    public function save()
    {
        if (is_a($this->_model, 'Modules\Dynamicfield\Entities\FieldTranslation')) {
            $this->saveField($this->_entity_id, $this->_field_id);
        } else {
            // save sub control of repeater
            $this->saveRepeaterField($this->_entity_id, $this->_field_id);
        }
    }
    // save normal field
    public function saveField($entity_id, $field_id)
    {
        $entity = Entity::getEntity($entity_id, $field_id);
        if (!$entity->id) {
            $entity->entity_id    = $entity_id ;
            $entity->field_id    = $field_id ;
            $entity->save() ;
        }
        $this->_model->entity_field_id = $entity->id ;
        $this->_model->locale = $this->_locale ;
        $this->_model->value = $this->getValue() ;
        $this->_model->save();
    }

    // save repeat data for field of repeater 
    public function saveRepeaterField($entity_id, $field_id)
    {
        $repeaterId = $this->_repeater_id ;
        ;

        $repeaterTranslate = RepeaterTranslation::firstOrNew(array('id'=>$this->_translation_id)) ;

        if (!$repeaterTranslate->id) {
            $entity = Entity::getEntity($entity_id, $repeaterId);
            if (!$entity->id) {
                $entity->entity_id    = $entity_id ;
                $entity->field_id    = $repeaterId ;
                $entity->save() ;
            }
            $repeaterTranslate->entity_repeater_id = $entity->id ;
            $repeaterTranslate->locale = $this->_locale ;
            $repeaterTranslate->save();
        }
        $this->_model->translation_id = $repeaterTranslate->id ;
        $this->_model->field_id = $this->_field_id ;

        $this->_model->value = $this->getValue() ;
        $this->_model->save();
        // assign translate_id to same group;
        $this->_translation_id = $this->_model->translation_id ;
    }

    // get value of option 
    public function getOption($key)
    {
        $value = "";
        try {
            $value  = $this->_options[$key];
        } catch (\Exception $e) {
        }

        return $value ;
    }
}
