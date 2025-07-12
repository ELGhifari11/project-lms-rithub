<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBadge extends Model
{
    /** @use HasFactory<\Database\Factories\UserBadgeFactory> */
    use HasFactory;

    protected $table = 'user_badges';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'badge_id',
        'earned_at',
    ];

    public $timestamps = false;


    /**
     * Relasi ke model User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model Badge.
     */
    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

}
