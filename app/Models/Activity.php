<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'user_id',
        'activity_date',
        'points',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeToday($query)
    {
        return $query->whereDate('activity_date', today());
    }
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('activity_date', now()->month);
    }
    public function scopeThisYear($query)
    {
        return $query->whereYear('activity_date', now()->year);
    }
}
