<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionSetting extends Model
{
    /** @use HasFactory<\Database\Factories\CommissionSettingFactory> */
    use HasFactory;

    // add fillable
    protected $fillable = [
        'item_type',
        'interval',
        'is_percentage',
        'fixed_commission',
        'platform_share',
        'is_active'
    ];
    // add guaded
    protected $guarded = ['id'];
    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }
}
