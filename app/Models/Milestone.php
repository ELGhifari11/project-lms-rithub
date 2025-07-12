<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    /** @use HasFactory<\Database\Factories\MilestoneFactory> */
    use HasFactory;

    protected $table = 'milestones';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'class_id',
        'title',
        'description',
        'learning_objectives',
        'requirements',
        'required_progress_percentage',
        'estimated_hours',
        'order_number',
        'resources',
        'difficulty_level',
        'is_mandatory',
        'is_active',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'resources' => 'array'
    ];

    /**
     * Get the class model associated with the milestone.
     */
    public function milestone_course()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the user milestones for the milestone.
     */
    public function userMilestones()
    {
        return $this->hasMany(UserMilestone::class, 'milestone_id');
    }


}
