<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pemunya;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Papar halaman Profil Pengguna
     */
    public function show()
    {
        $user = Auth::user();

        // Muatkan rekod-rekod berkaitan pengguna mengikut peranan
        $user->load([
            'pemunya.ternakan',
            'pawahPerjanjian',
            'ladangUnggas',
            'permohonanKursus.course',
            'temujanjiKlinik',
            'tempahanKenderaan.kenderaan',
            'permohonanInventori',
        ]);

        $kelantanData = config('kelantan.jajahan', []);
        $jajahanList = array_keys($kelantanData);

        return view('profile.show', compact('user', 'jajahanList', 'kelantanData'));
    }

    /**
     * Kemaskini maklumat peribadi pengguna
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'max:25'],
            'address' => ['required', 'string'],
            'jajahan' => ['required', 'string'],
        ], [
            'name.required' => 'Nama penuh wajib diisi.',
            'email.required' => 'Alamat emel wajib diisi.',
            'email.unique' => 'Alamat emel ini telah digunakan oleh akaun lain.',
            'phone.required' => 'No. Telefon wajib diisi.',
            'address.required' => 'Alamat kediaman / surat-menyurat wajib diisi.',
            'jajahan.required' => 'Sila pilih Jajahan.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->address = $validated['address'];
        $user->jajahan = $validated['jajahan'];
        $user->save();

        // Kemaskini rekod pemunya sekiranya penternak
        if ($user->pemunya) {
            $user->pemunya->update([
                'nama' => $user->name,
                'no_telefon' => $user->phone,
                'alamat' => $user->address,
                'jajahan' => $user->jajahan,
            ]);
        }

        // Jana notifikasi aktiviti
        UserNotification::send(
            $user->id,
            'Profil Dikemaskini',
            'Maklumat profil peribadi anda telah berjaya dikemaskini.',
            'profil',
            route('profile.show'),
            'fa-solid fa-user-pen',
            'blue'
        );

        return back()->with('success', 'Maklumat profil anda telah berjaya dikemaskini!');
    }

    /**
     * Tukar kata laluan pengguna
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.required' => 'Sila masukkan kata laluan semasa anda.',
            'password.required' => 'Sila masukkan kata laluan baharu.',
            'password.confirmed' => 'Pengesahan kata laluan baharu tidak sepadan.',
            'password.min' => 'Kata laluan baharu mestilah sekurang-kurangnya 6 aksara.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Kata laluan semasa yang dimasukkan adalah tidak tepat.',
            ])->with('error', 'Kata laluan semasa tidak tepat.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Jana notifikasi aktiviti keselamatan
        UserNotification::send(
            $user->id,
            'Kata Laluan Ditukar',
            'Kata laluan akaun anda telah berjaya dikemaskini. Jika ini bukan tindakan anda, sila hubungi pentadbir sistem dengan segera.',
            'profil',
            route('profile.show'),
            'fa-solid fa-key',
            'amber'
        );

        return back()->with('success', 'Kata laluan anda telah berjaya ditukar!');
    }
}
