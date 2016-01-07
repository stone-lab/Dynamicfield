<?php

namespace Modules\Dynamicfield\Entities;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $table = 'dynamicfield__entities';
    protected $fillable = ['entity_id', 'field_id','entity_type'];

    public function fields()
    {
        return $this->hasMany('Modules\Dynamicfield\Entities\FieldTranslation', 'entity_field_id', 'id');
    }
    // relation with RepeaterFieldTranslation
    public function repeaters()
    {
        return $this->hasMany('Modules\Dynamicfield\Entities\RepeaterTranslation', 'entity_repeater_id', 'id');
    }

    public function defindFields()
    {
        return $this->belongsTo('Modules\Dynamicfield\Entities\Field', 'field_id', 'id');
    }
    public function getFieldByLocale($locale = 'en')
    {
        $fields = $this->fields()->where('locale', $locale)->get();
        if ($fields->count()) {
            $object = $fields[0];
        } else {
            $object = new FieldTranslation();
        }

        return $object;
    }

    public function getRepeatersByLocale($locale)
    {
        $repeaters = $this->repeaters()
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

    public function scopeGetEntity($query, $entityId, $entityType, $fieldId)
    {
        $entities = $query->where('entity_id', $entityId)
                        ->where('entity_type', $entityType)
                        ->where('field_id', $fieldId)->get();
        if ($entities->count()) {
            $object = $entities[0];
        } else {
            $object = new self();
        }

        return $object;
    }

    public function scopeGetFieldsByEntity($query, $entityId)
    {
        $entities = $query->where('entity_id', $entityId)->get();

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
