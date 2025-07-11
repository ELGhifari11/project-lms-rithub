<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    use HasFactory;

    protected $table = 'user_subscriptions';


    protected $fillable = [
        'user_id',
        'order_id',
        'interval',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan model Bundle
    public function bundle()
    {
        return $this->belongsTo(Bundle::class);
    }

    // Relasi dengan model Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Scope untuk memfilter status aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope untuk memfilter status expired
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    // Scope untuk memfilter status canceled
    public function scopeCanceled($query)
    {
        return $query->where('status', 'canceled');
    }
}
