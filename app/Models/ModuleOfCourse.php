<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleOfCourse extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleOfCourseFactory> */
    use HasFactory;

    // add fillable
    protected $fillable = [
        'class_id',
        'title',
        'description',
        'order_index'
    ];

    // add guaded
    protected $guarded = ['id'];

    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function contents()
    {
        return $this->hasMany(EducationalContent::class, 'module_of_course_id')
            ->orderBy('order_index');
    }

    protected $with = ['contents'];


    public function milestones()
    {
        return $this->hasMany(Milestone::class,'module_of_course_id')->orderBy('order_index');
    }
}

