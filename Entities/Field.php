<?php

namespace Modules\Dynamicfield\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Media\Support\Traits\MediaRelation;

class Field extends Model
{
    use MediaRelation;

    protected $table = 'dynamicfield__fields';
    protected $fillable = ['group_id', 'data', 'type', 'name', 'order'];

    public function group()
    {
        return $this->belongsTo('Modules\Dynamicfield\Entities\Group', 'group_id', 'id');
    }

    public function fields()
    {
        return $this->hasMany('Modules\Dynamicfield\Entities\RepeaterField', 'field_id', 'id');
    }
    public function getListFields()
    {
        $data = $this->fields()->orderBy('order')->get();

        return $data;
    }
    public function getOptions()
    {
        $result = array();
        $optionClass = "Modules\Dynamicfield\Utility\Enum\Options\\" . ucfirst($this->type);
        if (class_exists($optionClass)) {
            $arrDefault = $optionClass::getList();
            $jsonData = (array) json_decode($this->data);
            $result = array_merge($arrDefault, $jsonData);
        }

        return $result;
    }
}
