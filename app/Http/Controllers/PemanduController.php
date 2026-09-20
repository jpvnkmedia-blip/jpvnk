<?php

namespace App\Http\Controllers;

use App\Models\Pemandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemanduController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->canManageKenderaanFleet()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kenderaan dan Super Admin dibenarkan melihat dan menguruskan Maklumat Pemandu.');
        }

        $query = Pemandu::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jajahan')) {
            $query->where('jajahan_penempatan', $request->jajahan);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_kp', 'like', "%{$search}%")
                  ->orWhere('no_pekerja', 'like', "%{$search}%")
                  ->orWhere('no_telefon', 'like', "%{$search}%");
            });
        }

        $pemanduList = $query->orderBy('nama')->paginate(12);

        $totalPemandu = Pemandu::count();
        $totalAktif = Pemandu::where('status', 'Aktif')->count();
        $totalBertugas = Pemandu::where('status', 'Bertugas')->count();
        $totalCuti = Pemandu::where('status', 'Cuti')->count();

        return view('kenderaan.pemandu.index', compact(
            'pemanduList',
            'totalPemandu',
            'totalAktif',
            'totalBertugas',
            'totalCuti'
        ));
    }

    public function create()
    {
        if (!Auth::user()->canManageKenderaanFleet()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kenderaan dan Super Admin dibenarkan mendaftar Maklumat Pemandu.');
        }

        return redirect()->route('kenderaan.pemandu.index');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->canManageKenderaanFleet()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kenderaan dan Super Admin dibenarkan mendaftar Maklumat Pemandu.');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'no_kp' => 'required|string|max:20|unique:pemandu,no_kp',
            'no_pekerja' => 'nullable|string|max:30',
            'no_telefon' => 'required|string|max:20',
            'kelas_lesen' => 'required|string|max:50',
            'tarikh_tamat_lesen' => 'nullable|date',
            'jajahan_penempatan' => 'required|string|max:50',
            'status' => 'required|in:Aktif,Bertugas,Cuti,Tidak Aktif',
            'catatan' => 'nullable|string',
        ]);

        Pemandu::create($validated);

        return redirect()->route('kenderaan.pemandu.index')->with('success', 'Maklumat pemandu baharu berjaya didaftarkan.');
    }

    public function show($id)
    {
        if (!Auth::user()->canManageKenderaanFleet()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kenderaan dan Super Admin dibenarkan melihat Maklumat Pemandu.');
        }

        $pemandu = Pemandu::findOrFail($id);
        return view('kenderaan.pemandu.show', compact('pemandu'));
    }

    public function edit($id)
    {
        if (!Auth::user()->canManageKenderaanFleet()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kenderaan dan Super Admin dibenarkan mengemaskini Maklumat Pemandu.');
        }

        $pemandu = Pemandu::findOrFail($id);
        return view('kenderaan.pemandu.edit', compact('pemandu'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->canManageKenderaanFleet()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kenderaan dan Super Admin dibenarkan mengemaskini Maklumat Pemandu.');
        }

        $pemandu = Pemandu::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'no_kp' => 'required|string|max:20|unique:pemandu,no_kp,' . $pemandu->id,
            'no_pekerja' => 'nullable|string|max:30',
            'no_telefon' => 'required|string|max:20',
            'kelas_lesen' => 'required|string|max:50',
            'tarikh_tamat_lesen' => 'nullable|date',
            'jajahan_penempatan' => 'required|string|max:50',
            'status' => 'required|in:Aktif,Bertugas,Cuti,Tidak Aktif',
            'catatan' => 'nullable|string',
        ]);

        $pemandu->update($validated);

        return redirect()->route('kenderaan.pemandu.index')->with('success', 'Maklumat pemandu berjaya dikemaskini.');
    }

    public function destroy($id)
    {
        if (!Auth::user()->canManageKenderaanFleet()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kenderaan dan Super Admin dibenarkan memadam Maklumat Pemandu.');
        }

        $pemandu = Pemandu::findOrFail($id);
        $pemandu->delete();

        return redirect()->route('kenderaan.pemandu.index')->with('success', 'Rekod pemandu telah dipadam.');
    }
}
