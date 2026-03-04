<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Role Management
        Route::resource('role', RoleController::class);
        Route::patch('role/{id}/toggle-status', [RoleController::class, 'toggleStatus'])->name('role.toggle-status');

        // User Management
        Route::resource('user', UserController::class);
        Route::patch('user/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('user.toggle-status');

        // Placeholders for other modules
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
