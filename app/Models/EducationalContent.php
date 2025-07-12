<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationalContent extends Model
{
    /** @use HasFactory<\Database\Factories\EducationalContentFactory> */
    use HasFactory;

    protected $table = 'educational_contents';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'module_of_course_id',
        'type',
        'title_content',
        'order_index',
        'thumbnail_path',
        'content_path',
        'duration',
        'is_preview'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // public function getThumbnailPathAttribute($value)
    // {
    //     if (request()->is('admin/*') || app()->runningInConsole()) {
    //         return $value;
    //     }
    //     return $value ? asset('storage/' . ltrim($value, '/')) : null;
    // }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index_of_module', 'asc');
    }

    /**
     * Get the class model associated with the educational content.
     */
    public function module()
    {
        return $this->belongsTo(ModuleOfCourse::class, 'module_of_course_id');
    }

    public function class()
    {
        return $this->module->class();
    }
}
