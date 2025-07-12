<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $table = 'events';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sub_category_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'max_participants',
        'price',
        'status',
        'thumbanil_path',
        'is_done',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_done' => 'boolean',
    ];

    // Relationships

    /**
     * Get the sub-category that owns the event.
     */
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    /**
     * Get the attendees for the event.
     */
    public function attendees()
    {
        return $this->hasMany(EventAttendee::class, 'event_id');
    }

    /**
     * Get the banner associated with the event.
     */
    public function banner()
    {
        return $this->morphOne(Banner::class, 'target');
    }

    // Scopes

    /**
     * Scope a query to only include upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')
                     ->where('start_time', '>', now());
    }

    /**
     * Scope a query to only include ongoing events.
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing')
                     ->where('start_time', '<=', now())
                     ->where('end_time', '>=', now());
    }

    /**
     * Scope a query to only include completed events.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed')
                     ->where('end_time', '<', now());
    }

    /**
     * Scope a query to only include canceled events.
     */
    public function scopeCanceled($query)
    {
        return $query->where('status', 'canceled');
    }
}
