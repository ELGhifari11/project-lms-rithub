<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    /** @use HasFactory<\Database\Factories\CertificateFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'class_id',
        'certificate_code',
        'certificate_url',
        'issued_at',

    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    /**
     * Get the user associated with the certificate.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the class model associated with the certificate.
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}
