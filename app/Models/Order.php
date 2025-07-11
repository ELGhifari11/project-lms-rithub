<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'promo_id',
        'total_amount',
        'discount_amount',
        'final_amount',
        'total_admin_fee',
        'payment_method',
        'payment_provider',
        'status',
        'payment_url,'
    ];

    protected $casts = [
        'midtrans_response' => 'array',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    /**
     * ============================
     *         Relationships
     * ============================
     */

    // Order belongs to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Order may have a promo
    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    // Order has many OrderItems
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Order has one subscription (indirectly)
    public function subscription()
    {
        return $this->hasOne(UserSubscription::class);
    }

    // Order can be used in a promo usage
    public function promoUsages()
    {
        return $this->hasMany(PromoUsage::class);
    }
}
