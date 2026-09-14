<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportingWo extends Model
{
    protected $table = 'reporting_wos';

    protected $fillable = [
        'no_order',
        'cid',
        'serial_number',
        'nama_teknisi',
        'nik_teknisi',
        'status_wo',
        'tanggal_sa',
        'vendor',
        'sektor',
        'cek_match',
    ];

    protected $casts = [
        'tanggal_sa' => 'date',
    ];

    /**
     * Relasi ke OntMasuk melalui serial_number
     */
    public function ontMasuk(): BelongsTo
    {
        return $this->belongsTo(OntMasuk::class, 'serial_number', 'serial_number');
    }

    /**
     * Relasi ke OntKeluar melalui serial_number
     */
    public function ontKeluar(): BelongsTo
    {
        return $this->belongsTo(OntKeluar::class, 'serial_number', 'serial_number');
    }

    /**
     * Cek apakah status WO adalah INSTALLED (Work Order Selesai)
     */
    public function isInstalled(): bool
    {
        return trim(strtolower($this->status_wo)) === 'work order selesai';
    }

    /**
     * Scope: search by keyword (no_order, serial_number, nama_teknisi, cid, nik_teknisi)
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (!$keyword) return $query;

        return $query->where(function ($q) use ($keyword) {
            $q->where('no_order', 'like', "%{$keyword}%")
              ->orWhere('serial_number', 'like', "%{$keyword}%")
              ->orWhere('nama_teknisi', 'like', "%{$keyword}%")
              ->orWhere('cid', 'like', "%{$keyword}%")
              ->orWhere('nik_teknisi', 'like', "%{$keyword}%");
        });
    }

    /**
     * Scope: filter status WO
     */
    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        if (!$status) return $query;

        if ($status === 'selesai' || $status === 'installed') {
            return $query->where('status_wo', 'like', '%selesai%');
        }

        if ($status === 'belum_selesai' || $status === 'not_installed') {
            return $query->where('status_wo', 'not like', '%selesai%');
        }

        return $query->where('status_wo', $status);
    }

    /**
     * Scope: filter berdasarkan teknisi
     */
    public function scopeFilterTeknisi(Builder $query, ?string $teknisi): Builder
    {
        if (!$teknisi) return $query;

        return $query->where('nama_teknisi', $teknisi);
    }
}
