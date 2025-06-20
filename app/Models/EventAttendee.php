<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAttendee extends Model
{
    /** @use HasFactory<\Database\Factories\EventAttendeeFactory> */
    use HasFactory;

    protected $table = 'event_attendees';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'event_id',
        'user_id',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Menentukan relasi antara EventAttendee dan Event
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Menentukan relasi antara EventAttendee dan User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope untuk mendapatkan attendee dengan status tertentu
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
