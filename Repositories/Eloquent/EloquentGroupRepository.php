<?php namespace Modules\Dynamicfield\Repositories\Eloquent;

use DB;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Dynamicfield\Entities\Field;
use Modules\Dynamicfield\Entities\Group;
use Modules\Dynamicfield\Entities\RepeaterField;
use Modules\Dynamicfield\Repositories\GroupRepository;

class EloquentGroupRepository extends EloquentBaseRepository implements GroupRepository
{
    /**
     * @param  int    $id
     * @return object
     */

    public function find($id)
    {
        return $this->model->find($id);
    }
    public function getGroup()
    {
        return $this->model ;
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
    public function saveData($group, $fields)
    {
        $bResult    = false;

        $groupId    = !empty($group['id'])?$group['id']:0;

        $itemsDeleted = $group["delete"];
        $itemsRepeaterDeleted = $group["delete_repeater"];

        DB::beginTransaction();
        try {
            if (!empty($itemsDeleted)) {
                $this->deleteFields($itemsDeleted);
            }
            if (!empty($itemsRepeaterDeleted)) {
                $this->deleteRepeaterField($itemsRepeaterDeleted);
            }
            $groupModel            = $this->model->firstOrNew(['id' => $groupId]);
            $groupModel->name        = $group["name"];
            $groupModel->template    = $group["template"];

            $groupModel->save();

            $group_id    =    $groupModel->id  ;
            $this->saveFields($group_id, $fields);
            DB::commit();
            $bResult    = true;
        } catch (\Exception $ex) {
            DB::rollback();
        }

        return $bResult ;
    }

    public function saveFields($group_id, $fields)
    {
        unset($fields["repeater_clone"]);
        unset($fields["field_clone"]);
        if (count($fields)) {
            foreach ($fields as $field) {
                $name        = $field["name"];
                $type        = $field["type"];
                $order        = $field["order"];
                $id            = $field['id']!='field_clone'?$field['id']:0;
                $fieldModel    = Field::firstOrNew(['id' => $id]);

                $fieldModel->group_id    = $group_id ;
                $fieldModel->name        = $name ;
                $fieldModel->type        = $type ;
                $fieldModel->order        = $order ;
                $fieldModel->data        = $this->getFieldData($field) ;
                $fieldModel->save();

                // save repeater fields

                $field_id = $fieldModel->id ;

                $this->saveRepeaterField($field_id, $field) ;
            }
        }
    }
    public function saveRepeaterField($field_id, $data)
    {
        if (isset($data["repeater"])) {
            $repeaters = $data["repeater"];
            unset($repeaters["repeater_clone"]);
            unset($repeaters["field_clone"]);

            if (count($repeaters)) {
                foreach ($repeaters as $field) {
                    $name        = $field["name"];
                    $type        = $field["type"];
                    $order        = $field["order"];
                    $id            = $field["id"];
                    $fieldModel    = RepeaterField::firstOrNew(['id' => $id]);
                    $fieldModel->field_id    = $field_id ;
                    $fieldModel->name        = $name ;
                    $fieldModel->type        = $type ;
                    $fieldModel->order        = $order ;
                    $fieldModel->data        = $this->getFieldData($field) ;
                    $fieldModel->save();
                }
            }
        }
    }

    private function deleteFields($strList)
    {
        $items = explode(",", $strList);

        return Field::destroy($items);
    }
    private function deleteRepeaterField($strList)
    {
        $items = explode(",", $strList);

        return RepeaterField::destroy($items);
    }
    private function getFieldData($field)
    {
        $type            = $field["type"];
        $optionClass    =  "Modules\Dynamicfield\Utility\Enum\Options\\"  . ucfirst($type) ;
        $options    = $optionClass::getKeys();

        $json =  array();
        foreach ($options as $k=>$v) {
            $json[$v] = $field[$v];
        }
        $strResult = json_encode($json) ;

        return $strResult;
    }
}
