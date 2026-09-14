<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OntKeluar extends Model
{
    protected $table = 'ont_keluars';

    protected $fillable = [
        'serial_number',
        'nama_teknisi',
        'tanggal_keluar',
        'keterangan',
        'catatan',
    ];

    protected $casts = [
        'tanggal_keluar' => 'date',
    ];

    /**
     * Relasi ke OntMasuk melalui serial_number
     */
    public function ontMasuk(): BelongsTo
    {
        return $this->belongsTo(OntMasuk::class, 'serial_number', 'serial_number');
    }

    /**
     * Apakah perangkat ini rusak?
     */
    public function isRusak(): bool
    {
        return $this->keterangan === 'Rusak';
    }

    /**
     * Scope: search by SN atau nama teknisi
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (!$keyword) return $query;

        return $query->where(function ($q) use ($keyword) {
            $q->where('serial_number', 'like', "%{$keyword}%")
              ->orWhere('nama_teknisi', 'like', "%{$keyword}%");
        });
    }

    /**
     * Scope: filter berdasarkan kondisi (normal / rusak)
     */
    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        if (!$status) return $query;

        if ($status === 'rusak') {
            return $query->where('keterangan', 'Rusak');
        }

        if ($status === 'normal') {
            return $query->whereNull('keterangan');
        }

        return $query;
    }
}
