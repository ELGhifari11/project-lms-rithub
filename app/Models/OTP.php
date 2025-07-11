<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{
    protected $table = 'o_t_p_s';

    protected $fillable = [
        'user_id',
        'unique_id',
        'otp',
        'type',
        'expires_at',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
