<?php namespace Modules\Dynamicfield\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class RepeaterTranslation extends Model
{
    /* use Translatable; */

    protected $table = 'dynamicfield__repeater_translations';
    /* public $translatedAttributes = []; */
    protected $fillable = ['locale'];

    public function entity()
    {
        return $this->belongsTo('Modules\Dynamicfield\Entities\Entity', 'entity_repeater_id', 'id');
    }

    public function FieldValues()
    {
        return $this->hasMany('Modules\Dynamicfield\Entities\RepeaterValue', 'translation_id', 'id');
    }

    public function getFieldValue($field_id)
    {
        $object = new RepeaterValue();

        if (is_numeric($this->id)) {
            $repeatValues =    $this->FieldValues()->where('field_id', $field_id)->get();

            if ($repeatValues->count()) {
                $object = $repeatValues[0];
            }
        }

        return $object;
    }
}
