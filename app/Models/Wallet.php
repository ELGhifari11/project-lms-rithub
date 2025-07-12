<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    /** @use HasFactory<\Database\Factories\WalletFactory> */
    use HasFactory;

    // add fillable
    protected $fillable = [
        'mentor_id',
        'bank_name',
        'account_holder_name',
        'bank_account_number',
        'balance'
    ];
    // add guaded
    protected $guarded = ['id'];
    // add hidden
    protected $hidden = ['created_at', 'updated_at'];


    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'wallet_id');
    }
}
