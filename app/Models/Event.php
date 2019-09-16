<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded = [];

    public function calendar()
    {
        return $this->belongsTo(Calendar::class);
    }
}
