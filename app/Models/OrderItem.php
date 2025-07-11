<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory;

    public $timestamps = false; // karena tabel tidak punya kolom updated_at


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'item_type',
        'item_id',
        'interval',
        'amount',
        'price',
        'admin_fee',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * ============================
     *         Relationships
     * ============================
     */

    // OrderItem belongs to Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Polymorphic relation to the item (Class or Bundle)
    public function item()
    {
        return $this->morphTo(null, 'item_type', 'item_id');
    }
}
