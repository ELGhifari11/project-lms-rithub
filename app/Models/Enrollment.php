<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    /** @use HasFactory<\Database\Factories\EnrollmentFactory> */
    use HasFactory;

    protected $table = 'enrollments';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'subscription_id',
        'status',
        'enrollable_type',
        'enrollable_id',
        'progress',
        'is_certificate_issued',
    ];

    // Definisikan relasi dengan model lain
    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get the parent enrollable model (Course or Class).
     */
    public function enrollable()
    {
        return $this->morphTo();
    }

    // Relasi ke model UserSubscription
    public function userSubscription()
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }

    // Mutator untuk mengatur status jika perlu
    public function getStatusAttribute($value)
    {
        return ucfirst($value); // Capitalize first letter of status
    }

    // Mutator untuk mengatur progress jika perlu
    public function getProgressAttribute($value)
    {
        return $value . '%'; // Display progress as percentage
    }
}
