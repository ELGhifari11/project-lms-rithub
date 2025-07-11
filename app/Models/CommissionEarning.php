<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionEarning extends Model
{
    /** @use HasFactory<\Database\Factories\CommissionEarningFactory> */
    use HasFactory;

    // add fillable
    protected $fillable = [
        'mentor_id',
        'order_id',
        'amount',
        'status'
    ];
    // add guaded
    protected $guarded = ['id'];
    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
