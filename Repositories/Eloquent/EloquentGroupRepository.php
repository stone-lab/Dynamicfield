<?php

namespace Modules\Dynamicfield\Repositories\Eloquent;

use DB;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Dynamicfield\Entities\Field;
use Modules\Dynamicfield\Entities\RepeaterField;
use Modules\Dynamicfield\Repositories\GroupRepository;
use Modules\Dynamicfield\Entities\Rule;

class EloquentGroupRepository extends EloquentBaseRepository implements GroupRepository
{
    /**
     * @param int $id
     *
     * @return object
     */
    public function find($id)
    {
        return $this->model->find($id);
    }
    public function getGroup()
    {
        return $this->model;
    }
    public function getFields()
    {
        return $this->model->Fields;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->orderBy('id', 'DESC')->get();
    }
    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function saveData($group, $fields, $locations)
    {
        $bResult = false;
        $groupId = !empty($group['id']) ? $group['id'] : 0;
        $itemsDeleted = $group['delete'];
        $itemsRepeaterDeleted = $group['delete_repeater'];

        DB::beginTransaction();
        try {
            if (!empty($itemsDeleted)) {
                $this->deleteFields($itemsDeleted);
            }
            if (!empty($itemsRepeaterDeleted)) {
                $this->deleteRepeaterField($itemsRepeaterDeleted);
            }
            $groupModel = $this->model->firstOrNew(['id' => $groupId]);
            $groupModel->name = $group['name'];
            //$groupModel->template = $group['template'];

            $groupModel->save();

            $groupId = $groupModel->id;
            $this->saveFields($groupId, $fields);
            $this->saveLocations($groupId, $locations);
            DB::commit();
            $bResult = true;
        } catch (\Exception $ex) {
            DB::rollback();
        }

        return $bResult;
    }

    public function saveFields($groupId, $fields)
    {
        unset($fields['repeater_clone']);
        unset($fields['field_clone']);
        if (count($fields)) {
            foreach ($fields as $field) {
                $name = $field['name'];
                $type = $field['type'];
                $order = $field['order'];
                $id = $field['id'] != 'field_clone' ? $field['id'] : 0;
                $fieldModel = Field::firstOrNew(['id' => $id]);

                $fieldModel->group_id = $groupId;
                $fieldModel->name = $name;
                $fieldModel->type = $type;
                $fieldModel->order = $order;
                $fieldModel->data = $this->getFieldData($field);
                $fieldModel->save();

                $fieldId = $fieldModel->id;
                $this->saveRepeaterField($fieldId, $field);
            }
        }
    }

    public function saveLocations($groupId, $locations)
    {
        Rule::where('group_id', '=', $groupId)->delete();
        if (count($locations)) {
            foreach ($locations as $location) {
                $ruleModel = new Rule();
                $strResult = json_encode($location);
                $ruleModel->group_id = $groupId;
                $ruleModel->rule = $strResult;
                $ruleModel->save();
            }
        }
    }

    public function saveRepeaterField($fieldId, $data)
    {
        if (isset($data['repeater'])) {
            $repeaters = $data['repeater'];
            unset($repeaters['repeater_clone']);
            unset($repeaters['field_clone']);

            if (count($repeaters)) {
                foreach ($repeaters as $field) {
                    $name = $field['name'];
                    $type = $field['type'];
                    $order = $field['order'];
                    $id = $field['id'];
                    $fieldModel = RepeaterField::firstOrNew(['id' => $id]);
                    $fieldModel->field_id = $fieldId;
                    $fieldModel->name = $name;
                    $fieldModel->type = $type;
                    $fieldModel->order = $order;
                    $fieldModel->data = $this->getFieldData($field);
                    $fieldModel->save();
                }
            }
        }
    }

    private function deleteFields($strList)
    {
        $items = explode(',', $strList);
        \Debugbar::info($items);
        \Debugbar::info('go to detroy');

        return Field::destroy($items);
    }
    private function deleteRepeaterField($strList)
    {
        $items = explode(',', $strList);

        return RepeaterField::destroy($items);
    }

    private function getFieldData($field)
    {
        $type = $field['type'];
        $opitionClass = "Modules\Dynamicfield\Utility\Enum\Options\\".ucfirst($type);
        $options = $opitionClass::getKeys();

        $json = array();
        foreach ($options as $k => $v) {
            $json[$v] = $field[$v];
        }
        $strResult = json_encode($json);

        return $strResult;
    }
}
