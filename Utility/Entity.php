<?php

namespace Modules\Dynamicfield\Utility;

use Modules\Dynamicfield\Entities\Group;
use Modules\Dynamicfield\Entities\Rule;
use Modules\Dynamicfield\Utility\Fields\File;
use Modules\Dynamicfield\Utility\Fields\Image;
use Modules\Dynamicfield\Utility\Fields\Number;
use Modules\Dynamicfield\Utility\Fields\Repeater;
use Modules\Dynamicfield\Utility\Fields\Text;
use Modules\Dynamicfield\Utility\Fields\Textarea;
use Modules\Dynamicfield\Utility\Fields\Wysiwyg;

class Entity
{
    private $template;
    private $entityId;
    private $locale;
    private $type;
    private $groupFields;
    private $fieldValues;
    private $htmlItemTemplate = "<div class='panel box box-primary'>
										<div class='box-header'>
											<h4 class='box-title'><a>%s</a></h4>
										</div>
										<div class='panel-collapse'>
											<div class='box-body'>%s</div>
										</div>
									</div>";

    public function __construct($entityId, $template, $locale, $type)
    {
        $this->template = $template;
        $this->entityId = $entityId;
        $this->locale = $locale;
        $this->type = $type;
    }

    public function init($default = null)
    {
        //getGroupByRule
        $options["type"]     = $this->type;
        $options["template"] = $this->template;
        $groups = $this->getGroupByRule($options);

        if (count($groups)) {
            foreach ($groups as $groupId) {
                $group = Group::find($groupId);
                $this->initGroup($group, $default);
            }
        }
    }

    public function valid()
    {
        $isValid = true;

        if (count($this->groupFields)) {
            foreach ($this->groupFields as $group) {
                $fields = $group['fields'];
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

    public function render()
    {
        $html = '';

        if (count($this->groupFields)) {
            foreach ($this->groupFields as $group) {
                $htmlControl = '';
                $fields = $group['fields'];

                foreach ($fields as $field) {
                    $htmlControl .= $field->render();
                }
                $label = $group['name'];
                $html .= sprintf($this->htmlItemTemplate, $label, $htmlControl);
            }
        }

        return $html;
    }

    public function save()
    {
        $bResult = false;
        try {
            if (count($this->groupFields)) {
                foreach ($this->groupFields as $group) {
                    $fields = $group['fields'];
                    foreach ($fields as $field) {
                        $field->save();
                    }
                }
            }
            $bResult = true;
        } catch (\Exception $e) {
            //exception handling
        }

        return $bResult;
    }

    public function values()
    {
        return $this->fieldValues;
    }
    public function getGroupByRule($options)
    {
        $arrResult = array();
        $rules = Rule::all();

        foreach ($rules as $rule) {
            $params = (array) json_decode($rule->rule);
            $defaultMatch = true;
            foreach ($params as $item) {
                $match = $this->matchRule((array) $item, $options);
                $defaultMatch = $defaultMatch && $match;
            }
            if ($defaultMatch) {
                $arrResult[$rule->group_id] = $rule->group_id;
            }
        }
        return $arrResult;
    }

    private function matchRule($rule, $options)
    {
        $match      = false;
        $type       = array_get($rule, "parameter", 'type');
        $operator   = array_get($rule, "operator", 'equal');
        $value      = array_get($rule, "value", 'default');

        if ($operator == "equal") {
            $match = ($value === $options[$type]);
        } elseif ($operator == "notequal") {
            $match = ($value !== $options[$type]);
        }

        return $match;
    }

    private function initGroup($group, $default = null)
    {
        $groupId = $group->id;
        $groupName = $group->name;
        $fields = $group->getListFields();
        $fieldData = $this->getFieldPostData($default);
        $controls = array();
        if ($fields->count()) {
            $controls['name'] = $groupName;
            foreach ($fields as $field) {
                $fieldValue = @$fieldData['fields'][$field->id];
                $fieldControl = null;
                switch ($field->type) {
                    case 'text':
                        $fieldControl = new Text($field, $this->entityId, $this->locale);
                        break;
                    case 'number':
                        $fieldControl = new Number($field, $this->entityId, $this->locale);
                        break;
                    case 'textarea':
                        $fieldControl = new Textarea($field, $this->entityId, $this->locale);
                        break;
                    case 'wysiwyg':
                        $fieldControl = new Wysiwyg($field, $this->entityId, $this->locale);
                        break;
                    case 'file':
                        $fieldControl = new File($field, $this->entityId, $this->locale);
                        break;
                    case 'image':
                        $fieldControl = new Image($field, $this->entityId, $this->locale);
                        break;
                    case 'repeater':
                        $fieldControl = new Repeater($field, $this->entityId, $this->locale);
                        break;
                }

                // assign entity type class to field to use for save data;
                $fieldControl->setEntityType($this->type);
                $fieldControl->init($fieldValue);
                $controls['fields'][$field->id] = $fieldControl;
                $this->fieldValues[$field->name] = $fieldControl->getDisplayValue();
            }
            $this->groupFields[$groupId] = $controls;
        }
    }

    private function getFieldPostData($data)
    {
        $arrData = array();

        if (isset($data)) {
            $arrData = @$data[$this->locale];
        }

        return $arrData;
    }
}
