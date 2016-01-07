<?php

namespace Modules\Dynamicfield\Entities;

use Illuminate\Database\Eloquent\Model;

class RepeaterTranslation extends Model
{
    protected $table = 'dynamicfield__repeater_translations';
    protected $fillable = ['entity_repeater_id','locale','order'];

    public function entity()
    {
        return $this->belongsTo('Modules\Dynamicfield\Entities\Entity', 'entity_repeater_id', 'id');
    }

    public function fieldValues()
    {
        return $this->hasMany('Modules\Dynamicfield\Entities\RepeaterValue', 'translation_id', 'id');
    }

    public function getFieldValue($fieldId)
    {
        $object = new RepeaterValue();
        if (is_numeric($this->id)) {
            $repeatValues = $this->fieldValues()->where('field_id', $fieldId)->get();
            if ($repeatValues->count()) {
                $object = $repeatValues[0];
            }
        }

        return $object;
    }
}
