<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClassModel extends Model
{
    /** @use HasFactory<\Database\Factories\ClassModelFactory> */
    use HasFactory, LogsActivity;

    protected $table = 'class_models';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'thumbnail_path',
        'mentor_id',
        'sub_category_id',
        'duration_minutes',
        'price',
        'lifetime_price',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'thumbnail_path', 'mentor_id', 'sub_category_id', 'duration_minutes', 'price', 'status', 'created_at', 'updated_at']);
    }

    // public function getThumbnailPathAttribute($value)
    // {
    //     if (request()->is('admin/*') || app()->runningInConsole()) {
    //         return $value;
    //     }
    //     return $value ? asset('storage/' . ltrim($value, '/')) : null;
    // }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // public function bookmarks()
    // {
    //     return $this->morphMany(Bookmark::class, 'bookmarkable');
    // }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function classesTaught()
    {
        return $this->hasMany(ClassModel::class, 'mentor_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function enrollments()
    {
        return $this->morphMany(Enrollment::class, 'enrollable');
    }


    public function modules()
    {
        return $this->hasMany(ModuleOfCourse::class, 'class_id')->orderBy('order_index');
    }

    public function milestonesOfModule($moduleId = null)
    {
        $query = $this->modules();

        if ($moduleId) {
            $query->where('id', $moduleId);
        }

        return $query->with(['milestones' => function ($q) {
            $q->orderBy('order_index');
        }]);
    }

    public function allContents()
    {
        return EducationalContent::with('module')
            ->whereHas('module', function ($query) {
                $query->where('class_id', $this->id);
            })->get();
    }

    // public function feedbacks()
    // {
    //     return $this->hasMany(Feedback::class, 'class_id');
    // }

    // public function certificates()
    // {
    //     return $this->hasMany(Certificate::class, 'class_id');
    // }

    public function milestones()
    {
        return $this->hasMany(Milestone::class, 'class_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators (Optional)
    |--------------------------------------------------------------------------
    */
}
