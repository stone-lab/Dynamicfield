<?php namespace Modules\Dynamicfield\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class RepeaterField extends Model
{
    /* use Translatable; */

    protected $table = 'dynamicfield__repeater_fields';
    /* public $translatedAttributes = []; */
    protected $fillable = ['field_id','data','type','name','order'];

    public function Group()
    {
        return $this->belongsTo('Modules\Dynamicfield\Entities\Field', 'field_id', 'id');
    }

    public function getOptions()
    {
        $optionClass    =  "Modules\Dynamicfield\Utility\Enum\Options\\"  . ucfirst($this->type) ;

        $arrDefault    = $optionClass::getList();

        $jsonData        = (array) json_decode($this->data) ;
        $result = array_merge($arrDefault, $jsonData);

        return $result;
    }
}
