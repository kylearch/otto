<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function getLogoAttribute()
    {
        return asset("img/institutions/logos/{$this->id}.png");
    }
}
