<?php

namespace Modules\Dynamicfield\Entities;

use Illuminate\Database\Eloquent\Model;

;

class Entity extends Model
{
    protected $table = 'dynamicfield__entities';
    protected $fillable = ['entity_id', 'field_id', 'entity_type'];
    /**
     * Create Relationship with FieldTranslation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fields()
    {
        return $this->hasMany('Modules\Dynamicfield\Entities\FieldTranslation', 'entity_field_id', 'id');
    }

    /**
     * Create Relationship with RepeaterTranslation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function repeaters()
    {
        return $this->hasMany('Modules\Dynamicfield\Entities\RepeaterTranslation', 'entity_repeater_id', 'id');
    }

    /**
     * Create Relationship with Field.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function defindFields()
    {
        return $this->belongsTo('Modules\Dynamicfield\Entities\Field', 'field_id', 'id');
    }

    /**
     * Get list fields by locale.
     *
     * @param string $locale
     *
     * @return FieldTranslation
     */
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

    public static function getAllDataFields($entityId, $entityType)
    {
        $fields = \DB::table('dynamicfield__entities')
            ->join('dynamicfield__field_translations', 'dynamicfield__entities.id', '=', 'dynamicfield__field_translations.entity_field_id')
            ->select('dynamicfield__entities.field_id', 'dynamicfield__field_translations.locale', 'dynamicfield__field_translations.value')
            ->where('dynamicfield__entities.entity_id', '=', $entityId)
            ->where('dynamicfield__entities.entity_type', '=', $entityType)
            ->get();

        return $fields;
    }

    public static function getAllDataTranactionRepeater($entityId, $entityType, $fieldId, $locale)
    {
        $fields = \DB::table('dynamicfield__entities')
            ->join('dynamicfield__repeater_translations', function ($join) use ($locale) {
                $join->on('dynamicfield__entities.id', '=', 'dynamicfield__repeater_translations.entity_repeater_id')->where('dynamicfield__repeater_translations.locale', '=', $locale);
            })
            ->select('dynamicfield__repeater_translations.id', 'dynamicfield__repeater_translations.locale')
            ->OrderBy('dynamicfield__repeater_translations.order')
            ->where('dynamicfield__entities.entity_id', '=', $entityId)
            ->where('dynamicfield__entities.field_id', '=', $fieldId)
            ->where('dynamicfield__entities.entity_type', '=', $entityType)
            ->get();

        return $fields;
    }

    public static function getAllDataTranactionRepeaterFields($entityId, $entityType, $fieldId, $locale)
    {
        $fields = \DB::table('dynamicfield__entities')
            ->join('dynamicfield__repeater_translations', function ($join) use ($locale) {
                $join->on('dynamicfield__entities.id', '=', 'dynamicfield__repeater_translations.entity_repeater_id')->where('dynamicfield__repeater_translations.locale', '=', $locale);
            })
            ->join('dynamicfield__repeater_values', 'dynamicfield__repeater_translations.id', '=', 'dynamicfield__repeater_values.translation_id')
            ->select('dynamicfield__repeater_values.*')
            ->where('dynamicfield__entities.entity_id', '=', $entityId)
            ->where('dynamicfield__entities.field_id', '=', $fieldId)
            ->where('dynamicfield__entities.entity_type', '=', $entityType)
            ->get();

        return $fields;
    }

    /**
     * Get list repeater field by locale.
     *
     * @param $locale
     *
     * @return mixed
     */
    public function getRepeatersByLocale($locale)
    {
        $repeaters = $this->repeaters()
                            ->where('locale', $locale)
                            ->orderBy('order')
                            ->get();

        return $repeaters;
    }

    /**
     * Get entities by fieldID.
     *
     * @param $fieldId
     *
     * @return Entity
     */
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

    /**
     * Find entities by entityID,type and fieldId.
     *
     * @param $query
     * @param $entityId
     * @param $entityType
     * @param $fieldId
     *
     * @return Entity
     */
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

    /**
     * Find list fields by EntityId.
     *
     * @param $query
     * @param $entityId
     *
     * @return mixed
     */
    public function scopeGetFieldsByEntity($query, $entityId)
    {
        $entities = $query->where('entity_id', $entityId)->get();

        return $entities;
    }

    /**
     * Duplicate field of page.
     *
     * @param int $pageId
     *
     * @return Model
     */
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

    /**
     * Get first value in Field model.
     *
     * @return mixed
     */
    public function getFieldType()
    {
        $field = $this->defindFields()->first();

        return $field->type;
    }
}
