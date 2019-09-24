<?php

namespace App\Models;

class IllegalVehicle extends Model
{
    public function detachments()
    {
        return $this->hasOne('App\Models\Detachment', 'id', 'detachment_id');
    }

    public function users()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
