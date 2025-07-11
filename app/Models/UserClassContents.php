<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserClassContents extends Model
{
    protected $fillable = [
        'user_id',
        'class_id',
        'content_id'
    ];

    protected $casts = [
        'completed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function content()
    {
        return $this->belongsTo(EducationalContent::class, 'content_id');
    }
}
