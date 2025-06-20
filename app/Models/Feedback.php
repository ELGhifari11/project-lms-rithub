<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    /** @use HasFactory<\Database\Factories\FeedbackFactory> */
    use HasFactory;

    protected $table = 'feedbacks';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'class_id',
        'rating',
        'comment',
        'is_approved',
        'anonymous',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'rating' => 'float',
        'is_approved' => 'boolean',
        'anonymous' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user associated with the feedback.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the class model associated with the feedback.
     */
    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    // ================================
    // Scopes
    // ================================

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeAnonymous($query)
    {
        return $query->where('anonymous', true);
    }


    // ================================
    // Accessors & Mutators (Optional)
    // ================================

    public function getRatingStarsAttribute()
    {
        return str_repeat('⭐', (int) round($this->rating));
    }

}
