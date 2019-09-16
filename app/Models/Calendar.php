<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $guarded = [];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
