<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (!$user->role) {
                Auth::logout();
                return redirect()->route('login')->withErrors(['username' => 'Akun Anda tidak memiliki peran.']);
            }
            if ($user->role->name === 'Praktikan') {
                return redirect()->route('praktikan.dashboard');
            } elseif ($user->role->name === 'Aslab') {
                return redirect()->route('aslab.dashboard');
            }
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        Log::info('Login attempt started', ['username' => $request->username, 'ip' => $request->ip()]);

        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'cf-turnstile-response' => 'required',
        ]);

        Log::info('Basic validation passed, verifying Turnstile');

        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret'   => config('services.turnstile.secret'),
            'response' => $request->input('cf-turnstile-response'),
            'remoteip' => $request->ip(),
        ]);

        $outcome = $response->json();

        if (!$outcome['success']) {
            Log::error('Turnstile verification failed', ['error-codes' => $outcome['error-codes'] ?? 'unknown']);
            return back()->withErrors([
                'username' => 'Verifikasi keamanan gagal. Silakan coba lagi.',
            ])->withInput();
        }

        Log::info('Turnstile verified, attempting authentication');

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password], $request->remember)) {
            $user = Auth::user();
            $request->session()->regenerate();
            
            Log::info('Auth successful', ['user_id' => $user->id, 'role' => $user->role->name ?? 'no role']);

            if (!$user->status) {
                Log::warning('User account disabled', ['user_id' => $user->id]);
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Akun Anda sedang dinonaktifkan.',
                ]);
            }

            if ($user->role->name === 'Praktikan') {
                return redirect()->route('praktikan.dashboard')->with('login_success', 'Selamat datang kembali, ' . $user->name . '!');
            } elseif ($user->role->name === 'Aslab') {
                return redirect()->route('aslab.dashboard')->with('login_success', 'Selamat datang kembali aslab, ' . $user->name . '!');
            }

            return redirect()->route('admin.dashboard')->with('login_success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        Log::warning('Auth failed: Invalid credentials', ['username' => $request->username]);

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function showAslabLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role && $user->role->name === 'Aslab') {
                return redirect()->route('aslab.dashboard');
            }
            if ($user->role && $user->role->name === 'Praktikan') {
                return redirect()->route('praktikan.dashboard');
            }
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login-aslab');
    }

    public function aslabLogin(Request $request)
    {
        $request->validate([
            'npm' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['username' => $request->npm, 'password' => $request->password], $request->remember)) {
            $request->session()->regenerate();
            $request->session()->forget('url.intended'); // Hapus intended URL untuk cegah open redirect

            if (!Auth::user()->status) {
                Auth::logout();
                return back()->withErrors([
                    'npm' => 'Akun Anda sedang dinonaktifkan.',
                ]);
            }

            // Validasi Role Aslab
            if (Auth::user()->role->name !== 'Aslab') {
                Auth::logout();
                return back()->withErrors([
                    'npm' => 'Halaman ini khusus untuk Asisten Laboratorium.',
                ]);
            }

            return redirect()->route('aslab.dashboard')->with('login_success', 'Selamat datang kembali aslab, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'npm' => 'NPM atau password salah.',
        ])->onlyInput('npm');
    }

    public function showPraktikanLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role && $user->role->name === 'Praktikan') {
                return redirect()->route('praktikan.dashboard');
            }

            // If they have another role, take them to their dashboard
            if ($user->role) {
                return redirect()->route('admin.dashboard');
            }

            // If logged in but no role, force logout
            Auth::logout();
        }
        return view('auth.login-praktikan');
    }

    public function praktikanLogin(Request $request)
    {
        $request->validate([
            'npm' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['username' => $request->npm, 'password' => $request->password], $request->remember)) {
            $request->session()->regenerate();
            $request->session()->forget('url.intended'); // Hapus intended URL untuk cegah open redirect

            if (!Auth::user()->status) {
                Auth::logout();
                return back()->withErrors([
                    'npm' => 'Akun Anda sedang dinonaktifkan.',
                ]);
            }

            // Validasi Role Praktikan
            if (Auth::user()->role->name !== 'Praktikan') {
                Auth::logout();
                return back()->withErrors([
                    'npm' => 'Halaman ini khusus untuk Praktikan.',
                ]);
            }

            return redirect()->route('praktikan.dashboard')->with('login_success', 'Selamat datang praktikan, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'npm' => 'NPM atau password salah.',
        ])->onlyInput('npm');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'npm' => [
                'required',
                'string',
                'unique:users,username',
                'unique:praktikans,npm',
                'unique:aslabs,npm',
                'regex:/^\d{2}\.\d{4}\.\d{1}\.\d{5}$/'
            ],
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'npm.regex' => 'Format NPM tidak valid. Contoh: 06.2025.1.01111'
        ]);

        $role = \App\Models\Role::where('name', 'Praktikan')->first();
        if (!$role) {
            return back()->withErrors(['error' => 'Sistem Belum Siap: Role Praktikan tidak ditemukan.']);
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $user = \App\Models\User::create([
                'username' => $request->npm,
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'status' => true,
            ]);
            $user->role_id = $role->id;
            $user->save();

            \App\Models\Praktikan::create([
                'user_id' => $user->id,
                'npm' => $request->npm,
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('login.praktikan')->with('success', 'Pendaftaran berhasil. Silakan login menggunakan NPM Anda.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Pendaftaran gagal: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Pendaftaran gagal. Silakan coba lagi atau hubungi admin.'])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('logout_success', 'Anda telah berhasil logout.');
    }
    public function checkNpm(Request $request)
    {
        $npm = $request->query('npm');
        if (!$npm) {
            return response()->json(['exists' => false]);
        }

        $exists = \App\Models\User::where('username', $npm)->exists() ||
            \App\Models\Praktikan::where('npm', $npm)->exists() ||
            \App\Models\Aslab::where('npm', $npm)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function dashboardRedirect()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (!$user->role) {
                Auth::logout();
                return redirect()->route('home');
            }

            if ($user->role->name === 'Praktikan') {
                return redirect()->route('praktikan.dashboard');
            }

            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('home');
    }
}
