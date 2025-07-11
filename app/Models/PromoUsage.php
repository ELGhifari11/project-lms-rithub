<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoUsage extends Model
{
    /** @use HasFactory<\Database\Factories\PromoUsageFactory> */
    use HasFactory;

    protected $table = 'promo_usages';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'promo_id',
        'user_id',
        'order_id',
        'discount_amount',
        'used_at',
    ];

    protected $casts = [
        'discount_amount' => 'float',
        'used_at' => 'datetime',
    ];

    public $timestamps = false;


    /**
     * Get the promo associated with the promo usage.
     */
    public function promo()
    {
        return $this->belongsTo(Promo::class, 'promo_id');
    }

    /**
     * Get the user associated with the promo usage.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the order associated with the promo usage.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
