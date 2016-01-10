<?php

namespace Modules\Dynamicfield\Entities;

use Illuminate\Database\Eloquent\Model;

class RepeaterField extends Model
{
    protected $table = 'dynamicfield__repeater_fields';
    protected $fillable = ['field_id', 'data', 'type', 'name', 'order'];

    public function group()
    {
        return $this->belongsTo('Modules\Dynamicfield\Entities\Field', 'field_id', 'id');
    }

    public function getOptions()
    {
        $opitionClass = "Modules\Dynamicfield\Utility\Enum\Options\\" . ucfirst($this->type);
        $arrDefault = $opitionClass::getList();
        $jsonData = (array) json_decode($this->data);
        $result = array_merge($arrDefault, $jsonData);

        return $result;
    }
}
