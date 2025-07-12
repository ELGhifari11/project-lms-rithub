<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bundle extends Model
{
    use HasFactory;


    protected $table = 'bundles';

    protected $fillable = [
        'name',
        'description',
        'type',
        'total_price',
        'validity_days',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'validity_days' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get all items inside the bundle.
     */
    public function items()
    {
        return $this->hasMany(BundleItem::class);
    }

    public function enrollments()
    {
        return $this->morphMany(Enrollment::class, 'enrollable');
    }

    /**
     * Get all user subscriptions associated with this bundle.
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Scope for active bundles only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Accessor for readable type label (optional).
     */
    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'single_class' => 'Single Class',
            'category' => 'Category',
            'sub_category' => 'Sub-category',
            'full_access' => 'Full Access',
            'custom' => 'Custom',
            default => ucfirst($this->type),
        };
    }
}
