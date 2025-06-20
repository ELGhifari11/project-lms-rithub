<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    /** @use HasFactory<\Database\Factories\AuditLogFactory> */
    use HasFactory;

    protected $table = 'audit_logs';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'details',
        'ip_address',
    ];


    // Kolom yang harus di-cast ke tipe data tertentu
    protected $casts = [
        'details' => 'array',  // Menyimpan data JSON sebagai array
    ];

    // Menyembunyikan kolom tertentu dari array dan JSON output
    protected $hidden = [];

    // Relasi ke User (user yang melakukan aksi)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Mutator untuk ip_address jika diperlukan (contoh: untuk pemformatan)
    public function setIpAddressAttribute($value)
    {
        $this->attributes['ip_address'] = strtolower($value); // Contoh pemformatan IP address
    }

    // Scope untuk mencari berdasarkan user dan waktu
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

}
