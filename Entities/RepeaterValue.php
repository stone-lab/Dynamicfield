<?php namespace Modules\Dynamicfield\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class RepeaterValue extends Model
{
    /* use Translatable; */

    protected $table    = 'dynamicfield__repeater_values';
    protected $fillable = ['translation_id,field_id,value'];

    public function translation()
    {
        return $this->belongsTo('Modules\Dynamicfield\Entities\RepeaterTranslations', 'translation_id', 'id');
    }
}
