<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/login-aslab', [AuthController::class, 'showAslabLogin'])->name('login.aslab');
    Route::post('/login-aslab', [AuthController::class, 'aslabLogin'])->name('login.aslab.post');
    Route::get('/login-praktikan', [AuthController::class, 'showPraktikanLogin'])->name('login.praktikan');
    Route::post('/login-praktikan', [AuthController::class, 'praktikanLogin'])->name('login.praktikan.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        if (Auth::user()->role && Auth::user()->role->name === 'Praktikan') {
            return redirect()->route('praktikan.dashboard');
        }
        return redirect()->route('admin.dashboard');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Role Management
        Route::resource('role', RoleController::class);
        Route::patch('role/{id}/toggle-status', [RoleController::class, 'toggleStatus'])->name('role.toggle-status');

        // User Management
        Route::resource('user', UserController::class);
        Route::patch('user/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('user.toggle-status');

        // Aslab Management
        Route::resource('aslab', \App\Http\Controllers\AslabController::class);
        Route::patch('aslab/{id}/toggle-status', [\App\Http\Controllers\AslabController::class, 'toggleStatus'])->name('aslab.toggle-status');

        // Praktikum Management
        Route::resource('praktikum', \App\Http\Controllers\PraktikumController::class);
        Route::patch('praktikum/{id}/toggle-status', [\App\Http\Controllers\PraktikumController::class, 'toggleStatus'])->name('praktikum.toggle-status');
        Route::post('praktikum/{praktikum_id}/sesi', [\App\Http\Controllers\SesiPraktikumController::class, 'store'])->name('praktikum.sesi.store');
        Route::delete('praktikum/sesi/{id}', [\App\Http\Controllers\SesiPraktikumController::class, 'destroy'])->name('praktikum.sesi.destroy');

        // Pendaftaran Management (Admin)
        Route::get('/pendaftaran', [\App\Http\Controllers\Admin\PendaftaranController::class, 'index'])->name('pendaftaran.index');
        Route::get('/pendaftaran/{id}', [\App\Http\Controllers\Admin\PendaftaranController::class, 'show'])->name('pendaftaran.show');
        Route::patch('/pendaftaran/{id}/status', [\App\Http\Controllers\Admin\PendaftaranController::class, 'updateStatus'])->name('pendaftaran.update-status');

        // Praktikan Management
        Route::resource('praktikan', \App\Http\Controllers\PraktikanController::class);
        Route::patch('praktikan/{id}/toggle-status', [\App\Http\Controllers\PraktikanController::class, 'toggleStatus'])->name('praktikan.toggle-status');

        // User Actions
        Route::get('/kaprodi', fn() => 'Kaprodi Index')->name('kaprodi.index');
        Route::get('/prodi', fn() => 'Prodi Index')->name('prodi.index');
        Route::get('/profile/edit', fn() => 'Profile Edit')->name('profile.edit');

        Route::prefix('mahasiswa-cuti')->name('mahasiswa-cuti.')->group(function () {
            Route::get('/dashboard', fn() => 'Cuti Dashboard')->name('dashboard');
            Route::get('/', fn() => 'Cuti Index')->name('index');
        });
        Route::get('/periode', fn() => 'Periode Index')->name('periode.index');
        Route::prefix('fasilitas')->name('fasilitas.')->group(function () {
            Route::get('/dashboard', fn() => 'Fasilitas Dashboard')->name('dashboard');
        });
        Route::get('/gedung', fn() => 'Gedung Index')->name('gedung.index');
        Route::get('/kelas', fn() => 'Kelas Index')->name('kelas.index');
        Route::get('/support', fn() => 'Support Index')->name('support.index');
        Route::get('/laboratorium', fn() => 'Laboratorium Index')->name('laboratorium.index');
        Route::prefix('peminjaman-ruangan')->name('peminjaman-ruangan.')->group(function () {
            Route::get('/monitoring', fn() => 'Monitoring')->name('monitoring');
            Route::get('/', fn() => 'Peminjaman Index')->name('index');
        });
        Route::get('/pengajuan-ruangan', fn() => 'Pengajuan Index')->name('pengajuan-ruangan.index');
        Route::get('/legalisir', fn() => 'Legalisir Index')->name('legalisir.index');
        Route::get('/pengumuman', fn() => 'Pengumuman Index')->name('pengumuman.index');
    });

    // Praktikan Section
    Route::prefix('praktikan')->name('praktikan.')->middleware(['role.praktikan'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Praktikan\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/daftar-praktikum/{praktikum_id}', [\App\Http\Controllers\Praktikan\PendaftaranController::class, 'create'])->name('pendaftaran.create');
        Route::post('/daftar-praktikum', [\App\Http\Controllers\Praktikan\PendaftaranController::class, 'store'])->name('pendaftaran.store');
        Route::get('/riwayat-pendaftaran', [\App\Http\Controllers\Praktikan\PendaftaranController::class, 'index'])->name('pendaftaran.index');
    });
});

// Notifications placeholders
Route::name('notifications.')->group(function () {
    Route::post('/notifications/read-all', fn() => back())->name('readAll');
    Route::get('/notifications/{id}/go', fn() => back())->name('go');
    Route::get('/notifications', fn() => 'Notifications Index')->name('index');
});

Route::name('users.')->group(function () {
    Route::get('/user/profile/edit', fn() => 'User Profile Edit')->name('profile.edit');
});
