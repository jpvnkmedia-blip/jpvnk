@extends('layouts.app')

@section('title', 'Pengurusan Pengguna Sistem')
@section('page_title', 'Pengurusan Pengguna & Akses Peranan')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-amber-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border border-slate-700">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-300 text-xs font-bold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-crown text-amber-400"></i> Modul Super Admin
            </span>
            <h2 class="text-2xl font-black">Pengurusan Pengguna &amp; Peranan</h2>
            <p class="text-xs text-slate-300 mt-1">Uruskan akaun kakitangan pentadbir, pegawai jajahan, penternak dan pengguna awam sistem.</p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('users.create') }}" class="px-5 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-lg shadow-amber-900/40 flex items-center gap-2 transition hover:scale-102">
                <i class="fa-solid fa-user-plus text-sm"></i>
                <span>Tambah Pengguna Baharu</span>
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[10px] font-bold text-slate-500 uppercase">Jumlah Pengguna</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalUsers }}</div>
            <div class="text-[10px] text-emerald-600 mt-0.5 font-bold">{{ $totalAktif }} Aktif</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[10px] font-bold text-slate-500 uppercase">Staf &amp; Pentadbir</div>
            <div class="text-2xl font-black text-purple-700 mt-1">{{ $totalStaff }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Pegawai &amp; Admin</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[10px] font-bold text-slate-500 uppercase">Penternak Ruminan</div>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ $totalPenternak }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Berdaftar EPTR</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[10px] font-bold text-slate-500 uppercase">Usahawan Unggas</div>
            <div class="text-2xl font-black text-amber-700 mt-1">{{ $totalUsahawan }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Berdaftar EPU</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[10px] font-bold text-slate-500 uppercase">Orang Awam</div>
            <div class="text-2xl font-black text-slate-700 mt-1">{{ $totalAwam }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Pemohon Perkhidmatan</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[10px] font-bold text-slate-500 uppercase">Status Aktif</div>
            <div class="text-2xl font-black text-teal-700 mt-1">{{ $totalAktif }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">{{ $totalUsers - $totalAktif }} Tidak Aktif</div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 shadow-2xs">
        <form action="{{ route('users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <div class="lg:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, No. KP, Emel atau Telefon..." class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400"></i>
            </div>
            <div>
                <select name="role" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="semua">-- Semua Peranan (Role) --</option>
                    @foreach($roleDefinitions as $groupName => $roles)
                        <optgroup label="{{ $groupName }}">
                            @foreach($roles as $rKey => $rMeta)
                                <option value="{{ $rKey }}" {{ request('role') === $rKey ? 'selected' : '' }}>{{ $rMeta['label'] }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="jajahan" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="semua">-- Semua Jajahan --</option>
                    @foreach($jajahanList as $j)
                        <option value="{{ $j }}" {{ request('jajahan') === $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="semua">-- Semua Status --</option>
                    <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Tidak Aktif" {{ request('status') === 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
                <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shrink-0 transition">
                    Tapis
                </button>
                @if(request()->hasAny(['search', 'role', 'jajahan', 'status']))
                    <a href="{{ route('users.index') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl shrink-0 transition" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bulk Action Toolbar (Tindakan Pukal / Multi Delete) -->
    <div id="bulkActionBar" class="hidden bg-slate-900 text-white p-4 rounded-2xl border border-slate-800 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-3 transition-all duration-300">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-full bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 font-bold text-xs">
                <i class="fa-solid fa-check-double"></i>
            </span>
            <div>
                <span class="font-bold text-sm text-white"><span id="selectedCount" class="text-amber-400 font-black">0</span> pengguna dipilih</span>
                <p class="text-[11px] text-slate-400">Pilih tindakan operasi pukal untuk rekod yang ditandakan.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" id="clearSelectionBtn" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition flex items-center gap-1.5">
                <i class="fa-solid fa-xmark text-xs"></i>
                <span>Nyahpilih</span>
            </button>
            <form id="bulkDeleteForm" action="{{ route('users.multi-destroy') }}" method="POST" class="inline" onsubmit="return confirmBulkDelete();">
                @csrf
                @method('DELETE')
                <div id="bulkDeleteInputsContainer"></div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-lg shadow-rose-950/40 transition flex items-center gap-2">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                    <span>Padam Terpilih (Multi Delete)</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase text-[11px]">
                        <th class="px-4 py-3.5 w-10 text-center">
                            <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500 cursor-pointer" title="Pilih Semua Pengguna">
                        </th>
                        <th class="px-5 py-3.5">Pengguna &amp; Maklumat Asas</th>
                        <th class="px-4 py-3.5">No. Kad Pengenalan</th>
                        <th class="px-4 py-3.5">Peranan (Role)</th>
                        <th class="px-4 py-3.5">Jajahan</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="user-row hover:bg-slate-50/70 transition" data-user-id="{{ $u->id }}">
                            <td class="px-4 py-3.5 text-center">
                                @if(Auth::id() !== $u->id)
                                    <input type="checkbox" value="{{ $u->id }}" class="user-checkbox w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500 cursor-pointer">
                                @else
                                    <span title="Akaun anda sendiri (Dilindungi)" class="text-slate-300 cursor-not-allowed">
                                        <i class="fa-solid fa-lock text-xs"></i>
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-slate-700 text-xs shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 truncate">{{ $u->name }}</div>
                                        <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                            <span><i class="fa-solid fa-envelope mr-1 text-slate-400"></i>{{ $u->email }}</span>
                                            <span>&bull;</span>
                                            <span><i class="fa-solid fa-phone mr-1 text-slate-400"></i>{{ $u->phone }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 font-mono font-semibold text-slate-800">
                                {{ $u->ic_number ?? '-' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex flex-wrap gap-1.5 max-w-xs">
                                    @forelse($u->roles_data as $rd)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $rd['badge'] }}">
                                            <i class="fa-solid {{ $rd['icon'] }} text-[9px]"></i>
                                            <span>{{ $rd['label'] }}</span>
                                        </span>
                                    @empty
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border bg-slate-100 text-slate-700">
                                            {{ $u->role_label }}
                                        </span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-3.5 font-medium text-slate-700">
                                {{ $u->jajahan ?? 'Seluruh Kelantan' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <form action="{{ route('users.toggle-status', $u->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" @if(Auth::id() === $u->id) disabled @endif title="Klik untuk tukar status" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold transition {{ $u->status === 'Aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-300 hover:bg-rose-100' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $u->status === 'Aktif' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        <span>{{ $u->status ?? 'Aktif' }}</span>
                                    </button>
                                </form>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('users.show', $u->id) }}" class="p-1.5 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition" title="Lihat Profil & Aktiviti">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $u->id) }}" class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Kemaskini">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    @if(Auth::id() !== $u->id)
                                        <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Adakah anda pasti mahu memadam akaun pengguna {{ $u->name }}? Tindakan ini tidak boleh diundur.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Padam Pengguna">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-users-slash text-4xl text-slate-300 mb-3 block"></i>
                                <div class="font-bold text-sm text-slate-600">Tiada Pengguna Ditemui</div>
                                <p class="text-xs mt-1">Cuba ubah kata kunci carian atau tetapan tapisan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const bulkActionBar = document.getElementById('bulkActionBar');
    const selectedCountSpan = document.getElementById('selectedCount');
    const clearSelectionBtn = document.getElementById('clearSelectionBtn');
    const bulkDeleteInputsContainer = document.getElementById('bulkDeleteInputsContainer');

    function updateBulkState() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const count = checkedBoxes.length;

        selectedCountSpan.textContent = count;

        if (count > 0) {
            bulkActionBar.classList.remove('hidden');
        } else {
            bulkActionBar.classList.add('hidden');
        }

        // Update selectAll state
        if (userCheckboxes.length > 0) {
            selectAllCheckbox.checked = (count === userCheckboxes.length);
            selectAllCheckbox.indeterminate = (count > 0 && count < userCheckboxes.length);
        }

        // Highlight selected rows
        document.querySelectorAll('.user-row').forEach(row => {
            const cb = row.querySelector('.user-checkbox');
            if (cb && cb.checked) {
                row.classList.add('bg-amber-50/60');
            } else {
                row.classList.remove('bg-amber-50/60');
            }
        });

        // Populate hidden inputs for bulk form
        bulkDeleteInputsContainer.innerHTML = '';
        checkedBoxes.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            bulkDeleteInputsContainer.appendChild(input);
        });
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            userCheckboxes.forEach(cb => {
                cb.checked = selectAllCheckbox.checked;
            });
            updateBulkState();
        });
    }

    userCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkState);
    });

    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', function() {
            userCheckboxes.forEach(cb => {
                cb.checked = false;
            });
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
            updateBulkState();
        });
    }

    window.confirmBulkDelete = function() {
        const count = document.querySelectorAll('.user-checkbox:checked').length;
        if (count === 0) {
            alert('Sila pilih sekurang-kurangnya satu akaun pengguna untuk dipadam.');
            return false;
        }
        return confirm(`Adakah anda pasti mahu memadam ${count} akaun pengguna yang dipilih secara serentak?\n\nAMARAN: Tindakan ini tidak boleh diundur dan akan memadam rekod berkaitan pengguna.`);
    };
});
</script>
@endsection
