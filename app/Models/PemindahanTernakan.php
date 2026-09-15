<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class PemindahanTernakan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tarikh_permohonan' => 'date',
        'tarikh_jangka_pindah' => 'date',
        'tarikh_fmd_p1' => 'date',
        'tarikh_fmd_p2' => 'date',
        'tarikh_fmd_booster' => 'date',
        'tarikh_lsd' => 'date',
        'lain_vaksin_tarikh' => 'date',
        'tarikh_rawatan_terakhir' => 'date',
        'tarikh_keluar_ladang' => 'datetime',
        'tarikh_sembelih' => 'date',
        'tarikh_kelulusan' => 'datetime',
        'senarai_tag' => 'array',
        'status_penyakit_ruminan_besar' => 'boolean',
        'status_penyakit_ruminan_kecil' => 'boolean',
    ];

    public function pemunya(): BelongsTo
    {
        return $this->belongsTo(Pemunya::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Dapatkan jajahan yang meluluskan permit (pegawai_jajahan atau fallback ke jajahan_asal)
     */
    public function getJajahanKelulusanAttribute(): string
    {
        return $this->pegawai_jajahan ?: ($this->jajahan_asal ?: 'Bachok');
    }

    /**
     * Map Jajahan to Jawi mengikut jajahan pegawai yang meluluskan
     */
    public function getJajahanJawiAttribute(): string
    {
        $jajahanJawiMap = [
            'Kota Bharu' => 'كوتا بهارو',
            'Pasir Mas' => 'ڤاسير مس',
            'Tumpat' => 'تومڤت',
            'Bachok' => 'باچوق',
            'Pasir Puteh' => 'ڤاسير ڤوتيه',
            'Machang' => 'ماچڠ',
            'Tanah Merah' => 'تانه ميره',
            'Jeli' => 'جيلي',
            'Kuala Krai' => 'كوالا كراي',
            'Gua Musang' => 'ڬوا موسڠ',
        ];

        return $jajahanJawiMap[$this->jajahan_kelulusan] ?? 'باچوق';
    }

    /**
     * Dapatkan maklumat rasmi Pejabat Perkhidmatan Veterinar Jajahan bagi Letterhead
     */
    public function getPejabatInfoAttribute(): array
    {
        $jajahan = $this->jajahan_kelulusan;
        $pejabatList = config('kelantan.pejabat', []);

        foreach ($pejabatList as $namaJajahan => $info) {
            if (strcasecmp($namaJajahan, $jajahan) === 0) {
                return $info;
            }
        }

        return [
            'jajahan' => $jajahan,
            'jawi' => $this->jajahan_jawi,
            'nama' => 'PEJABAT PERKHIDMATAN VETERINAR',
            'jajahan_title' => 'JAJAHAN ' . strtoupper($jajahan),
            'alamat_baris1' => 'JALAN PEJABAT VETERINAR, ' . strtoupper($jajahan),
            'alamat_baris2' => 'KELANTAN DARUL NAIM',
            'tel' => '09-7788242',
            'faks' => '09-7780675',
            'kod' => strtoupper(substr($jajahan, 0, 2)),
            'kod_rujukan' => 'JPV' . strtoupper(substr($jajahan, 0, 2)),
        ];
    }

    /**
     * Format jantina text for official forms
     */
    public function getFormatKuantitiJantinaAttribute(): string
    {
        $j = (int) $this->bilangan_jantan;
        $b = (int) $this->bilangan_betina;
        $spesies = strtoupper($this->jenis_ternakan ?? 'LEMBU');

        if ($j > 0 && $b > 0) {
            return "{$j} ekor {$spesies} Jantan dan {$b} ekor {$spesies} betina";
        } elseif ($j > 0) {
            return "{$j} ekor {$spesies} Jantan";
        } elseif ($b > 0) {
            return "{$b} ekor {$spesies} betina";
        }

        return "0 ekor {$spesies}";
    }

    /**
     * Format short jantina text (e.g. "2 BETINA" or "1 JANTAN, 2 BETINA")
     */
    public function getFormatRingkasJantinaAttribute(): string
    {
        $j = (int) $this->bilangan_jantan;
        $b = (int) $this->bilangan_betina;

        $parts = [];
        if ($j > 0) $parts[] = "{$j} JANTAN";
        if ($b > 0) $parts[] = "{$b} BETINA";

        return !empty($parts) ? implode(', ', $parts) : '-';
    }

    /**
     * Returns Collection of 50 slots for tags
     */
    public function getSenaraiTagListAttribute(): Collection
    {
        $raw = is_array($this->senarai_tag) ? $this->senarai_tag : [];
        $list = collect();

        for ($i = 1; $i <= 50; $i++) {
            $existing = $raw[$i - 1] ?? null;
            if (is_string($existing)) {
                $list->push(['bil' => $i, 'no_tag' => $existing]);
            } elseif (is_array($existing)) {
                $list->push([
                    'bil' => $i,
                    'no_tag' => $existing['no_tag'] ?? ($existing['tag'] ?? ''),
                    'jantina' => $existing['jantina'] ?? '',
                    'ternakan_id' => $existing['ternakan_id'] ?? null,
                ]);
            } else {
                $list->push(['bil' => $i, 'no_tag' => '']);
            }
        }

        return $list;
    }

    /**
     * Generate standard reference number
     */
    public static function generateNoRujukan(string $jajahan = 'Bachok'): string
    {
        $jajahanCodes = [
            'Kota Bharu' => 'JPVKB',
            'Pasir Mas' => 'JPVPM',
            'Tumpat' => 'JPVT',
            'Bachok' => 'JPVB',
            'Pasir Puteh' => 'JPVPP',
            'Machang' => 'JPVM',
            'Tanah Merah' => 'JPVTM',
            'Jeli' => 'JPVJ',
            'Kuala Krai' => 'JPVKK',
            'Gua Musang' => 'JPVGM',
        ];

        $code = $jajahanCodes[$jajahan] ?? 'JPVB';
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        $seq = str_pad($count, 2, '0', STR_PAD_LEFT);

        return "{$code} 600/8/13-{$year}/{$seq}";
    }
}
