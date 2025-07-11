<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $table = 'categories';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'thumbnail_path'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function classes()
    {
        return $this->hasManyThrough(ClassModel::class, SubCategory::class, 'category_id', 'sub_category_id');
    }

    /**
     * Get all subcategories for this category.
     */
    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    /**
     * Scope a query to only include categories with a specific name.
     */
    public function scopeWithName($query, $name)
    {
        return $query->where('name', $name);
    }
}
