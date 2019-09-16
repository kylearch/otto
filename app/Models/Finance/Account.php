<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{

    const STATUS_ACTIVE = 0;
    const STATUS_CLOSED = 1;
    const STATUS_HIDDEN = 2;

    const STATUSES = [
        self::STATUS_ACTIVE => 'active',
        self::STATUS_CLOSED => 'closed',
        self::STATUS_HIDDEN => 'hidden',
    ];

    protected $guarded = [];

    protected $hidden = ['account_id', 'item_id', 'institution_id'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function balances()
    {
        return $this->hasMany(Balance::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getMaskAttribute()
    {
        return str_pad($this->attributes['mask'], 4, 0, STR_PAD_LEFT);
    }

    public function getLogoAttribute()
    {
        return asset("img/institutions/logos/{$this->attributes['institution_id']}.png");
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_HIDDEN]);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    public function isOpen()
    {
        return in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_HIDDEN], true);
    }

    public function isClosed()
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function isHidden()
    {
        return $this->status === self::STATUS_HIDDEN;
    }

}
