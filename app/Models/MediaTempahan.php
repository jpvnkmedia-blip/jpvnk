<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MediaTempahan extends Model
{
    use HasFactory;

    protected $table = 'media_tempahan';

    protected $fillable = [
        'no_rujukan',
        'user_id',
        'nama_pemohon',
        'jawatan',
        'jawatan_pemohon',
        'bahagian_unit_jajahan',
        'bahagian_unit',
        'no_telefon',
        'emel',
        'nama_program',
        'tarikh_program',
        'tarikh_tamat',
        'masa_mula',
        'masa_tamat',
        'lokasi',
        'penganjur',
        'pegawai_bertanggungjawab',
        'anggaran_peserta',
        'jenis_permohonan',
        'butiran_fotografi',
        'keperluan_fotografi',
        'butiran_poster',
        'keperluan_poster',
        'butiran_video',
        'keperluan_video',
        'butiran_lain',
        'catatan_keperluan',
        'tarikh_diperlukan',
        'tahap_keutamaan',
        'keutamaan',
        'sebab_segera',
        'lampiran',
        'pengesahan_pemohon',
        'perakuan',
        'tarikh_hantar',
        'status',
        'catatan_unit_media',
        'catatan_admin',
        'pegawai_media_bertugas',
        'peralatan_disediakan',
        'pautan_hasil_media',
        'diluluskan_oleh',
        'tarikh_kelulusan',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_program' => 'date',
            'tarikh_tamat' => 'date',
            'tarikh_diperlukan' => 'date',
            'tarikh_hantar' => 'datetime',
            'tarikh_kelulusan' => 'datetime',
            'jenis_permohonan' => 'array',
            'butiran_fotografi' => 'array',
            'butiran_poster' => 'array',
            'butiran_video' => 'array',
            'lampiran' => 'array',
            'pengesahan_pemohon' => 'boolean',
        ];
    }

    public function pemohon()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pelulus()
    {
        return $this->belongsTo(User::class, 'diluluskan_oleh');
    }

    public function pegawaiBertugas()
    {
        return $this->belongsTo(User::class, 'diluluskan_oleh');
    }

    // Accessors & Mutators for compatibility
    public function getJawatanPemohonAttribute()
    {
        return $this->attributes['jawatan'] ?? null;
    }

    public function setJawatanPemohonAttribute($value)
    {
        $this->attributes['jawatan'] = $value;
    }

    public function getBahagianUnitAttribute()
    {
        return $this->attributes['bahagian_unit_jajahan'] ?? null;
    }

    public function setBahagianUnitAttribute($value)
    {
        $this->attributes['bahagian_unit_jajahan'] = $value;
    }

    public function getKeutamaanAttribute()
    {
        return $this->attributes['tahap_keutamaan'] ?? 'Biasa';
    }

    public function setKeutamaanAttribute($value)
    {
        $this->attributes['tahap_keutamaan'] = $value;
    }

    public function getCatatanAdminAttribute()
    {
        return $this->attributes['catatan_unit_media'] ?? null;
    }

    public function setCatatanAdminAttribute($value)
    {
        $this->attributes['catatan_unit_media'] = $value;
    }

    public function getCatatanKeperluanAttribute()
    {
        return $this->attributes['butiran_lain'] ?? null;
    }

    public function setCatatanKeperluanAttribute($value)
    {
        $this->attributes['butiran_lain'] = $value;
    }

    public function getKeperluanFotografiAttribute()
    {
        return $this->butiran_fotografi;
    }

    public function setKeperluanFotografiAttribute($value)
    {
        $this->attributes['butiran_fotografi'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getKeperluanPosterAttribute()
    {
        return $this->butiran_poster;
    }

    public function setKeperluanPosterAttribute($value)
    {
        $this->attributes['butiran_poster'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getKeperluanVideoAttribute()
    {
        return $this->butiran_video;
    }

    public function setKeperluanVideoAttribute($value)
    {
        $this->attributes['butiran_video'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setPerakuanAttribute($value)
    {
        $this->attributes['pengesahan_pemohon'] = (bool)$value;
    }

    public static function generateNoRujukan(): string
    {
        $year = date('Y');
        $month = date('m');
        $count = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1;

        return sprintf('MEDIA/%s/%s/%04d', $year, $month, $count);
    }

    // Helper untuk warna status
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'Diluluskan' => [
                'bg' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
                'dot' => 'bg-emerald-500',
                'label' => 'Diluluskan',
                'icon' => 'fa-circle-check',
            ],
            'Menunggu Kelulusan' => [
                'bg' => 'bg-amber-50 text-amber-800 border-amber-300',
                'dot' => 'bg-amber-500',
                'label' => 'Menunggu Kelulusan',
                'icon' => 'fa-clock',
            ],
            'Perlu Pembetulan' => [
                'bg' => 'bg-sky-50 text-sky-800 border-sky-300',
                'dot' => 'bg-sky-500',
                'label' => 'Perlu Pembetulan',
                'icon' => 'fa-circle-exclamation',
            ],
            'Ditolak' => [
                'bg' => 'bg-rose-50 text-rose-800 border-rose-300',
                'dot' => 'bg-rose-500',
                'label' => 'Ditolak',
                'icon' => 'fa-circle-xmark',
            ],
            'Selesai' => [
                'bg' => 'bg-purple-50 text-purple-800 border-purple-300',
                'dot' => 'bg-purple-500',
                'label' => 'Selesai',
                'icon' => 'fa-flag-checkered',
            ],
            'Dibatalkan' => [
                'bg' => 'bg-slate-100 text-slate-700 border-slate-300',
                'dot' => 'bg-slate-400',
                'label' => 'Dibatalkan',
                'icon' => 'fa-ban',
            ],
            default => [
                'bg' => 'bg-slate-50 text-slate-700 border-slate-300',
                'dot' => 'bg-slate-400',
                'label' => $this->status,
                'icon' => 'fa-circle',
            ],
        };
    }

    // Helper untuk tahap keutamaan badge
    public function getKeutamaanBadgeAttribute(): array
    {
        return match ($this->tahap_keutamaan) {
            'Sangat Segera' => [
                'bg' => 'bg-rose-100 text-rose-800 border-rose-300',
                'icon' => 'fa-triangle-exclamation',
                'label' => 'Sangat Segera',
            ],
            'Segera' => [
                'bg' => 'bg-amber-100 text-amber-800 border-amber-300',
                'icon' => 'fa-bolt',
                'label' => 'Segera',
            ],
            default => [
                'bg' => 'bg-slate-100 text-slate-700 border-slate-300',
                'icon' => 'fa-calendar-check',
                'label' => 'Biasa',
            ],
        };
    }

    /**
     * Dapatkan status ketersediaan tarikh:
     * 🟢 Kosong: 0 tempahan aktif
     * 🟡 Sebahagian Ditempah: 1-2 tempahan aktif
     * 🔴 Penuh: >= 3 tempahan aktif
     */
    public static function getSlotStatusForDate(string $dateString): array
    {
        $activeBookings = static::whereDate('tarikh_program', $dateString)
            ->whereIn('status', ['Menunggu Kelulusan', 'Diluluskan', 'Perlu Pembetulan'])
            ->get();

        $count = $activeBookings->count();

        if ($count === 0) {
            return [
                'status' => 'kosong',
                'color' => 'emerald',
                'bg' => 'bg-emerald-500',
                'text' => 'text-emerald-700',
                'badge' => 'Tarikh Kosong',
                'icon' => 'fa-circle',
                'count' => 0,
                'is_full' => false,
                'bookings' => $activeBookings,
            ];
        } elseif ($count <= 2) {
            return [
                'status' => 'sebahagian',
                'color' => 'amber',
                'bg' => 'bg-amber-500',
                'text' => 'text-amber-700',
                'badge' => "Sebahagian Ditempah ($count Slot)",
                'icon' => 'fa-circle',
                'count' => $count,
                'is_full' => false,
                'bookings' => $activeBookings,
            ];
        } else {
            return [
                'status' => 'penuh',
                'color' => 'rose',
                'bg' => 'bg-rose-500',
                'text' => 'text-rose-700',
                'badge' => "Tarikh Penuh ($count Slot)",
                'icon' => 'fa-circle',
                'count' => $count,
                'is_full' => true,
                'bookings' => $activeBookings,
            ];
        }
    }

    /**
     * Format tarikh dan masa program mengikut standard Google Calendar (UTC ISO8601)
     */
    public function getGoogleCalendarDates(): string
    {
        $startDate = $this->tarikh_program ? $this->tarikh_program->format('Y-m-d') : date('Y-m-d');
        
        $rawStartTime = trim($this->masa_mula ?? '08:30');
        $startTime = preg_replace('/[^0-9:]/', '', $rawStartTime);
        if (strlen($startTime) == 4 && !str_contains($startTime, ':')) {
            $startTime = substr($startTime, 0, 2) . ':' . substr($startTime, 2, 2);
        }
        if (empty($startTime) || !str_contains($startTime, ':')) {
            $startTime = '08:30';
        }

        $endDate = ($this->tarikh_tamat ?: $this->tarikh_program) ? ($this->tarikh_tamat ?: $this->tarikh_program)->format('Y-m-d') : $startDate;
        
        $rawEndTime = trim($this->masa_tamat ?? '17:00');
        $endTime = preg_replace('/[^0-9:]/', '', $rawEndTime);
        if (strlen($endTime) == 4 && !str_contains($endTime, ':')) {
            $endTime = substr($endTime, 0, 2) . ':' . substr($endTime, 2, 2);
        }
        if (empty($endTime) || !str_contains($endTime, ':')) {
            $endTime = '17:00';
        }

        try {
            $start = Carbon::parse("{$startDate} {$startTime}", 'Asia/Kuala_Lumpur')->setTimezone('UTC');
            $end = Carbon::parse("{$endDate} {$endTime}", 'Asia/Kuala_Lumpur')->setTimezone('UTC');
            return $start->format('Ymd\THis\Z') . '/' . $end->format('Ymd\THis\Z');
        } catch (\Exception $e) {
            return str_replace('-', '', $startDate) . '/' . str_replace('-', '', $endDate);
        }
    }

    /**
     * Jana pautan terus Google Calendar untuk dimasukkan ke akaun jpvnkmedia@gmail.com
     */
    public function getGoogleCalendarUrlAttribute(): string
    {
        $title = "[JPVNK MEDIA] {$this->nama_program} ({$this->no_rujukan})";
        $dates = $this->getGoogleCalendarDates();
        $jenisStr = is_array($this->jenis_permohonan) ? implode(', ', $this->jenis_permohonan) : ($this->jenis_permohonan ?? 'Liputan Media');

        $details = "TEMPAHAN PERKHIDMATAN UNIT MEDIA & PENERBITAN JPVNK\n\n"
            . "📌 No. Rujukan: {$this->no_rujukan}\n"
            . "🎯 Nama Program: {$this->nama_program}\n"
            . "🏛️ Penganjur: {$this->penganjur}\n"
            . "📍 Lokasi: {$this->lokasi}\n"
            . "👤 Pemohon: {$this->nama_pemohon} ({$this->jawatan})\n"
            . "🏢 Bahagian / Unit: {$this->bahagian_unit_jajahan}\n"
            . "📞 No. Telefon: {$this->no_telefon}\n"
            . "✉️ Emel: {$this->emel}\n"
            . "🎥 Jenis Liputan: {$jenisStr}\n"
            . "🎬 Pegawai / Krew Bertugas: " . ($this->pegawai_media_bertugas ?: 'Unit Media JPVNK') . "\n"
            . "📷 Peralatan: " . ($this->peralatan_disediakan ?: 'Kamera DSLR, Gimbal & Mikrofon') . "\n"
            . "🔗 Status: " . $this->status . "\n\n"
            . "Sistem Bersepadu JPVNK: " . (function_exists('route') ? route('media.show', $this->id) : url('/media/' . $this->id));

        $params = [
            'action' => 'TEMPLATE',
            'text' => $title,
            'dates' => $dates,
            'details' => $details,
            'location' => $this->lokasi ?? 'Jabatan Perkhidmatan Veterinar Negeri Kelantan',
            'add' => 'jpvnkmedia@gmail.com',
        ];

        return 'https://calendar.google.com/calendar/render?' . http_build_query($params);
    }

    /**
     * Jana kandungan iCalendar (.ics) untuk kalendar Google / Outlook / Apple
     */
    public function generateIcsContent(): string
    {
        $dates = $this->getGoogleCalendarDates();
        $parts = explode('/', $dates);
        $dtStart = $parts[0] ?? gmdate('Ymd\THis\Z');
        $dtEnd = $parts[1] ?? gmdate('Ymd\THis\Z', strtotime('+2 hours'));
        $uid = md5($this->no_rujukan . $this->id) . '@veterinar.kelantan.gov.my';
        $summary = addcslashes("[JPVNK MEDIA] {$this->nama_program} ({$this->no_rujukan})", ",;\\");
        $location = addcslashes($this->lokasi ?? 'JPVNK Kelantan', ",;\\");
        $jenisStr = is_array($this->jenis_permohonan) ? implode(', ', $this->jenis_permohonan) : ($this->jenis_permohonan ?? '');
        $description = addcslashes("Program: {$this->nama_program}\\nNo Rujukan: {$this->no_rujukan}\\nPemohon: {$this->nama_pemohon} ({$this->no_telefon})\\nLiputan: {$jenisStr}\\nPegawai Bertugas: {$this->pegawai_media_bertugas}\\nStatus: {$this->status}", ",;\\");

        return "BEGIN:VCALENDAR\r\n"
            . "VERSION:2.0\r\n"
            . "PRODID:-//JPVNK//Sistem Veterinar Bersepadu Kelantan//MY\r\n"
            . "CALSCALE:GREGORIAN\r\n"
            . "METHOD:REQUEST\r\n"
            . "BEGIN:VEVENT\r\n"
            . "UID:{$uid}\r\n"
            . "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n"
            . "DTSTART:{$dtStart}\r\n"
            . "DTEND:{$dtEnd}\r\n"
            . "SUMMARY:{$summary}\r\n"
            . "DESCRIPTION:{$description}\r\n"
            . "LOCATION:{$location}\r\n"
            . "ORGANIZER;CN=Unit Media JPVNK:mailto:jpvnkmedia@gmail.com\r\n"
            . "ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=ACCEPTED;CN=JPVNK Media:mailto:jpvnkmedia@gmail.com\r\n"
            . "STATUS:CONFIRMED\r\n"
            . "END:VEVENT\r\n"
            . "END:VCALENDAR\r\n";
    }
}
