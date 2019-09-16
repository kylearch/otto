<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $guarded = [];

    protected $hidden = ['public_token', 'access_token', 'item_id'];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

}
