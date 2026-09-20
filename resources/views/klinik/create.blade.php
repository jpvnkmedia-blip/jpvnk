@extends('layouts.app')

@section('title', 'Daftar Temujanji Rawatan Klinik')
@section('page_title', 'Klinik Veterinar: Tempahan Temujanji Rawatan')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
    
    <div class="border-b border-slate-100 pb-4 mb-6">
        <h3 class="text-base font-bold text-slate-900">Borang Tempahan Temujanji Klinik Veterinar</h3>
        <p class="text-xs text-slate-500 mt-0.5">Sila masukkan butiran haiwan kesayangan / ternakan dan pilih klinik jajahan berhampiran.</p>
    </div>

    <form action="{{ route('klinik.store') }}" method="POST" class="space-y-4 text-xs">
        @csrf

        @if(Auth::user()->isStaff())
        <div x-data="{
            lookupMode: 'ic',
            noKp: '{{ old('no_kp_pemilik', '') }}',
            userId: '{{ old('user_id', '') }}',
            namaPemilik: '{{ old('nama_pemilik', '') }}',
            noTelefon: '{{ old('no_telefon_pemilik', '') }}',
            emelPemilik: '{{ old('emel_pemilik', '') }}',
            alamatPemilik: '{{ old('alamat_pemilik', '') }}',
            jajahanPemilik: '{{ old('jajahan_pemilik', '') }}',
            statusCarian: null,
            statusMesej: '',
            
            async semakNoKp() {
                let clean = (this.noKp || '').replace(/[^0-9]/g, '');
                if (clean.length < 6) {
                    this.statusCarian = null;
                    this.statusMesej = '';
                    this.userId = '';
                    return;
                }
                this.statusCarian = 'loading';
                this.statusMesej = 'Menyemak rekod pangkalan data JPVNK...';
                try {
                    const res = await fetch(`{{ route('klinik.semak_pemilik') }}?no_kp=${clean}`);
                    const data = await res.json();
                    if (data.found) {
                        this.statusCarian = 'found';
                        this.userId = data.user_id || '';
                        this.namaPemilik = data.nama || '';
                        this.noTelefon = data.no_telefon || '';
                        this.emelPemilik = data.emel || '';
                        this.alamatPemilik = data.alamat || '';
                        this.jajahanPemilik = data.jajahan || '';
                        this.statusMesej = `✓ Rekod Ditemui: ${data.nama} (${data.role_label || 'Akaun Berdaftar'})`;
                    } else {
                        this.statusCarian = 'new';
                        this.userId = '';
                        this.statusMesej = 'ℹ Rekod No. KP belum berdaftar. Sila lengkapkan maklumat pemilik di bawah untuk pendaftaran pengguna baharu secara automatik.';
                    }
                } catch(e) {
                    this.statusCarian = null;
                    this.statusMesej = '';
                }
            },

            pilihDariSenarai(e) {
                const opt = e.target.options[e.target.selectedIndex];
                if (opt && opt.value) {
                    this.userId = opt.value;
                    this.noKp = opt.dataset.ic || '';
                    this.namaPemilik = opt.dataset.name || '';
                    this.noTelefon = opt.dataset.phone || '';
                    this.emelPemilik = opt.dataset.email || '';
                    this.jajahanPemilik = opt.dataset.jajahan || '';
                    this.alamatPemilik = opt.dataset.alamat || '';
                    this.statusCarian = 'found';
                    this.statusMesej = `✓ Dipilih: ${opt.dataset.name} (${opt.dataset.role || 'Akaun Berdaftar'})`;
                } else {
                    this.userId = '';
                    this.statusCarian = null;
                    this.statusMesej = '';
                }
            },

            resetForm() {
                this.userId = '';
                this.noKp = '';
                this.namaPemilik = '';
                this.noTelefon = '';
                this.emelPemilik = '';
                this.alamatPemilik = '';
                this.jajahanPemilik = '';
                this.statusCarian = null;
                this.statusMesej = '';
            }
        }" class="p-5 rounded-3xl bg-linear-to-br from-rose-50/80 via-white to-rose-50/50 border-2 border-rose-200/80 shadow-xs space-y-4">
            
            <div class="flex flex-wrap items-center justify-between gap-2 pb-2 border-b border-rose-100">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center text-xs shadow-xs">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <div>
                        <span class="font-extrabold text-sm text-rose-950 uppercase tracking-wide">Daftar Bagi Pihak Pemilik / Orang Awam (Pilihan Admin Klinik)</span>
                    </div>
                </div>

                <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-rose-200 text-[11px]">
                    <button type="button" @click="lookupMode = 'ic'" :class="lookupMode === 'ic' ? 'bg-rose-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="px-2.5 py-1 rounded-lg transition">
                        <i class="fa-solid fa-id-card mr-1"></i> No. KP (Carian / Baru)
                    </button>
                    @if(count($registeredClients) > 0)
                    <button type="button" @click="lookupMode = 'dropdown'" :class="lookupMode === 'dropdown' ? 'bg-rose-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="px-2.5 py-1 rounded-lg transition">
                        <i class="fa-solid fa-list mr-1"></i> Pilih Dari Senarai
                    </button>
                    @endif
                </div>
            </div>

            <!-- Mode 1: Masukkan No Kad Pengenalan -->
            <div x-show="lookupMode === 'ic'" class="space-y-3">
                <div>
                    <label class="block font-bold text-slate-800 uppercase mb-1 flex items-center justify-between">
                        <span>No. Kad Pengenalan Pemilik (IC)</span>
                        <span class="text-[10px] font-normal text-slate-500 normal-case">Semakan automatik semasa taip</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="no_kp_pemilik" x-model="noKp" @input.debounce.350ms="semakNoKp()" placeholder="Contoh: 880101035555 atau 880101-03-5555" class="w-full pl-10 pr-24 py-2.5 text-sm bg-white border border-rose-300 rounded-xl focus:ring-2 focus:ring-rose-500 focus:outline-none font-mono font-bold text-slate-900">
                        <div class="absolute left-3.5 top-3 text-rose-500">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <button type="button" @click="semakNoKp()" class="absolute right-2 top-1.5 px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-[11px] font-bold transition shadow-xs">
                            Semak
                        </button>
                    </div>
                </div>

                <!-- Status Alert Box -->
                <template x-if="statusCarian === 'loading'">
                    <div class="p-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs flex items-center gap-2 animate-pulse">
                        <i class="fa-solid fa-circle-notch fa-spin text-blue-600"></i>
                        <span x-text="statusMesej"></span>
                    </div>
                </template>

                <template x-if="statusCarian === 'found'">
                    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs flex items-start justify-between gap-2 shadow-xs">
                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-base mt-0.5"></i>
                            <div>
                                <div class="font-bold text-emerald-950" x-text="statusMesej"></div>
                                <div class="text-[11px] text-emerald-800 mt-0.5">Maklumat pemilik telah diisi secara automatik. Anda boleh mengemaskini no telefon / alamat jika terdapat pertukaran terkini.</div>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-200 text-emerald-900">Akaun Sedia Ada</span>
                    </div>
                </template>

                <template x-if="statusCarian === 'new'">
                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-xs flex items-start gap-2 shadow-xs">
                        <i class="fa-solid fa-user-plus text-amber-600 text-base mt-0.5"></i>
                        <div>
                            <div class="font-bold text-amber-950" x-text="statusMesej"></div>
                            <div class="text-[11px] text-amber-800 mt-0.5">Sila lengkapkan Nama Penuh &amp; No. Telefon di bawah. Sistem akan mendaftarkan akaun baharu ini secara automatik semasa tempahan disimpan.</div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Mode 2: Pilih dari Senarai Pengguna Berdaftar -->
            @if(count($registeredClients) > 0)
            <div x-show="lookupMode === 'dropdown'" class="space-y-2">
                <label class="block font-bold text-slate-800 uppercase mb-1">Pilih Pengguna / Pemilik Dari Senarai</label>
                <select @change="pilihDariSenarai($event)" class="w-full px-3.5 py-2.5 text-sm bg-white border border-rose-300 rounded-xl focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    <option value="">-- Sila Pilih Pemilik Sedia Ada --</option>
                    @foreach($registeredClients as $client)
                        <option value="{{ $client->id }}"
                                data-ic="{{ $client->ic_number }}"
                                data-name="{{ $client->name }}"
                                data-phone="{{ $client->phone ?? $client->no_telefon }}"
                                data-email="{{ $client->email }}"
                                data-jajahan="{{ $client->jajahan }}"
                                data-alamat="{{ $client->alamat }}"
                                data-role="{{ $client->role_label }}">
                            {{ $client->name }} (KP: {{ $client->ic_number ?: 'N/A' }} - Tel: {{ $client->phone ?: '-' }}) [{{ $client->role_label }}]
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Maklumat Pemilik (Autofill / Pendaftaran Baru) -->
            <input type="hidden" name="user_id" :value="userId">

            <div class="pt-2 border-t border-rose-100/80 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">
                        Nama Penuh Pemilik
                    </label>
                    <input type="text" name="nama_pemilik" x-model="namaPemilik" placeholder="Nama Penuh Pemilik Haiwan" class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:outline-none font-semibold text-slate-900">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">
                        No. Telefon / WhatsApp
                    </label>
                    <input type="text" name="no_telefon_pemilik" x-model="noTelefon" placeholder="Contoh: 0199998888" class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:outline-none font-semibold text-slate-900">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">
                        Emel Pemilik <span class="text-slate-400 font-normal normal-case">(Pilihan)</span>
                    </label>
                    <input type="email" name="emel_pemilik" x-model="emelPemilik" placeholder="Contoh: pemilik@gmail.com" class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:outline-none text-slate-900">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">
                        Jajahan / Daerah <span class="text-slate-400 font-normal normal-case">(Pilihan)</span>
                    </label>
                    <input type="text" name="jajahan_pemilik" x-model="jajahanPemilik" placeholder="Contoh: Kota Bharu" class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:outline-none text-slate-900">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 uppercase mb-1">
                        Alamat Tetap / Premis Pemilik <span class="text-slate-400 font-normal normal-case">(Pilihan)</span>
                    </label>
                    <input type="text" name="alamat_pemilik" x-model="alamatPemilik" placeholder="Contoh: No 123, Kg Wakaf Bharu, Tumpat" class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:outline-none text-slate-900">
                </div>
            </div>

            <div class="flex items-center justify-between text-[11px] text-rose-800 pt-1">
                <span><i class="fa-solid fa-circle-info mr-1"></i> Biarkan ruangan di atas kosong jika temujanji adalah bagi akaun sendiri ({{ Auth::user()->name }}).</span>
                <button type="button" @click="resetForm()" class="text-rose-600 hover:text-rose-900 font-bold underline">
                    Kosongkan
                </button>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Jenis Haiwan</label>
                <select name="jenis_haiwan" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none font-bold">
                    <option value="Kucing">Kucing</option>
                    <option value="Lembu">Lembu</option>
                    <option value="Kambing">Kambing</option>
                    <option value="Anjing">Anjing</option>
                    <option value="Kuda">Kuda</option>
                    <option value="Unggas / Burung">Unggas / Burung</option>
                    <option value="Lain-lain">Lain-lain</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Nama / Panggilan Haiwan (Jika Ada)</label>
                <input type="text" name="nama_haiwan" value="{{ old('nama_haiwan') }}" placeholder="Contoh: Comel / Si Tompok" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Baka Haiwan</label>
                <input type="text" name="baka" value="{{ old('baka') }}" placeholder="Contoh: DSH, Persian, KK" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Jantina</label>
                <select name="jantina_haiwan" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    <option value="Jantan">Jantan</option>
                    <option value="Betina">Betina</option>
                    <option value="Tidak Diketahui">Tidak Diketahui</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Umur Haiwan</label>
                <input type="text" name="umur_haiwan" placeholder="Contoh: 1 Tahun 2 Bulan" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Tujuan Temujanji / Simptom Penyakit</label>
            <textarea name="simptom_atau_tujuan" rows="3" required placeholder="Nyatakan sebab rawatan (Contoh: Pemeriksaan berkala, suntikan vaksin, hilang selera makan, luka kecederaan)" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">{{ old('simptom_atau_tujuan') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Cadangan Temujanji</label>
                <input type="date" name="tarikh_temujanji" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Sesi Masa</label>
                <select name="sesi" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none font-bold">
                    <option value="Pagi (8:30 AM - 12:30 PM)">Pagi (8:30 AM - 12:30 PM)</option>
                    <option value="Petang (2:00 PM - 4:30 PM)">Petang (2:00 PM - 4:30 PM)</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Klinik / Pusat Veterinar Jajahan</label>
            <select name="klinik_jajahan" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                @foreach($klinikList as $k)
                    <option value="{{ $k }}">{{ $k }}</option>
                @endforeach
            </select>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3">
            <a href="{{ route('klinik.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-lg shadow-rose-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Sahkan Tempahan Temujanji</span>
            </button>
        </div>
    </form>
</div>
@endsection
