@extends('layouts.app')

@section('title', 'Semakan Permohonan ' . $application->no_rujukan)
@section('page-title', 'Perincian & Semakan Permohonan')

@push('styles')
<!-- Leaflet Map CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #previewMap { height: 220px; width: 100%; border-radius: 0.75rem; z-index: 10; }
</style>
@endpush

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    <!-- Top Action & Navigation Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div class="flex items-center space-x-4">
            <a href="{{ route('naimbif.admin.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center space-x-3">
                    <span class="text-xl font-black text-slate-900 font-mono">{{ $application->no_rujukan }}</span>
                    {!! $application->status_badge !!}
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Dihantar pada: {{ $application->created_at->format('d/m/Y, h:i A') }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('naimbif.public.print', $application->no_rujukan) }}" target="_blank"
               class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-800 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition-colors flex items-center">
                <i class="fas fa-print mr-2 text-slate-600"></i> Cetak Borang A4 Rasmi
            </a>
            
            <form action="{{ route('naimbif.admin.destroy', $application->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti ingin memadam rekod permohonan ini? Tindakan ini tidak boleh diundur.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2.5 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Grid: Left (Form Details) | Right (Officer Action Boxes) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- ============================================ -->
        <!-- LEFT COLUMN: APPLICATION FORM DATA           -->
        <!-- ============================================ -->
        <div class="lg:col-span-7 space-y-6">

            <!-- 1. Maklumat Peserta -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xs">1</span>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Maklumat Peserta</h3>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="col-span-2">
                        <span class="text-slate-400 font-semibold block uppercase text-[10px]">Nama Penuh:</span>
                        <div class="text-sm font-bold text-slate-900 mt-0.5">{{ $application->nama }}</div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block uppercase text-[10px]">No. Kad Pengenalan:</span>
                        <div class="font-bold text-slate-800 font-mono mt-0.5">{{ $application->formatted_no_kp }}</div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block uppercase text-[10px]">No. Telefon:</span>
                        <div class="font-bold text-slate-800 mt-0.5">{{ $application->no_telefon }}</div>
                    </div>
                    <div class="col-span-2">
                        <span class="text-slate-400 font-semibold block uppercase text-[10px]">Alamat Tetap:</span>
                        <div class="text-slate-800 mt-0.5 leading-relaxed">{{ $application->alamat_tetap }}, {{ $application->poskod }} {{ $application->jajahan }}</div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block uppercase text-[10px]">Pengalaman Menternak:</span>
                        <div class="font-bold text-slate-800 mt-0.5">{{ $application->pengalaman_menternak }} Tahun</div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block uppercase text-[10px]">Status Penternakan:</span>
                        <div class="font-bold text-slate-800 mt-0.5">{{ $application->status_penternakan }}</div>
                    </div>
                    <div class="col-span-2 p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="text-slate-500 font-semibold block uppercase text-[10px]">Rekod Kursus / Latihan:</span>
                        @if($application->pernah_kursus)
                            <div class="text-slate-800 mt-1">
                                <span class="font-bold text-emerald-700">Pernah Mengikuti:</span> {{ $application->nama_kursus }} <br>
                                <span class="text-slate-500 text-[11px]">Anjuran: {{ $application->anjuran_kursus }}</span>
                            </div>
                        @else
                            <div class="text-slate-600 mt-1">
                                Belum pernah mengikuti kursus. Minat kursus JPVNK:
                                <span class="font-bold {{ $application->berminat_kursus_jpvnk ? 'text-emerald-700' : 'text-slate-500' }}">
                                    {{ $application->berminat_kursus_jpvnk ? 'YA, BERMINAT' : 'TIDAK' }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 2. Maklumat Asas Ladang -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xs">2</span>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Maklumat Asas Ladang</h3>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="col-span-2">
                        <span class="text-slate-400 font-semibold block uppercase text-[10px]">Alamat Tapak Ladang:</span>
                        <div class="text-slate-800 mt-0.5 leading-relaxed">
                            {{ $application->alamat_ladang ?: $application->alamat_tetap }}, {{ $application->poskod_ladang ?: $application->poskod }} {{ $application->jajahan_ladang ?: $application->jajahan }}
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block uppercase text-[10px]">Status Tanah:</span>
                        <div class="font-bold text-slate-800 mt-0.5">
                            {{ $application->status_tanah }}
                            @if($application->status_tanah === 'Lain-lain')
                                ({{ $application->status_tanah_lain }})
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block uppercase text-[10px]">Keluasan Tanah:</span>
                        <div class="font-bold text-slate-800 mt-0.5">{{ $application->keluasan_tanah }} Ekar</div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block uppercase text-[10px]">Padang Ragut:</span>
                        <div class="font-bold {{ $application->padang_ragut === 'Ada' ? 'text-emerald-700' : 'text-slate-600' }} mt-0.5">
                            {{ $application->padang_ragut }}
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block uppercase text-[10px]">Bilangan Pekerja:</span>
                        <div class="font-bold text-slate-800 mt-0.5">{{ $application->bilangan_pekerja }} Orang</div>
                    </div>
                </div>

                <!-- GPS Map Preview -->
                <div class="pt-2 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-slate-500 font-semibold text-[11px] uppercase">
                            <i class="fas fa-map-marker-alt text-emerald-600 mr-1"></i> Koordinat GPS:
                            <span class="font-mono font-bold text-slate-800">{{ $application->gps_latitud ?? '-' }}, {{ $application->gps_longitud ?? '-' }}</span>
                        </span>
                    </div>
                    <div id="previewMap"></div>
                </div>
            </div>

            <!-- 3. Maklumat Asas Ternakan & Matriks Stok -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xs">3</span>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Maklumat Asas & Stok Ternakan</h3>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs mb-4">
                    <div>
                        <span class="text-slate-400 font-semibold block uppercase text-[10px]">Punca Ternakan:</span>
                        <div class="font-bold text-slate-800 mt-0.5">
                            {{ $application->punca_ternakan }}
                            @if($application->punca_ternakan === 'Lain-lain')
                                ({{ $application->punca_ternakan_lain }})
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block uppercase text-[10px]">Kaedah Pembiakan:</span>
                        <div class="font-bold text-slate-800 mt-0.5">{{ $application->kaedah_pembiakan }}</div>
                    </div>
                </div>

                <!-- Matrix Table -->
                <div class="overflow-x-auto border border-slate-200 rounded-2xl">
                    <table class="min-w-full text-xs text-center border-collapse">
                        <thead>
                            <tr class="bg-slate-800 text-white font-bold">
                                <th rowspan="2" class="p-2.5 text-left border-r border-slate-700">BAKA TERNAKAN</th>
                                <th colspan="3" class="p-1.5 border-r border-slate-700 bg-emerald-900 text-emerald-200">BETINA</th>
                                <th colspan="2" class="p-1.5 border-r border-slate-700 bg-blue-900 text-blue-200">JANTAN</th>
                                <th rowspan="2" class="p-2.5 bg-slate-900">JUMLAH</th>
                            </tr>
                            <tr class="bg-slate-700 text-slate-200 font-semibold text-[10px]">
                                <th class="p-1 border-r border-slate-600">ANAK</th>
                                <th class="p-1 border-r border-slate-600">DARA</th>
                                <th class="p-1 border-r border-slate-600">INDUK</th>
                                <th class="p-1 border-r border-slate-600">ANAK</th>
                                <th class="p-1 border-r border-slate-600">PEJANTAN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @php
                                $standardBreeds = ['CHAROLAIS', 'BELGIAN BLUE', "BLONDE D'AQUITAINE", 'LIMOUSIN', 'KEDAH KELANTAN', 'LAIN-LAIN'];
                                $totBAnak = 0; $totBDara = 0; $totBInduk = 0; $totJAnak = 0; $totJPej = 0; $grand = 0;
                            @endphp

                            @foreach($standardBreeds as $b)
                                @php
                                    $inv = $inventories[$b] ?? null;
                                    $ba = $inv ? $inv->betina_anak : 0;
                                    $bd = $inv ? $inv->betina_dara : 0;
                                    $bi = $inv ? $inv->betina_induk : 0;
                                    $ja = $inv ? $inv->jantan_anak : 0;
                                    $jp = $inv ? $inv->jantan_pejantan : 0;
                                    $rowTot = $ba + $bd + $bi + $ja + $jp;

                                    $totBAnak += $ba; $totBDara += $bd; $totBInduk += $bi;
                                    $totJAnak += $ja; $totJPej += $jp; $grand += $rowTot;
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="p-2 text-left font-bold text-slate-800 border-r border-slate-200">
                                        {{ $b }}
                                        @if($b === 'LAIN-LAIN' && $inv && $inv->nama_baka_lain)
                                            <span class="block text-[10px] text-slate-500 font-normal">({{ $inv->nama_baka_lain }})</span>
                                        @endif
                                    </td>
                                    <td class="p-2 border-r border-slate-200 bg-emerald-50/20">{{ $ba ?: '-' }}</td>
                                    <td class="p-2 border-r border-slate-200 bg-emerald-50/20">{{ $bd ?: '-' }}</td>
                                    <td class="p-2 border-r border-slate-200 bg-emerald-50/20">{{ $bi ?: '-' }}</td>
                                    <td class="p-2 border-r border-slate-200 bg-blue-50/20">{{ $ja ?: '-' }}</td>
                                    <td class="p-2 border-r border-slate-200 bg-blue-50/20">{{ $jp ?: '-' }}</td>
                                    <td class="p-2 font-bold text-slate-900 bg-slate-50 font-mono">{{ $rowTot ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-900 text-white font-extrabold text-xs">
                                <td class="p-2.5 text-left border-r border-slate-800">JUMLAH</td>
                                <td class="p-2 border-r border-slate-800 text-emerald-300 font-mono">{{ $totBAnak }}</td>
                                <td class="p-2 border-r border-slate-800 text-emerald-300 font-mono">{{ $totBDara }}</td>
                                <td class="p-2 border-r border-slate-800 text-emerald-300 font-mono">{{ $totBInduk }}</td>
                                <td class="p-2 border-r border-slate-800 text-blue-300 font-mono">{{ $totJAnak }}</td>
                                <td class="p-2 border-r border-slate-800 text-blue-300 font-mono">{{ $totJPej }}</td>
                                <td class="p-2.5 bg-amber-500 text-slate-950 font-black font-mono text-sm">{{ $grand }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- 4. Pengakuan & Tandatangan -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xs">4</span>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Pengakuan & Tandatangan</h3>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <div>
                        <div class="text-emerald-700 font-bold"><i class="fas fa-check-circle mr-1"></i> Perakuan Sahih Diberikan</div>
                        <div class="text-slate-400 text-[11px] mt-0.5">Tarikh Mohon: {{ $application->tarikh_permohonan ? $application->tarikh_permohonan->format('d/m/Y') : '-' }}</div>
                    </div>
                    <div>
                        @if($application->tandatangan)
                            <div class="text-center">
                                <img src="{{ $application->tandatangan }}" alt="Tandatangan" class="h-12 max-w-[150px] border border-slate-200 rounded p-1 bg-slate-50">
                                <span class="text-[9px] text-slate-400 block mt-0.5">Tandatangan Digital</span>
                            </div>
                        @else
                            <span class="text-slate-400 italic">Tiada imej tandatangan</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- ============================================ -->
        <!-- RIGHT COLUMN: OFFICIAL ACTIONS               -->
        <!-- ============================================ -->
        <div class="lg:col-span-5 space-y-6">

            <!-- 5. BORANG TINDAKAN PEJABAT JAJAHAN -->
            @if(Auth::user()->isPegawaiJajahan())
                <div class="bg-white rounded-3xl p-6 border-2 border-emerald-600/30 shadow-md space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-2">
                            <div class="w-7 h-7 rounded-lg bg-emerald-700 text-white flex items-center justify-center text-xs font-bold">
                                <i class="fas fa-building"></i>
                            </div>
                            <h3 class="text-sm font-bold text-emerald-950 uppercase tracking-wider">Tindakan Pejabat Jajahan</h3>
                        </div>
                        <span class="text-[10px] uppercase font-bold px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded">
                            {{ $application->jajahan_ladang ?: $application->jajahan }}
                        </span>
                    </div>

                    <form action="{{ route('naimbif.admin.update_jajahan', $application->id) }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- ID Premis -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">ID PREMIS TERNAKAN</label>
                            <input type="text" name="id_premis" value="{{ old('id_premis', $application->id_premis) }}"
                                   placeholder="Contoh: JPV/KB/BL/2026/042"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 font-mono font-bold focus:ring-emerald-500">
                        </div>

                        <!-- Status Kelengkapan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">STATUS KELENGKAPAN DOKUMEN</label>
                            <select name="status_kelengkapan" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 font-semibold focus:ring-emerald-500">
                                <option value="Dalam Semakan" {{ old('status_kelengkapan', $application->status_kelengkapan) == 'Dalam Semakan' ? 'selected' : '' }}>Dalam Semakan</option>
                                <option value="Lengkap" {{ old('status_kelengkapan', $application->status_kelengkapan) == 'Lengkap' ? 'selected' : '' }}>Lengkap</option>
                                <option value="Tidak Lengkap" {{ old('status_kelengkapan', $application->status_kelengkapan) == 'Tidak Lengkap' ? 'selected' : '' }}>Tidak Lengkap</option>
                            </select>
                        </div>

                        <!-- Syor Permohonan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">SYOR PERMOHONAN JAJAHAN</label>
                            <select name="syor_permohonan" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 font-semibold focus:ring-emerald-500">
                                <option value="Belum Disemak" {{ old('syor_permohonan', $application->syor_permohonan) == 'Belum Disemak' ? 'selected' : '' }}>Belum Disemak</option>
                                <option value="Disokong" {{ old('syor_permohonan', $application->syor_permohonan) == 'Disokong' ? 'selected' : '' }}>Disokong</option>
                                <option value="Tidak disokong" {{ old('syor_permohonan', $application->syor_permohonan) == 'Tidak disokong' ? 'selected' : '' }}>Tidak Disokong</option>
                            </select>
                        </div>

                        <!-- Pegawai Penyiasat & Tarikh Siasatan -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1">PEGAWAI PENYIASAT <span class="text-rose-500">*</span></label>
                                <input type="text" name="pegawai_penyiasat" value="{{ old('pegawai_penyiasat', $application->pegawai_penyiasat ?: Auth::user()->name) }}" required
                                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-emerald-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1">TARIKH SIASATAN PREMIS</label>
                                <input type="date" name="tarikh_siasatan" value="{{ old('tarikh_siasatan', $application->tarikh_siasatan ? $application->tarikh_siasatan->format('Y-m-d') : date('Y-m-d')) }}"
                                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-emerald-500">
                            </div>
                        </div>

                        <!-- Catatan Jajahan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">CATATAN & LAPORAN SIASATAN JAJAHAN</label>
                            <textarea name="catatan_jajahan" rows="3" placeholder="Laporan keadaan kandang, biosekuriti, kualiti padang ragut..."
                                      class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-emerald-500">{{ old('catatan_jajahan', $application->catatan_jajahan) }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 transition-colors shadow-sm flex items-center justify-center">
                            <i class="fas fa-save mr-1.5"></i> Simpan Ulasan Jajahan
                        </button>
                    </form>
                </div>
            @else
                <!-- Paparan Hasil Siasatan Jajahan untuk Pegawai Negeri (Read-Only) -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-2">
                            <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-bold">
                                <i class="fas fa-building"></i>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Laporan Siasatan Jajahan</h3>
                        </div>
                        <span class="text-[10px] uppercase font-bold px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded">
                            {{ $application->jajahan_ladang ?: $application->jajahan }}
                        </span>
                    </div>

                    @if($application->syor_permohonan === 'Disokong' || $application->syor_permohonan === 'Tidak disokong' || $application->id_premis)
                        <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-700">Perakuan Syor Jajahan:</span>
                                <span class="px-2.5 py-0.5 rounded-full font-bold text-xs {{ $application->syor_permohonan === 'Disokong' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-rose-100 text-rose-800 border border-rose-300' }}">
                                    {{ $application->syor_permohonan }}
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-500">ID Premis Ternakan:</span>
                                <span class="font-mono font-bold text-emerald-900 ml-1">{{ $application->id_premis ?? 'Tiada' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500">Status Kelengkapan:</span>
                                <span class="font-semibold text-slate-800 ml-1">{{ $application->status_kelengkapan }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500">Pegawai Penyiasat:</span>
                                <span class="font-semibold text-slate-800 ml-1">{{ $application->pegawai_penyiasat ?? '-' }}</span>
                            </div>
                            @if($application->tarikh_siasatan)
                                <div>
                                    <span class="text-slate-500">Tarikh Siasatan:</span>
                                    <span class="font-semibold text-slate-800 ml-1">{{ $application->tarikh_siasatan->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            @if($application->catatan_jajahan)
                                <div class="pt-2 border-t border-emerald-200/60 text-slate-700 italic">
                                    "{{ $application->catatan_jajahan }}"
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-500 space-y-2">
                            <div class="flex items-center text-slate-700 font-bold">
                                <i class="fas fa-hourglass-half text-amber-500 mr-1.5"></i> Menunggu Siasatan Pejabat Jajahan
                            </div>
                            <p class="text-[11px] leading-relaxed text-slate-500">
                                Pejabat Veterinar Jajahan {{ $application->jajahan_ladang ?: $application->jajahan }} belum selesai membuat siasatan premis dan perakuan syor.
                            </p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- 6. BORANG KELULUSAN IBU PEJABAT (JPVNK) -->
            @if(Auth::user()->isPegawaiNegeri())
                @if($application->isDisemakJajahan())
                    <div class="bg-white rounded-3xl p-6 border-2 border-blue-600/30 shadow-md space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center space-x-2">
                                <div class="w-7 h-7 rounded-lg bg-blue-700 text-white flex items-center justify-center text-xs font-bold">
                                    <i class="fas fa-landmark"></i>
                                </div>
                                <h3 class="text-sm font-bold text-blue-950 uppercase tracking-wider">Ulasan & Kelulusan Jabatan</h3>
                            </div>
                            <span class="text-[10px] uppercase font-bold px-2 py-0.5 bg-blue-100 text-blue-800 rounded">
                                Ibu Pejabat JPVNK
                            </span>
                        </div>

                        <form action="{{ route('naimbif.admin.update_negeri', $application->id) }}" method="POST" class="space-y-4">
                            @csrf

                            <!-- Status Negeri -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">KEPUTUSAN KELULUSAN JABATAN <span class="text-rose-500">*</span></label>
                                <div class="grid grid-cols-3 gap-2">
                                    <label class="flex items-center p-2 border rounded-xl cursor-pointer hover:bg-slate-50 text-xs {{ old('status_negeri', $application->status_negeri) == 'Menunggu Kelulusan' ? 'border-blue-500 bg-blue-50' : 'border-slate-300' }}">
                                        <input type="radio" name="status_negeri" value="Menunggu Kelulusan" {{ old('status_negeri', $application->status_negeri) == 'Menunggu Kelulusan' ? 'checked' : '' }} class="text-blue-600">
                                        <span class="ml-1.5 font-semibold text-slate-800 text-[11px]">Menunggu</span>
                                    </label>
                                    <label class="flex items-center p-2 border rounded-xl cursor-pointer hover:bg-slate-50 text-xs {{ old('status_negeri', $application->status_negeri) == 'Lulus' ? 'border-emerald-500 bg-emerald-50' : 'border-slate-300' }}">
                                        <input type="radio" name="status_negeri" value="Lulus" {{ old('status_negeri', $application->status_negeri) == 'Lulus' ? 'checked' : '' }} class="text-emerald-600">
                                        <span class="ml-1.5 font-bold text-emerald-800 text-[11px]">LULUS</span>
                                    </label>
                                    <label class="flex items-center p-2 border rounded-xl cursor-pointer hover:bg-slate-50 text-xs {{ old('status_negeri', $application->status_negeri) == 'Gagal' ? 'border-rose-500 bg-rose-50' : 'border-slate-300' }}">
                                        <input type="radio" name="status_negeri" value="Gagal" {{ old('status_negeri', $application->status_negeri) == 'Gagal' ? 'checked' : '' }} class="text-rose-600">
                                        <span class="ml-1.5 font-bold text-rose-800 text-[11px]">GAGAL</span>
                                    </label>
                                </div>
                            </div>

                            <!-- No Rujukan Negeri -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">NO. RUJUKAN KELULUSAN RASMI</label>
                                <input type="text" name="no_rujukan_negeri" value="{{ old('no_rujukan_negeri', $application->no_rujukan_negeri) }}"
                                       placeholder="Biarkan kosong untuk auto-generate semasa Lulus"
                                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 font-mono font-bold focus:ring-blue-500">
                            </div>

                            <!-- Pegawai Pelulus -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">PEGAWAI PELULUS <span class="text-rose-500">*</span></label>
                                <input type="text" name="pegawai_pelulus" value="{{ old('pegawai_pelulus', $application->pegawai_pelulus ?: (Auth::user()->isPegawaiNegeri() ? Auth::user()->name : 'Dr. Ahmad Farhan bin Ismail')) }}" required
                                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-blue-500">
                            </div>

                            <!-- Ulasan Negeri -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">ULASAN / SYARAT KELULUSAN JABATAN</label>
                                <textarea name="ulasan_negeri" rows="3" placeholder="Ulasan rasmi mesyuarat jawatankuasa ladang bridlot..."
                                          class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-blue-500">{{ old('ulasan_negeri', $application->ulasan_negeri) }}</textarea>
                            </div>

                            <button type="submit" class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-blue-700 hover:bg-blue-800 transition-colors shadow-sm flex items-center justify-center">
                                <i class="fas fa-stamp mr-1.5"></i> Kemas Kini Keputusan Jabatan
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Notifikasi Menunggu Siasatan Pejabat Jajahan untuk Pegawai Negeri -->
                    <div class="bg-white rounded-3xl p-6 border-2 border-slate-200 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center space-x-2">
                                <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Ulasan & Kelulusan Jabatan</h3>
                            </div>
                            <span class="text-[10px] uppercase font-bold px-2.5 py-0.5 bg-amber-100 text-amber-800 rounded-full">
                                Menunggu Siasatan Jajahan
                            </span>
                        </div>

                        <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200 text-xs text-amber-950 space-y-2">
                            <div class="flex items-center font-bold text-amber-900">
                                <i class="fas fa-hourglass-half text-amber-600 mr-2 text-sm"></i>
                                Tindakan Kelulusan Belum Dibuka
                            </div>
                            <p class="text-[11px] leading-relaxed text-amber-800">
                                Pegawai Ibu Pejabat JPVNK (Negeri) hanya boleh membuat <strong>Ulasan & Kelulusan Jabatan</strong> selepas <strong>Pejabat Veterinar Jajahan {{ $application->jajahan_ladang ?: $application->jajahan }}</strong> selesai menjalankan siasatan premis dan menghantar perakuan syor (Disokong / Tidak Disokong).
                            </p>
                        </div>
                    </div>
                @endif
            @else
                <!-- Paparan Status Sahaja untuk Pegawai Jajahan (Read-Only) -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-2">
                            <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-bold">
                                <i class="fas fa-landmark"></i>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Keputusan Ibu Pejabat JPVNK</h3>
                        </div>
                        <span class="text-[10px] uppercase font-bold px-2 py-0.5 bg-slate-100 text-slate-600 rounded">
                            Bidang Kuasa Negeri
                        </span>
                    </div>

                    @if($application->status_negeri === 'Lulus' || $application->status_negeri === 'Gagal')
                        <div class="p-4 rounded-2xl {{ $application->status_negeri === 'Lulus' ? 'bg-emerald-50 border border-emerald-200' : 'bg-rose-50 border border-rose-200' }} space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-700">Status Kelulusan:</span>
                                {!! $application->status_badge !!}
                            </div>
                            @if($application->no_rujukan_negeri)
                                <div>
                                    <span class="text-slate-500">No. Rujukan Kelulusan:</span>
                                    <span class="font-mono font-bold text-slate-900 ml-1">{{ $application->no_rujukan_negeri }}</span>
                                </div>
                            @endif
                            @if($application->pegawai_pelulus)
                                <div>
                                    <span class="text-slate-500">Pegawai Pelulus:</span>
                                    <span class="font-semibold text-slate-800 ml-1">{{ $application->pegawai_pelulus }}</span>
                                </div>
                            @endif
                            @if($application->tarikh_kelulusan_negeri)
                                <div>
                                    <span class="text-slate-500">Tarikh Kelulusan:</span>
                                    <span class="font-semibold text-slate-800 ml-1">{{ $application->tarikh_kelulusan_negeri->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            @if($application->ulasan_negeri)
                                <div class="pt-2 border-t border-slate-200/60 text-slate-700 italic">
                                    "{{ $application->ulasan_negeri }}"
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-500 space-y-2">
                            <div class="flex items-center text-slate-700 font-bold">
                                <i class="fas fa-lock text-slate-400 mr-1.5"></i> Menunggu Keputusan Ibu Pejabat
                            </div>
                            <p class="text-[11px] leading-relaxed text-slate-500">
                                Keputusan kelulusan rasmi adalah di bawah bidang kuasa Ibu Pejabat JPVNK (Negeri) setelah siasatan dan syor jajahan diserahkan.
                            </p>
                        </div>
                    @endif
                </div>
            @endif

        </div>

    </div>

</div>
@endsection

@push('scripts')
<!-- Leaflet Map JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lat = parseFloat('{{ $application->gps_latitud }}') || 6.1254;
        const lng = parseFloat('{{ $application->gps_longitud }}') || 102.2381;

        const map = L.map('previewMap').setView([lat, lng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        L.marker([lat, lng]).addTo(map)
            .bindPopup('<b>{{ $application->nama }}</b><br>{{ $application->jajahan_ladang ?: $application->jajahan }}')
            .openPopup();
    });
</script>
@endpush
