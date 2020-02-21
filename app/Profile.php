<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    public function movies()
    {
        return $this->hasMany(Movie::class);
    }
}
