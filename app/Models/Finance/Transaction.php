<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Tags\HasTags;

class Transaction extends Model
{
    use SoftDeletes, HasTags;

    protected $guarded = [];

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function getAmountAttribute() {
        return $this->account->type === 'depository' ? $this->attributes['amount'] * -1 : $this->attributes['amount'];
    }

    public function scopeToday($query)
    {
        return $query->where('date', now()->format('Y-m-d'));
    }
}
