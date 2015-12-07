<?php namespace Modules\Dynamicfield\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    /* use Translatable; */

    protected $table        = 'dynamicfield__entities';
    /* public $translatedAttributes = []; */
    protected $fillable    = ['entity_id','field_id'];
    //public $timestamps 	= false;
    // relation with FieldValueTranslation
    public function Fields()
    {
        return $this->hasMany('Modules\Dynamicfield\Entities\FieldTranslation', 'entity_field_id', 'id');
    }
    // relation with RepeaterFieldTranslation
    public function Repeaters()
    {
        return $this->hasMany('Modules\Dynamicfield\Entities\RepeaterTranslation', 'entity_repeater_id', 'id');
    }

    public function getFieldByLocale($locale = "en")
    {
        $object    = null;
        $fields    =    $this->Fields()->where('locale', $locale)->get();
        if ($fields->count()) {
            $object = $fields[0];
        } else {
            $object = new FieldTranslation();
        }

        return $object;
    }

    public function getRepeatersByLocale($locale)
    {
        $repeaters    =    $this->Repeaters()
                            ->where('locale', $locale)
                            ->orderBy('order')
                            ->get();

        return $repeaters;
    }
    public function getEntitiesByFieldId($fieldId)
    {
        $object    =    null;
        $values    =    $this->where('field_id', $fieldId)->get();
        if ($values->count()) {
            $object = $values[0];
        } else {
            $object = new Entity();
        }

        return $object;
    }

    public function scopeGetEntity($query, $entity_id, $field_id)
    {
        $object    = null;
        $entities =    $query->where('entity_id', $entity_id)
                        ->where('field_id', $field_id)->get();
        if ($entities->count()) {
            $object = $entities[0];
        } else {
            $object = new Entity();
        }

        return $object;
    }

    public function scopeGetFieldsByEntity($query, $entity_id)
    {
        $entities =    $query->where('entity_id', $entity_id)->get();

        return $entities;
    }
}
