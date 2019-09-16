<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $guarded = [];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
