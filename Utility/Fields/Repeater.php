<?php

namespace Modules\Dynamicfield\Utility\Fields;

use Collective\Html\FormFacade;
use Modules\Dynamicfield\Entities\Entity;
use Modules\Dynamicfield\Entities\RepeaterTranslation;

class Repeater extends FieldBase
{
    protected $options;
    protected $fieldModel;
    protected $value;
    protected $fieldType;
    protected $fieldLabel;
    protected $fieldId;
    protected $groupFieldId;
    protected $locale;
    protected $repeaterHeaders;
    protected $order;
    protected $isValid = true;
    protected $htmlItemTemplate = '<tr>%s</tr>';

    protected $groupFields = array();
    protected $fieldValues = array();

    protected $deleteItems = '';
    protected $defaultOrder = '';

    public function init($default = null)
    {
        $this->initRepeatFields($this->field, $default);
    }

    //init data per group
    private function initRepeatFields($fieldInfo, $default = null)
    {
        $controls = array();
        $repeaterId = $fieldInfo->id;
        $entity = Entity::getEntity($this->entityId, $this->entityType, $repeaterId);
        $repeaters = $entity->getRepeatersByLocale($this->locale);

        $post_data = @$default['value'];
        $this->defaultOrder = @$default['order'];
        if (isset($post_data)) {
            unset($post_data['clone']);
            $this->deleteItems = $default['delete'];

            foreach ($post_data as $k => $control) {
                $listDefault = $post_data[$k];
                $controls[$k]['fields'] = $this->createListControlAfterPostData($k, $listDefault);
                $controls[$k]['order'] = -1;
            }
        } else {
            if ($repeaters->count()) {
                $i = 1;
                foreach ($repeaters as $repeater) {
                    $controls[$repeater->id]['fields'] = $this->createListControl($repeater);
                    $controls[$repeater->id]['order'] = $i;
                    ++$i;
                }
            }
        }

        $this->groupFields = $controls;

        $this->repeaterHeaders = $this->createHeaderRepeater();
    }

    // create list controller when user post data from browser

    private function createListControlAfterPostData($repeaterId, $default = null)
    {
        $controls = array();
        $repeaterField = $this->field;
        $nameFormat = '%s[fields]'.sprintf('[%s][value][%s]', $this->getFieldId(), $repeaterId).'[%s][value]';
        $idFormat = '%s_'.sprintf('%s_%s_', $this->getFieldId(), $repeaterId).'_%s_value';
        $filedOfRepeater = $repeaterField->getListFields();

        if ($filedOfRepeater->count()) {
            foreach ($filedOfRepeater as $field) {
                $listDefault = $default[$field->id];
                $fieldControl = $this->createFieldControl($field, $repeaterId, $listDefault);
                $fieldControl->setHtmlNameFormat($nameFormat);
                $fieldControl->setHtmlIdFormat($idFormat);
                $controls[$field->id] = $fieldControl;
            }
        }

        return $controls;
    }

    private function createListControl($repeater)
    {
        $repeater_field = $this->field;
        $nameFormat = '%s[fields]'.sprintf('[%s][value][%s]', $this->getFieldId(), $repeater->id).'[%s][value]';
        $idFormat = '%s_'.sprintf('%s_%s_', $this->getFieldId(), $repeater->id).'_%s_value';
        $filedOfRepeater = $repeater_field->getListFields();

        if ($filedOfRepeater->count()) {
            foreach ($filedOfRepeater as $field) {
                $fieldControl = $this->createFieldControl($field, $repeater->id);
                $fieldControl->setHtmlItemTemplate('%s');
                $fieldControl->setHtmlNameFormat($nameFormat);
                $fieldControl->setHtmlIdFormat($idFormat);

                $value = $repeater->getFieldValue($field->id);
                $fieldControl->setValue($value->value);

                $controls[$field->id] = $fieldControl;
            }
        }

        return $controls;
    }

    private function createFieldControl($field, $translateId, $default = null)
    {
        $fieldControl = null;
        $fieldValue = $default;

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
        }
        // assign entity type class to field to use for save data;
        $fieldControl->setEntityType($this->entityType);
        $fieldControl->setRepeaterId($this->fieldId);
        $fieldControl->setTranslateId($translateId);

        $fieldControl->init($fieldValue);

        return $fieldControl;
    }
    private function createHeaderRepeater()
    {
        $repeater = new RepeaterTranslation();
        //TODO Here was ='clone'
        $repeater->id = 'clone';
        $controls = $this->createListControl($repeater);

        return $controls;
    }
    // valid for group field
    public function valid()
    {
        $isValid = true;

        return $isValid;
    }
    // render for group field
    public function render()
    {
        $htmlRepeaterTemplate = '';
        $tdFirstBody = "<td class='field-order'>
                                <label class='circle' >%s</label>
                                <input type='hidden' name='%s' id='%s' value='%s'/>
                            </td>";
        $repeaterDeleteId = 'repeater_delete_'.$this->locale.'_'.$this->getFieldId();
        $repeaterTableId = 'repeater_table_'.$this->locale.'_'.$this->getFieldId();
        $repeaterDeleteName = sprintf('%s[fields][%s][delete]', $this->locale, $this->getFieldId());
        $tdDeleteBtn = "<td class='last'>
							<a class='btn-delete' onclick=\"deleteRepeaterField('{$this->locale}_{$this->getFieldId()}',this)\">
								<span class='glyphicon glyphicon-minus'></span>
							</a>
						</td>";
        $groups = $this->groupFields;

        $headers = $this->repeaterHeaders;
        $columns = count($headers) + 2;
        $tableHeader = "<tr class='repeater-group'>
							<th colspan='{$columns}'>".$this->getLabel().'</th>
						</tr>';

        $columnWidth = 50;
        if (count($headers)) {
            $columnWidth = 100 / ($columns - 2);
             // make header
            $tdHeader = '';
            $tdTemplate = '';
            foreach ($headers as $field) {
                $tdHeader .= sprintf("<th class='caption' width='%s'>%s</th>", $columnWidth.'%', $field->getLabel());
                $field->setLabel('');
                $tdTemplate .= sprintf("<td width='%s'>%s</td>", $columnWidth.'%', $field->render());
            }

            $groupName = $this->locale.'[fields][%s][order][%s]';
            $groupName = sprintf($groupName, $this->fieldId, 'clone');
            // assign template to create new item of repeater
            $tdFirstNew = sprintf($tdFirstBody, -1, $groupName, $groupName, -1);
            $tdTemplate = $tdFirstNew.$tdTemplate.$tdDeleteBtn;
            $repeaterFormat = "<tr class='repeater-template' id='repeater_template_%s_%s'>%s</tr>";
            $htmlRepeaterTemplate = sprintf($repeaterFormat, $this->locale, $this->getFieldId(), $tdTemplate);

            // assign header lable for repeater
            $tdHeader = '<th>&nbsp;</th>'.$tdHeader."<th class='last'>&nbsp;</th>";
            $tableHeader .= $tdHeader;
        }
        $trBody = '';
        if (count($groups)) {
            // make row
            $i = 1;
            foreach ($groups as $groupId => $group) {
                $groupName = $this->locale.'[fields][%s][order][%s]';
                $groupOrder = $group['order'];
                $groupName = sprintf($groupName, $this->fieldId, $groupId);

                $fields = $group['fields'];
                $tdBody = '';
                foreach ($fields as $field) {
                    $field->setLabel('');
                    $tdBody .= sprintf("<td width='%s'>%s</td>", $columnWidth.'%', $field->render());
                }

                $tdFirstNew = sprintf($tdFirstBody, $i, $groupName, $groupName, $groupOrder);
                $tdBody = $tdFirstNew.$tdBody.$tdDeleteBtn;
                $trBody .= sprintf("<tr data-id='%s' class='another-field'>%s</tr>", $groupId, $tdBody);
                ++$i;
            }
        }
        $tableBody = $trBody.$htmlRepeaterTemplate;
        $htmlAddNewBtn = "<a  class ='btn btn-primary btn-flat' onclick=\"addRepeaterField('{$this->locale}_{$this->getFieldId()}')\">".trans('Add Item').'</a>';
        $tableFooter = sprintf("<td colspan='{$columns}'>%s</td>", $htmlAddNewBtn);

        $tableHtml = "<table  class='table-repeater ' id='{$repeaterTableId}' >
							<thead>%s</thead>
							<tbody class='sortable'>%s</tbody>
							<tfoot>%s</tfoot>
						  </table>";

        $html = sprintf($tableHtml, $tableHeader, $tableBody, $tableFooter);

        // assign index for repeater to make new item
        $repeaterIndexId = 'repeater_index_'.$this->locale.'_'.$this->getFieldId();

        $inputIndex = FormFacade::hidden($repeaterIndexId, 0, array('id' => $repeaterIndexId));
        $inputDelete = FormFacade::hidden($repeaterDeleteName, '', array('id' => $repeaterDeleteId));
        $html            .= $inputIndex.$inputDelete;

        $html            .=  sprintf("<script>$( document ).ready(function() {bindSortableForRepeater('%s');});</script>", $repeaterTableId);

        return $html;
    }

    // save field data to database ;
    public function save()
    {
        $bResult = false;
        try {
            if (!empty($this->deleteItems)) {
                $items = explode(',', $this->deleteItems);
                RepeaterTranslation::destroy($items);
            }
            if (count($this->groupFields)) {
                foreach ($this->groupFields as $groupId => $group) {
                    $fields = $group['fields'];
                    $order = $this->defaultOrder[$groupId];

                    $translate_id = 0;
                    foreach ($fields as $field) {
                        if ($translate_id) {
                            $field->setTranslateId($translate_id);
                        }
                        $field->save();
                        $translate_id = $field->getTranslateId();
                    }
                     // update order
                     $translate = RepeaterTranslation::find($translate_id);
                    $translate->order = $order;
                    $translate->save();
                }
            }
            $bResult = true;
        } catch (\Exception $e) {
            //exception handling
        }

        return $bResult;
    }

    public function getDisplayValue()
    {
        $values = array();

        if (count($this->groupFields)) {
            $i = 0;
            foreach ($this->groupFields as $key => $group) {
                $fields = $group['fields'];
                $item = array();
                foreach ($fields as $field) {
                    $item[$field->getFieldName()] = $field->getDisplayValue();
                }
                $item['id'] = $key;
                $values[$i] = $item;
                ++$i;
            }
        }

        return $values;
    }

    public function getErrorMessage()
    {
        $error = $this->getOption('error_message');

        return $error;
    }

    protected function getOrder()
    {
        return $this->order;
    }
}
