@extends('layouts.app')

@section('title', 'EPTR - Enakmen Pendaftaran Ternakan Ruminan')
@section('page_title', 'Enakmen Pendaftaran Ternakan Ruminan (EPTR 2024)')

@section('content')
<div class="space-y-6">

    <!-- Header Actions & KPI -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Senarai Ternakan Ruminan Berdaftar</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pengurusan pendaftaran ternakan (Lembu, Kerbau, Kambing, Biri-biri), salasilah anak, program kesihatan & pawah</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(!Auth::user()->isStaff() || Auth::user()->isSuperAdmin())
            <a href="{{ route('eptr.daftar-anak') }}" class="px-3.5 py-2 rounded-xl bg-pink-50 hover:bg-pink-100 text-pink-800 border border-pink-300 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                <i class="fa-solid fa-baby text-pink-600"></i>
                <span>Daftar Anak Ternakan</span>
            </a>
            @endif
            @if(Auth::user()->role !== 'orang_awam')
            <a href="{{ route('eptr.penternak.index') }}" class="px-3.5 py-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-300 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                <i class="fa-solid fa-users text-sky-600"></i>
                <span>Senarai Penternak</span>
            </a>
            @endif
            <a href="{{ route('eptr.kesihatan.index') }}" class="px-3.5 py-2 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-300 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                <i class="fa-solid fa-heart-pulse text-teal-600"></i>
                <span>Program Kesihatan</span>
            </a>
            <a href="{{ route('eptr.borang-b.index') }}" class="px-3.5 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-300 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                <i class="fa-solid fa-arrows-rotate text-blue-600"></i>
                <span>Pindah Milik (Borang B)</span>
            </a>
            <a href="{{ route('eptr.borang-c.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                <i class="fa-solid fa-file-circle-xmark text-rose-600"></i>
                <span>Borang C</span>
            </a>
            <a href="{{ route('eptr.borang-d.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                <i class="fa-solid fa-file-invoice text-amber-600"></i>
                <span>Borang D</span>
            </a>
            @if(!Auth::user()->isStaff() || Auth::user()->isSuperAdmin())
            <a href="{{ route('eptr.create') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-lg shadow-emerald-700/30 transition flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Daftar Ternakan (Borang A)</span>
            </a>
            @endif
        </div>
    </div>

    <!-- KPI Summary Pills -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-cow"></i>
            </div>
            <div>
                <div class="text-lg font-black text-slate-900">{{ $totalCows }}</div>
                <div class="text-[11px] text-slate-500 font-semibold">Lembu</div>
            </div>
        </div>
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-shield-cat"></i>
            </div>
            <div>
                <div class="text-lg font-black text-slate-900">{{ $totalGoats }}</div>
                <div class="text-[11px] text-slate-500 font-semibold">Kambing</div>
            </div>
        </div>
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-hippo"></i>
            </div>
            <div>
                <div class="text-lg font-black text-slate-900">{{ $totalBuffalo }}</div>
                <div class="text-[11px] text-slate-500 font-semibold">Kerbau</div>
            </div>
        </div>
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-shield-heart"></i>
            </div>
            <div>
                <div class="text-lg font-black text-slate-900">{{ $totalSheep }}</div>
                <div class="text-[11px] text-slate-500 font-semibold">Biri-biri</div>
            </div>
        </div>
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center gap-3 col-span-2 sm:col-span-1">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-handshake"></i>
            </div>
            <div>
                <div class="text-lg font-black text-amber-800">{{ $totalWithProgram }}</div>
                <div class="text-[11px] text-slate-500 font-semibold">Program Pawah / Bantuan</div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form action="{{ route('eptr.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 {{ Auth::user()->isStaff() ? 'lg:grid-cols-6' : 'lg:grid-cols-5' }} gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-600 mb-1">Carian No Tag / Nama</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="No Tag / No KP / Nama" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            @if(Auth::user()->isStaff())
            <div>
                <label class="block font-bold text-slate-600 mb-1">Pilih Pemilik / Pemunya</label>
                <select name="pemunya_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">-- Semua Pemilik --</option>
                    @foreach($pemunyaList as $p)
                        <option value="{{ $p->id }}" {{ request('pemunya_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }} ({{ $p->jajahan }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block font-bold text-slate-600 mb-1">Jenis Ternakan</label>
                <select name="jenis_ternakan" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Semua Jenis</option>
                    <option value="Lembu" {{ request('jenis_ternakan') == 'Lembu' ? 'selected' : '' }}>Lembu</option>
                    <option value="Kerbau" {{ request('jenis_ternakan') == 'Kerbau' ? 'selected' : '' }}>Kerbau</option>
                    <option value="Kambing" {{ request('jenis_ternakan') == 'Kambing' ? 'selected' : '' }}>Kambing</option>
                    <option value="Biri-biri" {{ request('jenis_ternakan') == 'Biri-biri' ? 'selected' : '' }}>Biri-biri</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Jajahan</label>
                <select name="jajahan" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Semua Jajahan</option>
                    @foreach(['Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Pasir Puteh', 'Machang', 'Tanah Merah', 'Jeli', 'Kuala Krai', 'Gua Musang'] as $j)
                        <option value="{{ $j }}" {{ request('jajahan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Status Program</label>
                <select name="program" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Semua</option>
                    <option value="Ada Program" {{ request('program') == 'Ada Program' ? 'selected' : '' }}>Ada Program Bantuan / Pawah</option>
                    <option value="Tiada Program" {{ request('program') == 'Tiada Program' ? 'selected' : '' }}>Tiada Program (Persendirian)</option>
                    <option value="Pawah" {{ request('program') == 'Pawah' ? 'selected' : '' }}>Program Pawah</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> Tapis
                </button>
                <a href="{{ route('eptr.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition" title="Reset Penapis">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Multi-Select & Bulk Printing Container -->
    <div x-data="{
        selectedIds: [],
        allIds: {{ json_encode($ternakanList->pluck('id')->map(fn($id) => (string)$id)) }},
        toggleSelectAll() {
            if (this.selectedIds.length === this.allIds.length) {
                this.selectedIds = [];
            } else {
                this.selectedIds = [...this.allIds];
            }
        },
        submitBulkPrint(routeUrl) {
            if (this.selectedIds.length === 0) {
                alert('Sila pilih sekurang-kurangnya satu ekor ternakan.');
                return;
            }
            let form = document.getElementById('bulkPrintForm');
            form.action = routeUrl;
            document.getElementById('bulkPrintIds').value = this.selectedIds.join(',');
            form.submit();
        }
    }">

        <!-- Hidden Form for Bulk Print Submission -->
        <form id="bulkPrintForm" method="POST" target="_blank" class="hidden">
            @csrf
            <input type="hidden" name="ids" id="bulkPrintIds" value="">
        </form>

        <!-- Floating Bulk Action Toolbar (Appears when rows are selected) -->
        @if(Auth::user()->isStaff())
            <div x-show="selectedIds.length > 0" x-cloak class="p-4 mb-4 bg-slate-900 text-white rounded-2xl shadow-xl border border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-3 transition-all animate-fadeIn">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-emerald-500 text-slate-950 font-black flex items-center justify-center text-xs" x-text="selectedIds.length"></span>
                    <div>
                        <div class="text-xs font-bold text-white"><span x-text="selectedIds.length"></span> ekor ternakan dipilih</div>
                        <div class="text-[10px] text-slate-400">Pilih tindakan cetakan pukal untuk admin jajahan</div>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="submitBulkPrint('{{ route('eptr.cetak-pukal-borang-a') }}')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-print text-emerald-200"></i>
                        <span>Cetak Pukal Borang A (<span x-text="selectedIds.length"></span>)</span>
                    </button>
                    <button type="button" @click="submitBulkPrint('{{ route('eptr.cetak-pukal-borang-b') }}')" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-file-certificate text-amber-200"></i>
                        <span>Cetak Pukal Borang B - Kad Kuning (<span x-text="selectedIds.length"></span>)</span>
                    </button>
                    <button type="button" @click="selectedIds = []" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">
                        Nyahpilih
                    </button>
                </div>
            </div>
        @endif

        <!-- Ternakan Table -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                            @if(Auth::user()->isStaff())
                                <th class="px-3.5 py-3.5 w-10 text-center">
                                    <input type="checkbox" @click="toggleSelectAll()" :checked="selectedIds.length > 0 && selectedIds.length === allIds.length" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                </th>
                            @endif
                            <th class="px-4 py-3.5">No Tag Telinga</th>
                            <th class="px-4 py-3.5">Pemunya</th>
                            <th class="px-4 py-3.5">Jenis & Baka</th>
                            <th class="px-4 py-3.5">Jantina & Umur</th>
                            <th class="px-4 py-3.5">Jajahan / Daerah</th>
                            <th class="px-4 py-3.5">Kolum Program (Pilihan)</th>
                            <th class="px-4 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($ternakanList as $t)
                            <tr class="hover:bg-slate-50/80 transition" :class="selectedIds.includes('{{ (string)$t->id }}') ? 'bg-emerald-50/40' : ''">
                                @if(Auth::user()->isStaff())
                                    <td class="px-3.5 py-3.5 text-center">
                                        <input type="checkbox" value="{{ (string)$t->id }}" x-model="selectedIds" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                    </td>
                                @endif
                                <td class="px-4 py-3.5 font-mono font-bold text-emerald-800">
                                    @if($t->no_tag)
                                        <a href="{{ route('eptr.show', $t->id) }}" class="hover:underline flex items-center gap-1.5">
                                            <i class="fa-solid fa-tag text-emerald-600"></i>
                                            <span>{{ $t->no_tag }}</span>
                                        </a>
                                        <div class="text-[10px] text-slate-400 font-sans font-normal">{{ $t->no_siri_kad_kuning ?? 'DB-2026' }}</div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-800 border border-amber-300">
                                            <i class="fa-solid fa-clock-rotate-left text-amber-600"></i>
                                            <span>Menunggu Tag</span>
                                        </span>
                                        <div class="text-[10px] text-slate-400 font-sans font-normal">Belum Diluluskan</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-slate-900">{{ $t->pemunya->nama ?? 'N/A' }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">{{ $t->pemunya->no_kp ?? '-' }} &bull; {{ $t->pemunya->no_telefon ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-semibold text-slate-900 capitalize">{{ $t->baka }}</div>
                                    <div class="text-[11px] text-slate-500 capitalize">{{ $t->jenis_ternakan }} &bull; {{ $t->warna ?? 'Warna Asal' }}</div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center gap-1 font-semibold {{ $t->jantina === 'Jantan' ? 'text-blue-700' : 'text-pink-700' }}">
                                        <i class="fa-solid {{ $t->jantina === 'Jantan' ? 'fa-mars' : 'fa-venus' }}"></i>
                                        {{ $t->jantina }}
                                    </span>
                                    <div class="text-[11px] text-slate-500">{{ $t->umur ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-medium text-slate-900">{{ $t->jajahan }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $t->daerah ?? ($t->mukim ?? '-') }}</div>
                                </td>
                                <td class="px-4 py-3.5">
                                    @if($t->program && $t->program !== 'Tiada')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                            <i class="fa-solid fa-handshake mr-1.5 text-amber-700"></i>
                                            {{ $t->program }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px] italic">Tiada (Persendirian)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $t->status_kelulusan === 'Menunggu' ? 'bg-amber-100 text-amber-800 border border-amber-300' : ($t->status === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : ($t->status === 'Pawah' ? 'bg-blue-100 text-blue-800' : ($t->status === 'Sembelih' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800'))) }}">
                                        {{ $t->status_kelulusan === 'Menunggu' ? 'Menunggu Kelulusan' : $t->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                    @if($t->isDibatalkanAtauMatiAtauSembelih())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-100 text-slate-500 font-bold text-xs border border-slate-200" title="Ternakan telah dibatalkan / mati / disembelih - tiada rawatan atau perubahan dibenarkan">
                                            <i class="fa-solid fa-lock text-[10px]"></i> Rekod Ditutup
                                        </span>
                                    @else
                                        <!-- Tindakan Program Kesihatan & Vaksinasi -->
                                        @if($t->status_kelulusan === 'Diluluskan')
                                            <a href="{{ route('eptr.kesihatan.create', ['ternakan_id' => $t->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 font-bold border border-teal-300 transition" title="Tambah Rekod Program Kesihatan / Vaksinasi">
                                                <i class="fa-solid fa-heart-pulse text-teal-600"></i>
                                                <span>+ Kesihatan</span>
                                            </a>
                                        @endif

                                        <!-- Tindakan Daftar Anak (Jika Induk Betina & Bukan Staf / Super Admin Sahaja) -->
                                        @if($t->jantina === 'Betina' && (!Auth::user()->isStaff() || Auth::user()->isSuperAdmin()) && $t->status_kelulusan === 'Diluluskan')
                                            <a href="{{ route('eptr.daftar-anak', ['induk_id' => $t->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-pink-50 hover:bg-pink-100 text-pink-800 font-bold border border-pink-300 transition" title="Daftar Kelahiran Anak Bagi Induk Ini">
                                                <i class="fa-solid fa-baby text-pink-600"></i>
                                                <span>+ Anak</span>
                                            </a>
                                        @endif
                                    @endif

                                    <!-- Butang Kelulusan Pegawai / Admin Jajahan -->
                                    @if($t->status_kelulusan === 'Menunggu' && Auth::user()->isStaff())
                                        <form action="{{ route('eptr.lulus', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('Luluskan pendaftaran ternakan ini dan jana No. Tag Telinga rasmi mengikut Daerah {{ $t->daerah }}?');">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition" title="Luluskan & Jana No Tag">
                                                <i class="fa-solid fa-circle-check"></i> Lulus & Tag
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Kad Kuning (Pasport Ternakan) -->
                                    <a href="{{ route('eptr.show', $t->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold border border-slate-300 transition" title="Buka Kad Kuning (Pasport Ternakan)">
                                        <i class="fa-solid fa-id-card text-amber-600"></i> Kad Kuning
                                    </a>

                                    @if($t->resit_pembayaran)
                                        <a href="{{ asset('storage/' . $t->resit_pembayaran) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold border border-indigo-200 transition text-xs" title="Buka Gambar / Fail Resit Asal (Tab Baharu)">
                                            <i class="fa-solid fa-file-invoice-dollar"></i> Resit
                                        </a>
                                    @endif

                                    <!-- Pindah Milik (Borang B) -->
                                    @if($t->no_tag && !$t->isDibatalkanAtauMatiAtauSembelih())
                                        <a href="{{ route('eptr.borang-b.create', ['ternakan_id' => $t->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold border border-blue-200 transition" title="Pindah Milik Ternakan (Borang B)">
                                            <i class="fa-solid fa-arrows-rotate text-blue-600"></i>
                                            <span>Pindah</span>
                                        </a>
                                    @endif

                                    <!-- Butang Cetakan Rasmi (Hanya untuk Admin Jajahan / Pegawai setelah Diluluskan) -->
                                    @if(Auth::user()->isStaff())
                                        @if($t->status_kelulusan === 'Diluluskan' && !empty($t->no_tag))
                                            <a href="{{ route('eptr.cetak-borang-a', $t->id) }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold border border-slate-300 transition" title="Cetak Borang A Asal (Pegawai Sahaja)">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                            <a href="{{ route('eptr.cetak-kad-kuning', $t->id) }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold border border-amber-300 transition" title="Cetak Kad Kuning Pasport (Pegawai Sahaja)">
                                                <i class="fa-solid fa-file-contract text-amber-600"></i>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-50 text-slate-400 font-medium text-[10px] border border-slate-200" title="Dokumen hanya boleh dicetak setelah permohonan diluluskan">
                                                <i class="fa-solid fa-lock text-[9px]"></i> Belum Lulus
                                            </span>
                                        @endif
                                    @endif

                                    <!-- Permit Sembelih (Borang D) -->
                                    @if($t->no_tag && !$t->isDibatalkanAtauMatiAtauSembelih())
                                        <a href="{{ route('eptr.borang-d.create', ['ternakan_id' => $t->id]) }}" class="inline-flex items-center gap-1 px-2 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition" title="Mohon Permit Sembelih (Borang D)">
                                            <i class="fa-solid fa-receipt text-amber-600"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ Auth::user()->isStaff() ? '9' : '8' }}" class="px-6 py-12 text-center text-slate-400">
                                    <i class="fa-solid fa-cow text-4xl text-slate-300 mb-2 block"></i>
                                    Tiada rekod ternakan yang sepadan dijumpai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($ternakanList->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $ternakanList->links() }}
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
