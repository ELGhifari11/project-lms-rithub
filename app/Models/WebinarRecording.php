<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebinarRecording extends Model
{
    /** @use HasFactory<\Database\Factories\WebinarRecordingFactory> */
    use HasFactory;

    protected $table = 'webinar_recordings';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'mentor_id',
        'sub_category_id',
        'is_preview',
        'thumbnail_path',
        'title',
        'content_path',
        'description',
        'price',
        'lifetime_price',
        'views',
        'duration',
        'status',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    /**
     * Get the class model associated with the webinar recording.
     */

     public function enrollments()
     {
        return $this->morphMany(Enrollment::class, 'enrollable');
     }

    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }
    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class,'sub_category_id');
    }

    // Menentukan akses ke kolom waktu otomatis
    public $timestamps = true;
}
