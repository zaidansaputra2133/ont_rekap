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
     */
    public function sudahKeluar(): bool
    {
        return \App\Models\OntKeluar::where('serial_number', $this->serial_number)->exists();
    }

    /**
     * Deteksi merek ONT otomatis berdasarkan prefix Serial Number standar Telkom Akses.
     * - ZTE: diawali 'ZTE'
     * - Fiberhome: diawali 'FHTT'
     * - Nokia: diawali 'ALCL'
     * - Huawei: diawali '48575443' (hex GPON SN) atau 'HWTC'
     */
    public static function detectBrand(?string $serialNumber): ?string
    {
        if (!$serialNumber) {
            return null;
        }

        $sn = strtoupper(trim($serialNumber));

        if (str_starts_with($sn, 'ZTE')) {
            return 'ZTE';
        }
        if (str_starts_with($sn, 'FHTT')) {
            return 'Fiberhome';
        }
        if (str_starts_with($sn, 'ALCL')) {
            return 'Nokia';
        }
        if (str_starts_with($sn, '48575443') || str_starts_with($sn, 'HWTC')) {
            return 'Huawei';
        }

        return null;
    }
}
