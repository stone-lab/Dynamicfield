<?php namespace Modules\Dynamicfield\Utility\Fields;

use Collective\Html\FormFacade;
use Modules\Dynamicfield\Entities\Entity;
use Modules\Dynamicfield\Entities\RepeaterTranslation;

class Repeater extends FieldBase
{
    protected $_options;
    protected $_fieldModel;
    protected $_value;
    protected $_field_type;
    protected $_field_label;
    protected $_field_id;
    protected $_group_field_id;
    protected $_locale;
    protected $_isValid = true;
    protected $_html_item_template = "<tr>%s</tr>";

    protected $_groupFields = array();
    protected $_fieldValues = array();

    protected $_delete_items = "";
    protected $_defaultOrder = "";

    public function init($default= null)
    {
        $this->_initRepeatFields($this->_field, $default);
    }

    //init data per group
    private function _initRepeatFields($field_info, $default=null)
    {
        $controls = array();
        $repeater_id        =    $field_info->id ;
        $group_name        =    $field_info->name ;

        $entity        = Entity::getEntity($this->_entity_id, $repeater_id) ;
        $repeaters      = $entity->getRepeatersByLocale($this->_locale);

        $post_data  = @$default["value"] ;
        $this->_defaultOrder =  @$default["order"] ;
        if (isset($post_data)) {
            unset($post_data["clone"]);
            $this->_delete_items = $default["delete"];

            foreach ($post_data as $k=>$control) {
                $_listDefault = $post_data[$k] ;
                $controls[$k]['fields'] = $this->_createListControlAfterPostData($k, $_listDefault);
                $controls[$k]['order'] = -1;
            }
        } else {
            if ($repeaters->count()) {
                $i=1;
                foreach ($repeaters as $repeater) {
                    $controls[$repeater->id]['fields'] = $this->_createListControl($repeater);
                    $controls[$repeater->id]['order']  = $i;
                    $i++;
                }
            }
        }

        $this->_groupFields = $controls;

        $this->_repeaterHeaders  = $this->_createHeaderRepeater();
    }

    // create list controller when user post data from browser

    private function _createListControlAfterPostData($repeaterId, $default =null)
    {
        $repeater_field    = $this->_field;
        $name_format = "%s[fields]" . sprintf("[%s][value][%s]", $this->getFieldId(), $repeaterId) . "[%s][value]" ;
        $id_format = "%s_" . sprintf("%s_%s_", $this->getFieldId(), $repeaterId) . "_%s_value" ;
        //setHtmlNameFormat

        $filedOfRepeater = $repeater_field->getListFields();
        if ($filedOfRepeater->count()) {
            foreach ($filedOfRepeater as $field) {
                $_listDefault = $default[$field->id] ;

                $field_control = $this->_createFieldControl($field, $repeaterId, $_listDefault);
                $field_control->setHtmlNameFormat($name_format);
                $field_control->setHtmlIdFormat($id_format);

                $controls[$field->id] = $field_control;
            }
        }

        return $controls;
    }

    private function _createListControl($repeater)
    {
        $repeater_field    = $this->_field;
        $name_format = "%s[fields]" . sprintf("[%s][value][%s]", $this->getFieldId(), $repeater->id) . "[%s][value]" ;
        $id_format = "%s_" . sprintf("%s_%s_", $this->getFieldId(), $repeater->id) . "_%s_value" ;
        //setHtmlNameFormat

        $filedOfRepeater = $repeater_field->getListFields();
        if ($filedOfRepeater->count()) {
            foreach ($filedOfRepeater as $field) {
                $field_control = $this->_createFieldControl($field, $repeater->id);
                $field_control->setHtmlItemTemplate("%s");
                $field_control->setHtmlNameFormat($name_format);
                $field_control->setHtmlIdFormat($id_format);

                $value = $repeater->getFieldValue($field->id);
                $field_control->setValue($value->value);

                $controls[$field->id] = $field_control;
            }
        }

        return $controls;
    }

    private function _createFieldControl($field, $translateId, $default=null)
    {
        $field_control    = null;
        //$field_data		=  	$this->_getFieldPostData($default);
        $field_value    = $default ;

        switch ($field->type) {
            case 'text':
                $field_control = new Text($field, $this->_entity_id, $this->_locale);
                break;
            case 'number':
                $field_control = new Number($field, $this->_entity_id, $this->_locale);
                break;
            case 'textarea':
                $field_control = new Textarea($field, $this->_entity_id, $this->_locale);
                break;
            case 'wysiwyg':
                $field_control = new Wysiwyg($field, $this->_entity_id, $this->_locale);
                break;
            case 'file':
                $field_control = new File($field, $this->_entity_id, $this->_locale);
                break;
            case 'image':
                $field_control = new Image($field, $this->_entity_id, $this->_locale);
                break;
        }
        $field_control->setRepeaterId($this->_field_id);
        $field_control->setTranslateId($translateId);

        $field_control->init($field_value);

        return $field_control ;
    }
    private function _createHeaderRepeater()
    {
        $repeater = new RepeaterTranslation();
        //TODO Here was ='clone'
        $repeater->id = 'clone';
        $controls = $this->_createListControl($repeater);

        return $controls ;
    }
    // valid for group field
    public function valid()
    {
        $isValid  = true;

        /* if(count($this->_groupFields)){
            foreach($this->_groupFields as $group){
                $fields = $group["fields"] ;
                foreach($fields as $field){
                    $isValid = $field->valid();
                    if(!$isValid){
                        break;
                    }
                }
            }
        } */
        return $isValid;
    }

    // render for group field
    public function render()
    {
        $table_header    = "";
        $html_repeater_template  = "";
        $table_body    = "";
        $table_footer    = "";
        $td_first = "<td>&nbsp;</td>";
        $td_first_body ="<td class='field-order'>
                        <label class='circle' >%s</label>
                        <input type='hidden' name='%s' id='%s' value='%s'/>
                </td>";
        $td_last = "<td class='last'>&nbsp;</td>";
        $table_footer    = "";
        $repeater_delete_id    =  "repeater_delete_" . $this->_locale  . "_" . $this->getFieldId();
        $repeater_table_id    =  "repeater_table_" . $this->_locale  . "_" . $this->getFieldId();
        $repeater_delete_name    = sprintf("%s[fields][%s][delete]", $this->_locale, $this->getFieldId());
        $td_delete_btn    = "<td class='last'>
							<a class='btn-delete' onclick=\"deleteRepeaterField('{$this->_locale}_{$this->getFieldId()}',this)\">
								<span class='glyphicon glyphicon-minus'></span>
							</a>
						</td>";
        $groups  = $this->_groupFields ;

        $headers = $this->_repeaterHeaders;
        $columns = count($headers) +2  ;
        $table_header ="<tr class='repeater-group'>
							<th colspan='{$columns}'>" . $this->getLabel() . "</th>
						</tr>";

        $columnWidth = 50;
        if (count($headers)) {
            $columnWidth = 100/($columns-2);
             // make header
            $td_header = "";
            $td_template = "";
            foreach ($headers as $field) {
                $td_header .= sprintf("<th class='caption' width='%s'>%s</th>", $columnWidth . "%", $field->getLabel());
                $field->setLabel("");
                $td_template .= sprintf("<td width='%s'>%s</td>", $columnWidth . "%", $field->render());
            }

            $group_name = $this->_locale . "[fields][%s][order][%s]";
            $group_name = sprintf($group_name, $this->_field_id, "clone");
            // assign template to create new item of repeater
            $td_firt_new = sprintf($td_first_body, -1, $group_name, $group_name, -1);
            $td_template = $td_firt_new . $td_template . $td_delete_btn ;
            $repeater_format= "<tr class='repeater-template' id='repeater_template_%s_%s'>%s</tr>" ;
            $html_repeater_template = sprintf($repeater_format, $this->_locale, $this->getFieldId(), $td_template);

            // assign header lable for repeater
            $td_header = "<th>&nbsp;</th>" . $td_header . "<th class='last'>&nbsp;</th>" ;
            $table_header .= $td_header ;
        }
        $body_tr ="";
        if (count($groups)) {
            // make row
            $i=1;
            foreach ($groups as $groupId=>$group) {
                $group_name = $this->_locale . "[fields][%s][order][%s]";
                $group_order = $group["order"] ;
                $group_name = sprintf($group_name, $this->_field_id, $groupId);

                $fields = $group["fields"] ;
                $body_td = "";
                foreach ($fields as $field) {
                    $field->setLabel("");
                    $body_td .= sprintf("<td width='%s'>%s</td>", $columnWidth . "%", $field->render());
                }

                $td_firt_new = sprintf($td_first_body, $i, $group_name, $group_name, $group_order);
                $body_td =  $td_firt_new . $body_td . $td_delete_btn ;
                $body_tr .=sprintf("<tr data-id='%s' class='another-field'>%s</tr>", $groupId, $body_td);
                $i++;
            }
        }
        $table_body =  $body_tr . $html_repeater_template;

        $html_addnew_btn = "<a  class ='btn btn-primary btn-flat' onclick=\"addRepeaterField('{$this->_locale}_{$this->getFieldId()}')\">" . trans('Add Item') . "</a>";

        $table_footer = sprintf("<td colspan='{$columns}'>%s</td>", $html_addnew_btn);
        $table_footer =  $table_footer ;

        $table_html    = "<table  class='table-repeater ' id='{$repeater_table_id}' >
							<thead>%s</thead>
							<tbody class='sortable'>%s</tbody>
							<tfoot>%s</tfoot>
						  </table>";

        $html = sprintf($table_html, $table_header, $table_body, $table_footer) ;

        // assign index for repeater to make new item
        $repeater_index_id    =  "repeater_index_" . $this->_locale  . "_" . $this->getFieldId();

        $input_index = FormFacade::hidden($repeater_index_id, 0, array('id'=>$repeater_index_id));
        $input_delete = FormFacade::hidden($repeater_delete_name, "", array('id'=>$repeater_delete_id));
        $html            .= $input_index . $input_delete;

        $html            .=  sprintf("<script>$( document ).ready(function() {bindSortableForRepeater('%s');});</script>", $repeater_table_id);

        return $html;
    }

    // save field data to database ;
    public function save()
    {
        $bResult = false;
        $abort_save = false;

        try {
            if (!empty($this->_delete_items)) {
                $items = explode(",", $this->_delete_items);
                RepeaterTranslation::destroy($items);
            }
            if (count($this->_groupFields)) {
                foreach ($this->_groupFields as $groupId=>$group) {
                    $fields = $group["fields"];
                    $order = $this->_defaultOrder[$groupId];

                    $translate_id     = 0;
                    foreach ($fields as $field) {
                        if ($translate_id) {
                            $field->setTranslateId($translate_id);
                        }
                        $field->save();
                        $translate_id  = $field->getTranslateId();
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

        return $bResult ;
    }

    public function getDisplayValue()
    {
        $values = array();

        if (count($this->_groupFields)) {
            $i =0;
            foreach ($this->_groupFields as $key=>$group) {
                $fields = $group["fields"];
                $item = array();
                foreach ($fields as $field) {
                    $item[$field->getFieldName()] = $field->getDisplayValue();
                }
                $item['id'] = $key;
                $values[$i] = $item;
                $i++;
            }
        }

        return $values;
    }

    public function getErrorMessage()
    {
        $error ="";
        $error = $this->getOption("error_message") ;

        return $error ;
    }

    protected function getOrder()
    {
        return $this->_order;
    }
}
