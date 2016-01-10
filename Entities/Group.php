<?php

namespace Modules\Dynamicfield\Entities;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'dynamicfield__groups';
    protected $fillable = ['name'];

    public function fields()
    {
        return $this->hasMany('Modules\Dynamicfield\Entities\Field', 'group_id', 'id');
    }

    public function rules()
    {
        return $this->hasMany('Modules\Dynamicfield\Entities\Rule', 'group_id', 'id');
    }

    public function getListFields()
    {
        $data = $this->fields()->orderBy('order')->get();

        return $data;
    }

    public function scopeFindByTemplate($query, $template)
    {
        $groups = $query->where('template', $template)->get();

        return $groups;
    }
}
