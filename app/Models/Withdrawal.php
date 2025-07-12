<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Withdrawal extends Model
{
    /** @use HasFactory<\Database\Factories\WithdrawalFactory> */
    use HasFactory, LogsActivity;

    // add fillable
    protected $fillable = [
        'mentor_id',
        'wallet_id',
        'amount',
        'status',
        'note',
        'requested_at',
        'processed_at',
        'failure_code',
        'external_id',
    ];
    // add guaded
    protected $guarded = ['id'];
    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    // Withdrawal.php
    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnly([
                'mentor_id',
                'amount',
                'status',
                'note',
                'requested_at',
                'processed_at'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
