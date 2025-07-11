<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    /** @use HasFactory<\Database\Factories\BannerFactory> */
    use HasFactory;

    protected $table = 'banners';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'subtitle',
        'image_url',
        'target_type',
        'target_id',
        'link_url',
        'start_date',
        'end_date',
        'is_active',
        'order_index',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'target_id' => 'integer',
        'order_index' => 'integer',
    ];
    // Relasi Polimorfik (jika diperlukan)
    public function target()
    {
        return $this->morphTo();
    }

    // Menambahkan scope untuk status aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Menambahkan scope untuk urutan berdasarkan index
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index');
    }

    // Menambahkan mutator untuk memastikan URL memiliki format yang benar (opsional)
    public function setImageUrlAttribute($value)
    {
        $this->attributes['image_url'] = filter_var($value, FILTER_SANITIZE_URL);
    }

    // Menambahkan aksesors untuk mendapatkan waktu mulai dan selesai dalam format lebih ramah pengguna
    public function getStartDateFormattedAttribute()
    {
        return $this->start_date->format('d-m-Y H:i');
    }

    public function getEndDateFormattedAttribute()
    {
        return $this->end_date->format('d-m-Y H:i');
    }
}
