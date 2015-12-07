<?php namespace Modules\Dynamicfield\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    /* use Translatable; */

    protected $table    = 'dynamicfield__groups';
    /* public $translatedAttributes = []; */
    protected $fillable = ['name','template'];

    public function Fields()
    {
        return $this->hasMany('Modules\Dynamicfield\Entities\Field', 'group_id', 'id');
    }
    public function getListFields()
    {
        $data = $this->Fields()->orderBy('order')->get();

        return $data;
    }
    public function scopeFindByTemplate($query, $template)
    {
        $groups =    $query->where('template', $template)->get();

        return $groups;
    }
}
