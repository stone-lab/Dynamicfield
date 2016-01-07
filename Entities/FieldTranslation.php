<?php

namespace Modules\Dynamicfield\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Media\Support\Traits\MediaRelation;

class FieldTranslation extends Model
{
    use MediaRelation;
    protected $table = 'dynamicfield__field_translations';
    protected $fillable = ['entity_field_id', 'locale', 'value'];

    public function entity()
    {
        return $this->belongsTo('Modules\Dynamicfield\Entities\Entity', 'entity_field_id', 'id');
    }

    public function findFileByZoneForEntity($zone)
    {
        foreach ($this->files as $file) {
            if ($file->pivot->zone == $zone) {
                return $file;
            }
        }

        return '';
    }
}
