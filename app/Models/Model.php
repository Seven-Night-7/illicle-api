<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model AS EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Model extends EloquentModel
{
    use SoftDeletes;

    protected $hidden = ['deleted_at'];
}
