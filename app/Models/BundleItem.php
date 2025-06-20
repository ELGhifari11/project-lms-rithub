<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BundleItem extends Model
{
    use HasFactory;

    protected $table = 'bundle_items';


    protected $fillable = [
        'bundle_id',
        'item_type',
        'item_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    /**
     * Relasi ke bundle.
     */
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class);
    }

    /**
     * Polymorphic relasi ke item (class, category, sub_category, dll).
     */
    public function item()
    {
        return match ($this->item_type) {
            'class' => $this->belongsTo(ClassModel::class, 'item_id'),
            'category' => $this->belongsTo(Category::class, 'item_id'),
            'sub_category' => $this->belongsTo(SubCategory::class, 'item_id'),
            default => null, // 'all' atau kondisi fallback
        };
    }
}
