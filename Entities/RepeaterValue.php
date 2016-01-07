<?php

namespace Modules\Dynamicfield\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Media\Support\Traits\MediaRelation;

class RepeaterValue extends Model
{
    use MediaRelation;
    protected $table = 'dynamicfield__repeater_values';
    protected $fillable = ['translation_id,field_id,value'];

    public function translation()
    {
        return $this->belongsTo('Modules\Dynamicfield\Entities\RepeaterTranslations', 'translation_id', 'id');
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
