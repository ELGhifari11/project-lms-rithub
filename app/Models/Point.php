<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    /** @use HasFactory<\Database\Factories\PointFactory> */
    use HasFactory;

    protected $table = 'points';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'source',
        'description',
        'point_value',
        'created_at',
    ];

    public $timestamps = false;

    /**
     * Enum values for the 'source' column.
     */
    public const SOURCE_JOIN_CLASS = 'join_class';
    public const SOURCE_TESTIMONI = 'testimoni';
    public const SOURCE_BUY_BUNDLE = 'buy_bundle';
    public const SOURCE_EVENT = 'event';
    public const SOURCE_OTHER = 'other';

    /**
     * Available sources for points.
     *
     * @return array
     */
    public static function sources(): array
    {
        return [
            self::SOURCE_JOIN_CLASS,
            self::SOURCE_TESTIMONI,
            self::SOURCE_BUY_BUNDLE,
            self::SOURCE_EVENT,
            self::SOURCE_OTHER,
        ];
    }


    /**
     * Get the user associated with the point.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
