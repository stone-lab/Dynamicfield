<?php

namespace Modules\Dynamicfield\Repositories\Eloquent;

use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Dynamicfield\Repositories\GroupFieldRepository;

class EloquentGroupFieldRepository extends EloquentBaseRepository implements GroupFieldRepository
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->orderBy('created_at', 'DESC')->get();
    }
}
