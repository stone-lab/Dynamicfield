<?php

namespace Modules\Dynamicfield\Entities;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $table    = 'dynamicfield__rules';
    protected $fillable = ['group_id', 'rule'];

    public function group()
    {
        return $this->belongsTo('Modules\Dynamicfield\Entities\Group', 'group_id', 'id');
    }
}
