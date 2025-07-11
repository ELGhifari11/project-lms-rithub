<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Badge extends Model
{
    /** @use HasFactory<\Database\Factories\BadgeFactory> */
    use HasFactory;

    protected $table = 'badges';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'icon',
        'rule_type',
        'rule_value',
    ];

    protected $casts = [
        'rule_value' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

     /**
     * Get all user badges associated with this badge.
     */
    public function userBadges()
    {
        return $this->hasMany(UserBadge::class, 'badge_id');
    }
}
