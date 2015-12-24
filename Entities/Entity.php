<?php

namespace Modules\Dynamicfield\Entities;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $table = 'dynamicfield__entities';
    protected $fillable = ['entity_id', 'field_id'];

    public function Fields()
    {
        return $this->hasMany('Modules\Dynamicfield\Entities\FieldTranslation', 'entity_field_id', 'id');
    }
    // relation with RepeaterFieldTranslation
    public function Repeaters()
    {
        return $this->hasMany('Modules\Dynamicfield\Entities\RepeaterTranslation', 'entity_repeater_id', 'id');
    }

    public function defindFields()
    {
        return $this->belongsTo('Modules\Dynamicfield\Entities\Field', 'field_id', 'id');
    }
    public function getFieldByLocale($locale = 'en')
    {
        $object = null;
        $fields = $this->Fields()->where('locale', $locale)->get();
        if ($fields->count()) {
            $object = $fields[0];
        } else {
            $object = new FieldTranslation();
        }

        return $object;
    }

    public function getRepeatersByLocale($locale)
    {
        $repeaters = $this->Repeaters()
                            ->where('locale', $locale)
                            ->orderBy('order')
                            ->get();

        return $repeaters;
    }
    public function getEntitiesByFieldId($fieldId)
    {
        $values = $this->where('field_id', $fieldId)->get();
        if ($values->count()) {
            $object = $values[0];
        } else {
            $object = new self();
        }

        return $object;
    }

    public function scopeGetEntity($query, $entity_id, $field_id)
    {
        $entities = $query->where('entity_id', $entity_id)
                        ->where('field_id', $field_id)->get();
        if ($entities->count()) {
            $object = $entities[0];
        } else {
            $object = new self();
        }

        return $object;
    }

    public function scopeGetFieldsByEntity($query, $entity_id)
    {
        $entities = $query->where('entity_id', $entity_id)->get();

        return $entities;
    }
    public function duplicate($pageId = 0)
    {
        $entity = $this->replicate();
        $entity->entity_id = $pageId;
        $entity->save();
        $type = $this->getFieldType();
        if ($type != 'repeater') {
            $fields = $this->Fields;

            foreach ($fields as $translation) {
                $tranReplicate = $translation->replicate();
                $tranReplicate->entity_field_id = $entity->id;
                $tranReplicate->save();
            }
        } else {
            $repeaters = $this->Repeaters;

            foreach ($repeaters as $repeater) {
                $fields = $repeater->FieldValues;

                $repeaterReplicate = $repeater->replicate();
                $repeaterReplicate->entity_repeater_id = $entity->id;
                $repeaterReplicate->save();
                foreach ($fields as $translation) {
                    $tranReplicate = $translation->replicate();
                    $tranReplicate->translation_id = $repeaterReplicate->id;
                    $tranReplicate->save();
                }
            }
        }

        return $entity;
    }

    public function getFieldType()
    {
        $field = $this->defindFields()->first();

        return $field->type;
    }
}
