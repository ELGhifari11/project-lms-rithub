<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMilestone extends Model
{
    /** @use HasFactory<\Database\Factories\UserMilestoneFactory> */
    use HasFactory;

    protected $table = 'user_milestones';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'milestone_id',
        'achieved_at',
    ];

    protected $casts = [
        'achieved_at' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * Get the user associated with the milestone.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the milestone associated with the user.
     */
    public function milestone()
    {
        return $this->belongsTo(Milestone::class, 'milestone_id');
    }
}
