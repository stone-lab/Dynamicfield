<?php namespace Modules\Dynamicfield\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class FieldTranslation extends Model
{
    /* use Translatable; */

    protected $table = 'dynamicfield__field_translations';
    /* public $translatedAttributes = []; */
    protected $fillable = ['entity_field_id','locale','value'];

    public function Entity()
    {
        return $this->belongsTo('Modules\Dynamicfield\Entities\Entity', 'entity_field_id', 'id');
    }
    /*  */
}
