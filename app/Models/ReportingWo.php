<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
        if (! $keyword) {
            return $query;
        }

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
        if (! $status) {
            return $query;
        }

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
        if (! $teknisi) {
            return $query;
        }

        return $query->where('nama_teknisi', 'like', "%{$teknisi}%");
    }

    /**
     * Scope: filter tanggal selesai / SA (YYYY-MM-DD)
     */
    public function scopeFilterTanggal(Builder $query, ?string $tanggal): Builder
    {
        return $tanggal ? $query->whereDate('tanggal_sa', $tanggal) : $query;
    }

    /**
     * Scope: filter vendor atau sektor (parsial)
     */
    public function scopeFilterVendor(Builder $query, ?string $vendor): Builder
    {
        if (! $vendor) {
            return $query;
        }

        return $query->where(function ($q) use ($vendor) {
            $q->where('vendor', 'like', "%{$vendor}%")
                ->orWhere('sektor', 'like', "%{$vendor}%");
        });
    }

    /**
     * Scope: filter hasil auto-match (sesuai / beda)
     */
    public function scopeFilterMatch(Builder $query, ?string $match): Builder
    {
        if ($match === 'sesuai') {
            return $query->where('cek_match', 'SESUAI');
        }

        if ($match === 'beda') {
            return $query->where(function ($q) {
                $q->whereNull('cek_match')->orWhere('cek_match', '!=', 'SESUAI');
            });
        }

        return $query;
    }

    /**
     * Penyelarasan Otomatis via Serial Number (Opsi 1):
     * Menyelaraskan nama teknisi di ont_keluars dengan nama resmi di reporting_wos
     * berdasarkan kesamaan serial_number agar tidak terjadi duplikasi (doubling) nama teknisi.
     */
    public static function syncTeknisiNamesWithOntKeluar(): int
    {
        $matches = OntKeluar::join('reporting_wos', 'ont_keluars.serial_number', '=', 'reporting_wos.serial_number')
            ->whereNotNull('reporting_wos.nama_teknisi')
            ->where('reporting_wos.nama_teknisi', '!=', '')
            ->whereNotNull('ont_keluars.nama_teknisi')
            ->where('ont_keluars.nama_teknisi', '!=', '')
            ->whereRaw('TRIM(LOWER(ont_keluars.nama_teknisi)) != TRIM(LOWER(reporting_wos.nama_teknisi))')
            ->select(
                'ont_keluars.id as ont_keluar_id',
                'ont_keluars.nama_teknisi as nama_keluar',
                'reporting_wos.nama_teknisi as nama_wo'
            )
            ->get();

        $synced = 0;
        foreach ($matches as $match) {
            $namaLama = $match->nama_keluar;
            $namaBaru = trim($match->nama_wo);

            OntKeluar::where('id', $match->ont_keluar_id)->update(['nama_teknisi' => $namaBaru]);
            if (! empty($namaLama)) {
                OntKeluar::where('nama_teknisi', $namaLama)->update(['nama_teknisi' => $namaBaru]);
            }
            $synced++;
        }

        return $synced;
    }
}
