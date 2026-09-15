@extends('layouts.app')

@section('title', 'Maklumat Tempahan Kenderaan - ' . ($tempahan->no_tempahan ?? $tempahan->no_permohonan ?? 'KND-'.$tempahan->id))
@section('page_title', 'Kenderaan: ' . ($tempahan->no_tempahan ?? $tempahan->no_permohonan ?? 'KND-'.$tempahan->id))

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{ modalKelulusan: false, modalTolak: false, modalSelesai: false }">

    <div class="flex items-center justify-between">
        <a href="{{ route('kenderaan.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl border border-slate-200 transition">
            &larr; Kembali ke Senarai Kenderaan
        </a>
        <div class="flex items-center gap-2">
            @if(Auth::user()->canManageKenderaanFleet())
                @if(in_array($tempahan->status, ['Menunggu', 'Menunggu Kelulusan']))
                    <button @click="modalKelulusan = true" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check"></i> Luluskan Permohonan
                    </button>
                    <button @click="modalTolak = true" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-xmark"></i> Tolak Permohonan
                    </button>
                @elseif($tempahan->status === 'Diluluskan')
                    <button @click="modalSelesai = true" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-flag-checkered"></i> Catat Odometer & Selesaikan Trip
                    </button>
                @endif
            @endif
        </div>
    </div>

    <!-- Booking Overview Card -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-6 text-xs">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <span class="font-mono text-base font-black text-teal-900">{{ $tempahan->no_tempahan ?? $tempahan->no_permohonan ?? 'KND-'.$tempahan->id }}</span>
                <div class="text-[11px] text-slate-500 mt-0.5">Destinasi: <b class="text-slate-800">{{ $tempahan->destinasi }}</b></div>
            </div>
            <span class="px-3.5 py-1 rounded-full text-xs font-bold {{ $tempahan->status === 'Diluluskan' ? 'bg-emerald-100 text-emerald-800' : ($tempahan->status === 'Selesai' ? 'bg-blue-100 text-blue-800' : ($tempahan->status === 'Ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800')) }}">
                {{ $tempahan->status }}
            </span>
        </div>

        <!-- Approval / Rejection Banner -->
        @if($tempahan->status === 'Diluluskan' || $tempahan->status === 'Selesai')
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 space-y-1">
                <div class="flex items-center gap-2 font-bold text-xs text-emerald-900">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                    <span>Permohonan Tempahan Telah Diluluskan</span>
                </div>
                <div class="text-[11px] text-emerald-800 grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                    <div>Pegawai Pelulus: <b>{{ $tempahan->pelulus->name ?? 'Pegawai Pentadbiran' }}</b> ({{ $tempahan->pelulus->role_label ?? 'Admin Pejabat' }})</div>
                    <div>Tarikh Kelulusan: <b>{{ $tempahan->updated_at->format('d/m/Y H:i') }}</b></div>
                </div>
                @if($tempahan->catatan_kelulusan)
                    <div class="text-[11px] text-emerald-800 pt-1 border-t border-emerald-200/60 mt-1">
                        Catatan Kelulusan: <i>"{{ $tempahan->catatan_kelulusan }}"</i>
                    </div>
                @endif
            </div>
        @elseif($tempahan->status === 'Ditolak')
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-950 space-y-1">
                <div class="flex items-center gap-2 font-bold text-xs text-rose-900">
                    <i class="fa-solid fa-circle-xmark text-rose-600 text-sm"></i>
                    <span>Permohonan Tempahan Ditolak</span>
                </div>
                <div class="text-[11px] text-rose-800 grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                    <div>Pegawai Menolak: <b>{{ $tempahan->pelulus->name ?? 'Pegawai Pentadbiran' }}</b> ({{ $tempahan->pelulus->role_label ?? 'Admin Pejabat' }})</div>
                    <div>Tarikh Keputusan: <b>{{ $tempahan->updated_at->format('d/m/Y H:i') }}</b></div>
                </div>
                @if($tempahan->catatan_kelulusan)
                    <div class="text-[11px] text-rose-800 pt-1 border-t border-rose-200/60 mt-1 font-semibold">
                        Sebab Penolakan: <i>"{{ $tempahan->catatan_kelulusan }}"</i>
                    </div>
                @endif
            </div>
        @else
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-950 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-clock text-amber-600 text-base shrink-0"></i>
                    <div>
                        <div class="font-bold text-xs text-amber-900">Menunggu Kelulusan Pegawai</div>
                        <div class="text-[11px] text-amber-800">Permohonan ini sedang menunggu semakan dan pengesahan dari Admin Pejabat / Pegawai Bertugas.</div>
                    </div>
                </div>
                @if(Auth::user()->canManageKenderaanFleet())
                    <div class="flex items-center gap-2 shrink-0">
                        <button @click="modalKelulusan = true" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1">
                            <i class="fa-solid fa-circle-check"></i> Luluskan
                        </button>
                        <button @click="modalTolak = true" class="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1">
                            <i class="fa-solid fa-circle-xmark"></i> Tolak
                        </button>
                    </div>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">MAKLUMAT PEMOHON</span>
                <div>Nama: <b>{{ $tempahan->pemohon->name ?? '-' }}</b></div>
                <div>Jawatan: <b>{{ $tempahan->pemohon->role_label ?? '-' }}</b></div>
                <div>No Tel: <b>{{ $tempahan->pemohon->phone ?? '-' }}</b></div>
                <div>Bil. Penumpang: <b>{{ $tempahan->bilangan_penumpang }} Orang</b></div>
                @if($tempahan->senarai_nama_penumpang)
                    <div class="text-[11px] text-slate-600">Penumpang Lain: {{ $tempahan->senarai_nama_penumpang }}</div>
                @endif
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">KENDERAAN & JADUAL</span>
                <div>Kenderaan: <b class="text-teal-900">{{ $tempahan->kenderaan->no_pendaftaran ?? 'Belum Ditugaskan' }}</b> ({{ $tempahan->kenderaan->model ?? '-' }})</div>
                <div>Pemandu: <b>{{ $tempahan->pemandu_nama ?? $tempahan->nama_pemandu ?? 'Pemandu Ditugaskan' }}</b></div>
                <div>Bertolak: <b>{{ $tempahan->tarikh_mula ? $tempahan->tarikh_mula->format('d/m/Y') : '-' }} {{ $tempahan->masa_mula ? '('.$tempahan->masa_mula.')' : '' }}</b></div>
                <div>Kembali: <b>{{ $tempahan->tarikh_tamat ? $tempahan->tarikh_tamat->format('d/m/Y') : '-' }} {{ $tempahan->masa_tamat ? '('.$tempahan->masa_tamat.')' : '' }}</b></div>
            </div>
        </div>

        <div>
            <span class="font-bold text-slate-700 uppercase block mb-1">Tujuan Rasmi Penggunaan:</span>
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-slate-700 leading-relaxed">
                {{ $tempahan->tujuan_perjalanan ?? $tempahan->tujuan ?? 'Urusan Rasmi Jabatan' }}
            </div>
        </div>

        <!-- Trip Log & Odometer if completed -->
        @if($tempahan->status === 'Selesai')
            <div class="pt-4 border-t border-slate-100 space-y-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">LOG LOGISTIK & ODOMETER</span>
                <div class="grid grid-cols-3 gap-3 bg-blue-50/50 p-4 rounded-2xl border border-blue-200">
                    <div>
                        <span class="text-slate-500 text-[11px] block">Odometer Awal:</span>
                        <span class="font-bold font-mono">{{ number_format($tempahan->odometer_keluar ?? $tempahan->odometer_mula ?? 0) }} km</span>
                    </div>
                    <div>
                        <span class="text-slate-500 text-[11px] block">Odometer Akhir:</span>
                        <span class="font-bold font-mono">{{ number_format($tempahan->odometer_masuk ?? $tempahan->odometer_tamat ?? 0) }} km</span>
                    </div>
                    <div>
                        <span class="text-slate-500 text-[11px] block">Jumlah Jarak & Belanja Minyak:</span>
                        <span class="font-black text-emerald-800">{{ number_format(($tempahan->odometer_masuk ?? $tempahan->odometer_tamat ?? 0) - ($tempahan->odometer_keluar ?? $tempahan->odometer_mula ?? 0)) }} km (RM {{ number_format($tempahan->kos_minyak ?? 0, 2) }})</span>
                    </div>
                </div>
            </div>
        @endif

    </div>

    @if(Auth::user()->canManageKenderaanFleet())
    <!-- MODAL: Kelulusan Kenderaan -->
    <div x-show="modalKelulusan" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl text-xs space-y-4" @click.outside="modalKelulusan = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Kelulusan Permohonan Kenderaan</h3>
                        <p class="text-[11px] text-slate-500 font-mono">{{ $tempahan->no_tempahan ?? $tempahan->no_permohonan }}</p>
                    </div>
                </div>
                <button @click="modalKelulusan = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form action="{{ route('kenderaan.lulus', $tempahan->id) }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="status" value="Diluluskan">

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Kenderaan Yang Ditugaskan <span class="text-rose-500">*</span></label>
                    <select name="kenderaan_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">-- Pilih Kenderaan Tersedia --</option>
                        @foreach(($availableFleet ?? $availableKenderaan ?? $fleet ?? []) as $fleetItem)
                            <option value="{{ $fleetItem->id }}" {{ $tempahan->kenderaan_id == $fleetItem->id ? 'selected' : '' }}>
                                {{ $fleetItem->no_pendaftaran }} - {{ $fleetItem->model }} ({{ $fleetItem->jenis_kenderaan ?? $fleetItem->jenis ?? 'Kenderaan' }}) &bull; {{ $fleetItem->status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Pemandu Ditugaskan <span class="text-rose-500">*</span></label>
                    <select name="pemandu_nama" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="Pemandu Khas Jabatan">-- Pemandu Khas Jabatan --</option>
                        @foreach(($pemanduList ?? []) as $d)
                            <option value="{{ $d->nama }} ({{ $d->no_telefon }}) - Lesen {{ $d->kelas_lesen }}" {{ ($tempahan->pemandu_nama ?? '') === $d->nama ? 'selected' : '' }}>
                                {{ $d->nama }} &bull; {{ $d->no_telefon }} (Lesen: {{ $d->kelas_lesen }}) - {{ $d->status }}
                            </option>
                        @endforeach
                        <option value="Pemandu Sendiri (Pemohon)">Pemandu Sendiri (Pemohon)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Kelulusan / Syarat Penggunaan</label>
                    <textarea name="catatan_kelulusan" rows="2" placeholder="Catatan pegawai penguasa..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none font-medium">Permohonan tempahan diluluskan untuk urusan rasmi jabatan.</textarea>
                </div>

                <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-200 text-emerald-900 text-[11px] flex items-center gap-2">
                    <i class="fa-solid fa-user-shield text-base text-emerald-600"></i>
                    <div>Pegawai Pelulus: <b>{{ Auth::user()->name }}</b> ({{ Auth::user()->role_label }})</div>
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" @click="modalKelulusan = false" class="px-4 py-2 rounded-xl bg-slate-100 font-bold text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-check-double"></i> Sahkan Kelulusan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Penolakan Permohonan Kenderaan -->
    <div x-show="modalTolak" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl text-xs space-y-4" @click.outside="modalTolak = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Penolakan Permohonan Kenderaan</h3>
                        <p class="text-[11px] text-slate-500 font-mono">{{ $tempahan->no_tempahan ?? $tempahan->no_permohonan }}</p>
                    </div>
                </div>
                <button @click="modalTolak = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form action="{{ route('kenderaan.tolak', $tempahan->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Sebab / Alasan Penolakan <span class="text-rose-500">*</span></label>
                    <textarea name="sebab_tolak" rows="3" required placeholder="Nyatakan sebab permohonan tidak diluluskan (cth: Tiada kenderaan sedia pada tarikh tersebut / Bertindih operasi lain)..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:outline-none font-medium"></textarea>
                </div>

                <div class="p-3 bg-rose-50 rounded-2xl border border-rose-200 text-rose-900 text-[11px] flex items-center gap-2">
                    <i class="fa-solid fa-user-xmark text-base text-rose-600"></i>
                    <div>Pegawai Menolak: <b>{{ Auth::user()->name }}</b> ({{ Auth::user()->role_label }})</div>
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" @click="modalTolak = false" class="px-4 py-2 rounded-xl bg-slate-100 font-bold text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-ban"></i> Sahkan Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Selesaikan Trip & Odometer -->
    <div x-show="modalSelesai" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl text-xs space-y-4" @click.outside="modalSelesai = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900">Catat Log Tamat Trip & Odometer</h3>
                <button @click="modalSelesai = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form action="{{ route('kenderaan.selesai', $tempahan->id) }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Odometer Awal (km)</label>
                        <input type="number" name="odometer_mula" value="{{ $tempahan->odometer_keluar ?? $tempahan->kenderaan->odometer_semasa_km ?? 45000 }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Odometer Tamat (km)</label>
                        <input type="number" name="odometer_tamat" value="{{ ($tempahan->odometer_keluar ?? $tempahan->kenderaan->odometer_semasa_km ?? 45000) + 120 }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Kos Minyak / Touch 'n Go (RM)</label>
                    <input type="number" step="0.01" name="kos_minyak" value="50.00" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Laporan Keadaan Kenderaan</label>
                    <textarea name="laporan_keadaan_kenderaan" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">Kenderaan dipulangkan dalam keadaan bersih, minyak mencukupi dan tiada sebarang kerosakan fizikal.</textarea>
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" @click="modalSelesai = false" class="px-4 py-2 rounded-xl bg-slate-100 font-bold text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-md transition">Selesaikan Trip</button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection
