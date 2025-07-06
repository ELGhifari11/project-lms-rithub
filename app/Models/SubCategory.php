<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    /** @use HasFactory<\Database\Factories\SubCategoryFactory> */
    use HasFactory;

    protected $table = 'sub_categories';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'thumbnail_path'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


   /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Parent category of the subcategory.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Classes that belong to this subcategory.
     */
    // public function classes()
    // {
    //     return $this->hasMany(ClassModel::class, 'sub_category_id');
    // }

    /**
     * Events that are categorized under this subcategory.
     */
    // public function events()
    // {
    //     return $this->hasMany(Event::class, 'sub_category_id');
    // }

    /**
     * Webinars that are categorized under this subcategory.
     */
    // public function webinars()
    // {
    //     return $this->hasMany(WebinarRecording::class,'sub_category_id');
    // }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators (Optional)
    |--------------------------------------------------------------------------
    */


}

