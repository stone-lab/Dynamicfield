<?php namespace Modules\Dynamicfield\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    /* use Translatable; */

    protected $table = 'dynamicfield__fields';
    /* public $translatedAttributes = []; */
    protected $fillable = ['group_id','data','type','name','order'];

    public function Group()
    {
        return $this->belongsTo('Modules\Dynamicfield\Entities\Group', 'group_id', 'id');
    }

    public function Fields()
    {
        return $this->hasMany('Modules\Dynamicfield\Entities\RepeaterField', 'field_id', 'id');
    }
    public function getListFields()
    {
        $data = $this->Fields()->orderBy('order')->get();

        return $data;
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
