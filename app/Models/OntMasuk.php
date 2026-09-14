<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OntMasuk extends Model
{
    protected $table = 'ont_masuks';

    protected $fillable = [
        'serial_number',
        'brand',
        'tanggal_masuk',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];

    /**
     * Scope: filter berdasarkan Serial Number atau Brand
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (!$keyword) return $query;

        return $query->where(function ($q) use ($keyword) {
            $q->where('serial_number', 'like', "%{$keyword}%")
              ->orWhere('brand', 'like', "%{$keyword}%");
        });
    }

    /**
     * Scope: filter berdasarkan brand spesifik
     */
    public function scopeFilterBrand(Builder $query, ?string $brand): Builder
    {
        if (!$brand) return $query;

        return $query->where('brand', $brand);
    }

    /**
     * Cek apakah ONT ini sudah pernah keluar (ada di ont_keluars)
     * Sementara return false sampai tabel ont_keluars dibuat
     */
    public function sudahKeluar(): bool
    {
        // Akan diimplementasikan saat backend ONT Keluar dibuat
        return false;
    }
}
