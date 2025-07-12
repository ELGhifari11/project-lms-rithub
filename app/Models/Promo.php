<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    /** @use HasFactory<\Database\Factories\PromoFactory> */
    use HasFactory;

    protected $table = 'promos';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'discount_type',
        'discount_value',
        'applies_to',
        'target_id',
        'min_purchase',
        'max_discount',
        'max_usage_total',
        'max_usage_per_user',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'max_usage_total' => 'integer',
        'max_usage_per_user' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public $timestamps = true;


    /*
     * Relationships
     */

     public function usages()
     {
         return $this->hasMany(PromoUsage::class);
     }

     public function orders()
     {
         return $this->hasMany(Order::class);
     }

     /*
      * Accessors (optional)
      */

     public function isCurrentlyActive(): bool
     {
         $now = now();
         return $this->is_active &&
             $this->start_date <= $now &&
             $this->end_date >= $now;
     }

     /*
      * Scopes (optional)
      */

     public function scopeActive($query)
     {
         return $query->where('is_active', true)
             ->where('start_date', '<=', now())
             ->where('end_date', '>=', now());
     }
}
