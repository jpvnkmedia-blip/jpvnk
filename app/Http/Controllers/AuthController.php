<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pemunya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'ic_number' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'ic_number.required' => 'Sila masukkan No. Kad Pengenalan atau Emel anda.',
            'password.required' => 'Sila masukkan kata laluan anda.',
        ]);

        $loginInput = trim($credentials['ic_number']);
        $cleanIc = str_replace(['-', ' '], '', $loginInput);

        // Cari pengguna mengikut No. Kad Pengenalan (dibersihkan atau asal) atau Emel
        $user = User::where('ic_number', $cleanIc)
            ->orWhere('ic_number', $loginInput)
            ->orWhere('email', $loginInput)
            ->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Selamat kembali, ' . $user->name);
        }

        return back()->withErrors([
            'ic_number' => 'No. Kad Pengenalan / Emel atau kata laluan yang dimasukkan tidak tepat.',
        ])->onlyInput('ic_number');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $captcha = (string) rand(10000, 99999);
        session(['register_captcha' => $captcha]);

        return view('auth.register', compact('captcha'));
    }

    public function register(Request $request)
    {
        $cleanIc = str_replace(['-', ' '], '', $request->input('ic_number', ''));
        $request->merge(['ic_number' => $cleanIc]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'ic_number' => ['required', 'string', 'max:20', 'unique:users'],
            'nama_syarikat' => ['nullable', 'string', 'max:255'],
            'no_ssm' => ['nullable', 'string', 'max:50'],
            'bentuk_perniagaan' => ['nullable', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:25'],
            'fax' => ['nullable', 'string', 'max:25'],
            'address' => ['required', 'string'],
            'poskod' => ['nullable', 'string', 'max:10'],
            'negeri' => ['nullable', 'string', 'max:50'],
            'jajahan' => ['nullable', 'string'],
            'role' => ['nullable', 'in:penternak,usahawan,orang_awam'],
            'password' => ['nullable', 'confirmed'],
            'captcha' => ['nullable', 'string'],
        ], [
            'name.required' => 'Sila masukkan nama penuh anda.',
            'email.required' => 'Sila masukkan alamat emel yang sah.',
            'email.unique' => 'Alamat emel ini telah digunakan.',
            'ic_number.required' => 'Sila masukkan No. Kad Pengenalan anda.',
            'ic_number.unique' => 'No. Kad Pengenalan ini telah didaftarkan.',
            'phone.required' => 'Sila masukkan nombor telefon bimbit.',
            'address.required' => 'Sila masukkan alamat kediaman / surat-menyurat.',
            'password.confirmed' => 'Pengesahan kata laluan tidak sepadan.',
        ]);

        // Semakan Captcha jika disediakan dalam sesi
        $expectedCaptcha = session('register_captcha');
        if ($expectedCaptcha && !empty($request->captcha) && trim($request->captcha) !== (string)$expectedCaptcha) {
            return back()->withInput()->withErrors([
                'captcha' => 'Kod keselamatan (Captcha) tidak tepat. Sila cuba lagi.',
            ]);
        }

        $finalPassword = !empty($validated['password'])
            ? $validated['password']
            : User::generateDefaultPassword($validated['ic_number']);

        $role = $validated['role'] ?? (!empty($validated['nama_syarikat']) ? 'usahawan' : 'penternak');
        $jajahan = $validated['jajahan'] ?? 'Kota Bharu';

        $user = User::create([
            'name' => $validated['name'],
            'nama_syarikat' => $validated['nama_syarikat'] ?? null,
            'no_ssm' => $validated['no_ssm'] ?? null,
            'bentuk_perniagaan' => $validated['bentuk_perniagaan'] ?? null,
            'email' => $validated['email'],
            'ic_number' => $validated['ic_number'],
            'phone' => $validated['phone'],
            'fax' => $validated['fax'] ?? null,
            'address' => $validated['address'],
            'poskod' => $validated['poskod'] ?? null,
            'negeri' => $validated['negeri'] ?? 'Kelantan',
            'jajahan' => $jajahan,
            'role' => $role,
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => Hash::make($finalPassword),
        ]);

        // Jika peranan penternak, cipta profil pemunya ternakan sekali
        if ($role === 'penternak') {
            Pemunya::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'no_kp' => $user->ic_number,
                'no_telefon' => $user->phone,
                'alamat' => $user->address,
                'jajahan' => $user->jajahan,
                'status' => 'Aktif',
            ]);
        }

        \App\Models\UserNotification::send(
            $user->id,
            'Pendaftaran Akaun Berjaya',
            'Selamat datang ke Sistem Veterinar Bersepadu JPVNK (e-Unggas & EPTR)! Akaun anda telah sedia digunakan.',
            'auth',
            route('dashboard'),
            'fa-solid fa-circle-check',
            'emerald'
        );

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Pendaftaran akaun berjaya! Selamat datang ke Sistem Pengurusan JPVNK.');
    }

    // Google SSO Flow & Simulator
    public function googleLogin(Request $request)
    {
        // Menyediakan portal Google Sign-In
        return view('auth.google');
    }

    public function googleCallback(Request $request)
    {
        $email = $request->input('email', 'penternak.google@gmail.com');
        $name = $request->input('name', 'Penternak Google User');
        $role = $request->input('role', 'penternak');

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'ic_number' => '88050503' . rand(1000, 9999),
                'phone' => '018-9' . rand(100000, 999999),
                'address' => 'Kampung Wakaf Bharu, Tumpat, Kelantan',
                'jajahan' => 'Tumpat',
                'role' => $role,
                'auth_provider' => 'google',
                'status' => 'Aktif',
                'password' => Hash::make('google_oauth_pass_' . rand(1000, 9999)),
            ]
        );

        if ($user->role === 'penternak' && !$user->pemunya) {
            Pemunya::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'no_kp' => $user->ic_number,
                'no_telefon' => $user->phone,
                'alamat' => $user->address,
                'jajahan' => $user->jajahan,
                'status' => 'Aktif',
            ]);
        }

        Auth::login($user);
        return redirect()->route('dashboard')->with('success', 'Log masuk Google Account berjaya!');
    }

    // MyDigital ID SSO Flow & Simulator
    public function myDigitalIdLogin(Request $request)
    {
        return view('auth.mydigitalid');
    }

    public function myDigitalIdVerify(Request $request)
    {
        $ic = $request->input('ic_number', '900729035411');
        $name = $request->input('name', 'Warga Digital Kelantan');
        $role = $request->input('role', 'penternak');

        $user = User::firstOrCreate(
            ['ic_number' => $ic],
            [
                'name' => $name,
                'email' => 'digital.' . $ic . '@mydigitalid.gov.my',
                'phone' => '019-9' . rand(100000, 999999),
                'address' => 'Bandar Baru Kubang Kerian, 16150 Kota Bharu, Kelantan',
                'jajahan' => 'Kota Bharu',
                'role' => $role,
                'auth_provider' => 'mydigital_id',
                'status' => 'Aktif',
                'password' => Hash::make('mydigital_id_' . $ic),
            ]
        );

        if ($user->role === 'penternak' && !$user->pemunya) {
            Pemunya::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'no_kp' => $user->ic_number,
                'no_telefon' => $user->phone,
                'alamat' => $user->address,
                'jajahan' => $user->jajahan,
                'status' => 'Aktif',
            ]);
        }

        Auth::login($user);
        return redirect()->route('dashboard')->with('success', 'Pengesahan MyDigital ID berjaya! Log masuk disahkan.');
    }

    // Fast Role Switcher (Untuk Demo & Ujian Pantas)
    public function switchRole(Request $request)
    {
        $user = null;

        if ($request->filled('user_id')) {
            $user = User::find($request->user_id);
        } elseif ($request->filled('role')) {
            $query = User::where('role', $request->role);
            if ($request->filled('jajahan')) {
                $query->where('jajahan', $request->jajahan);
            }
            $user = $query->first();
        }

        if (!$user) {
            return back()->with('error', 'Pengguna untuk peranan tersebut tidak dijumpai.');
        }

        Auth::login($user);
        return redirect()->route('dashboard')->with('success', 'Telah beralih ke peranan: ' . $user->role_label . ' (' . $user->name . ')');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berjaya log keluar.');
    }
}
