<?php

namespace Modules\Dynamicfield\Utility\Fields;

use Modules\Dynamicfield\Entities\Entity;
use Modules\Dynamicfield\Entities\Field;
use Modules\Dynamicfield\Entities\RepeaterTranslation;
use Modules\Dynamicfield\Entities\RepeaterValue;

class FieldBase
{
    protected $field;
    protected $options;
    protected $fieldModel;
    protected $value;
    protected $fieldType;
    protected $fieldLabel;
    protected $fieldId;
    protected $repeaterId;
    protected $translationId;
    protected $entityId;
    protected $locale;
    protected $model;
    protected $entityType;
    protected $isValid = true;
    protected $htmlNameFormat = '%s[fields][%s][value]';
    protected $htmlIdFormat = '%s_%s_value';
    protected $htmlItemTemplate = "<div class='form-group'>%s</div>";

    public function __construct($fieldInfo, $entityId, $locale)
    {
        $this->field       = $fieldInfo;
        $option_data        = (array) json_decode($fieldInfo->data);
        $this->options     = $option_data;
        $this->entityId   = $entityId;
        $this->locale      = $locale;
        $this->fieldId    = $this->field->id;
    }

    public function init($default = null)
    {
        $this->model = $this->getModel();
        if (!isset($default)) {
            $this->value = $this->model->value;
            if (!$this->model->id) {
                $this->value = $this->getOption('default');
            }
        } else {
            $this->value = $default['value'];
        }
    }

    public function getModel()
    {
        if (is_a($this->field, 'Modules\Dynamicfield\Entities\Field')) {
            $entity = Entity::getEntity($this->entityId, $this->entityType, $this->fieldId);
            $model = $entity->getFieldByLocale($this->locale);
        } else {
            $model = new RepeaterValue();
            if (is_numeric($this->translationId)) {
                $repeaterTranslate = RepeaterTranslation::firstOrNew(array('id' => $this->translationId));
                $model = $repeaterTranslate->getFieldValue($this->fieldId);
            }
        }

        return $model;
    }
    /* get value  */
    public function getValue()
    {
        return  $this->value;
    }

    public function getDisplayValue()
    {
        return $this->value;
    }
    /*  */
    public function setValue($value)
    {
        $this->value = $value;
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
        return $this->fieldId;
    }
    /*  */
    public function getFieldName()
    {
        return $this->field->name;
    }
    /* get Html id */
    public function getHtmlId()
    {
        $strHtmlId = sprintf($this->htmlIdFormat, $this->locale, $this->fieldId);

        return $strHtmlId;
    }
    /* get Html Nam */
    public function getHtmlName()
    {
        $strHtmlName = sprintf($this->htmlNameFormat, $this->locale, $this->fieldId);

        return $strHtmlName;
    }
    public function setHtmlNameFormat($strFormat)
    {
        $this->htmlNameFormat = $strFormat;
    }

    public function setHtmlIdFormat($strFormat)
    {
        $this->htmlIdFormat = $strFormat;
    }
    /* get Html Nam */
    public function getLabel()
    {
        $strLabel = $this->getOption('label');

        return $strLabel;
    }
    public function setLabel($value)
    {
        $this->options['label'] = $value;
    }
    public function setRepeaterId($value)
    {
        $this->repeaterId = $value;
    }
    public function setTranslateId($value)
    {
        $this->translationId = $value;
    }
    public function getTranslateId()
    {
        return $this->translationId;
    }
    public function setHtmlItemTemplate($strFormat)
    {
        $this->htmlItemTemplate = $strFormat;
    }
    public function save()
    {
        if (is_a($this->model, 'Modules\Dynamicfield\Entities\FieldTranslation')) {
            $this->saveField();
        } else {
            // save sub control of repeater
            $this->saveRepeaterField();
        }
    }
    // save normal field
    public function saveField()
    {
        $entity = Entity::getEntity($this->entityId, $this->entityType, $this->fieldId);
        if (!$entity->id) {
            $entity->entity_id      = $this->entityId;
            $entity->field_id       = $this->fieldId;
            $entity->entity_type    = $this->entityType;
            $entity->save();
        }
        $this->model->entity_field_id = $entity->id;
        $this->model->locale = $this->locale;
        $this->model->value = $this->getValue();
        $this->model->save();
    }

    // save repeat data for field of repeater 
    public function saveRepeaterField()
    {
        $repeaterId = $this->repeaterId;

        $repeaterTranslate = RepeaterTranslation::firstOrNew(array('id' => $this->translationId));

        if (!$repeaterTranslate->id) {
            $entity = Entity::getEntity($this->entityId, $this->entityType, $repeaterId);
            if (!$entity->id) {
                $entity->entity_id      = $this->entityId;
                $entity->entity_type    = $this->entityType;
                $entity->field_id       = $repeaterId;
                $entity->save();
            }
            $repeaterTranslate->entity_repeater_id = $entity->id;
            $repeaterTranslate->locale = $this->locale;
            $repeaterTranslate->save();
        }
        $this->model->translation_id = $repeaterTranslate->id;
        $this->model->field_id = $this->fieldId;

        $this->model->value = $this->getValue();
        $this->model->save();
        // assign translate_id to same group;
        $this->translationId = $this->model->translation_id;
    }

    public function setEntityType($type)
    {
        $this->entityType = $type ;
    }
    // get value of option 
    public function getOption($key)
    {
        $value = '';
        try {
            $value = $this->options[$key];
        } catch (\Exception $e) {
        }

        return $value;
    }
}
