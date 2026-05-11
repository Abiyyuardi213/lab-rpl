<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\PresensiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/bahlil', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/bahlil', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:10,1');
    Route::get('/login-aslab', [AuthController::class, 'showAslabLogin'])->name('login.aslab');
    Route::post('/login-aslab', [AuthController::class, 'aslabLogin'])->name('login.aslab.post')->middleware('throttle:10,1');
    Route::get('/login-praktikan', [AuthController::class, 'showPraktikanLogin'])->name('login.praktikan');
    Route::post('/login-praktikan', [AuthController::class, 'praktikanLogin'])->name('login.praktikan.post')->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post')->middleware('throttle:5,1');
});

// Password Reset Routes (Available for all to prevent route naming issues)
Route::get('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email')->middleware('throttle:5,1');
Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])
    ->name('password.update')->middleware('throttle:5,1');

Route::get('/sitemap.xml', function () {
    $kegiatans = \App\Models\Kegiatan::where('is_active', true)->latest()->get();
    $pengumumans = \App\Models\Pengumuman::where('is_active', true)->latest()->get();
    return response()->view('sitemap', compact('kegiatans', 'pengumumans'))->header('Content-Type', 'text/xml');
});

Route::get('/', [WelcomeController::class, 'index'])->name('home');
Route::get('/tentang', [WelcomeController::class, 'about'])->name('about');
Route::get('/praktikum', [WelcomeController::class, 'praktikum'])->name('praktikum.public');
Route::get('/aslab', [WelcomeController::class, 'aslab'])->name('aslab.public');
Route::get('/struktur-organisasi', [WelcomeController::class, 'organization'])->name('organization');
Route::get('/pengumuman', [WelcomeController::class, 'pengumuman'])->name('pengumuman.public');
Route::get('/pengumuman/{slug}', [WelcomeController::class, 'pengumumanDetail'])->name('pengumuman.show');
Route::get('/kegiatan', [WelcomeController::class, 'kegiatan'])->name('kegiatan.public');
Route::get('/kegiatan/{slug}', [WelcomeController::class, 'kegiatanDetail'])->name('kegiatan.show');
Route::get('/p/{token}', [PresensiController::class, 'publicVerify'])->name('presensi.public-verify');
Route::get('/s/{token}', [PresensiController::class, 'scanLanding'])->name('presensi.scan-landing');
Route::get('/impostor', function () {
    return view('impostor');
})->name('impostor');

// Dashboard redirection for logged in users
Route::get('/home', [AuthController::class, 'dashboardRedirect'])->name('dashboard.redirect');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/leave-impersonation', [\App\Http\Controllers\PraktikanController::class, 'leaveImpersonation'])->name('impersonation.leave');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/fetch', [\App\Http\Controllers\NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::get('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    Route::prefix('admin')->name('admin.')->middleware(['role.admin'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Role & User Management (Super Admin ONLY)
        Route::middleware('role.superadmin')->group(function () {
            Route::resource('role', RoleController::class);
            Route::patch('role/{id}/toggle-status', [RoleController::class, 'toggleStatus'])->name('role.toggle-status');
            Route::resource('user', UserController::class);
            Route::patch('user/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('user.toggle-status');
        });

        // Aslab Management
        Route::resource('aslab', \App\Http\Controllers\AslabController::class);
        Route::patch('aslab/{id}/toggle-status', [\App\Http\Controllers\AslabController::class, 'toggleStatus'])->name('aslab.toggle-status');

        // Praktikum Management
        Route::resource('praktikum', \App\Http\Controllers\PraktikumController::class);
        Route::patch('praktikum/{id}/toggle-status', [\App\Http\Controllers\PraktikumController::class, 'toggleStatus'])->name('praktikum.toggle-status');
        Route::get('praktikum/{id}/students', [\App\Http\Controllers\PraktikumController::class, 'students'])->name('praktikum.students');
        Route::get('praktikum/{praktikum_id}/sesi', fn($id) => redirect()->route('admin.praktikum.show', $id));
        Route::post('praktikum/{praktikum_id}/sesi', [\App\Http\Controllers\SesiPraktikumController::class, 'store'])->name('praktikum.sesi.store');
        Route::patch('praktikum/sesi/{id}', [\App\Http\Controllers\SesiPraktikumController::class, 'update'])->name('praktikum.sesi.update');
        Route::delete('praktikum/sesi/{id}', [\App\Http\Controllers\SesiPraktikumController::class, 'destroy'])->name('praktikum.sesi.destroy');
        Route::get('praktikum/{praktikum_id}/aslab', fn($id) => redirect()->route('admin.praktikum.show', $id));
        Route::post('praktikum/{praktikum_id}/aslab', [\App\Http\Controllers\PraktikumController::class, 'storeAslab'])->name('praktikum.aslab.store');
        Route::delete('praktikum/aslab/{id}', [\App\Http\Controllers\PraktikumController::class, 'destroyAslab'])->name('praktikum.aslab.destroy');
        Route::patch('praktikum/pendaftaran/{pendaftaran_id}/assign-aslab', [\App\Http\Controllers\PraktikumController::class, 'assignStudentToAslab'])->name('praktikum.pendaftaran.assign-aslab');
        Route::post('praktikum/{praktikum_id}/auto-assign-aslab', [\App\Http\Controllers\PraktikumController::class, 'autoAssignAslab'])->name('praktikum.auto-assign-aslab');
        Route::post('praktikum/{praktikum_id}/bulk-assign-aslab', [\App\Http\Controllers\PraktikumController::class, 'bulkAssignAslab'])->name('praktikum.bulk-assign-aslab');
        Route::get('praktikum/{id}/export-students', [\App\Http\Controllers\PraktikumController::class, 'exportStudents'])->name('praktikum.export-students');
        Route::get('praktikum/{id}/download-template', [\App\Http\Controllers\PraktikumController::class, 'downloadTemplate'])->name('praktikum.download-template');
        Route::post('praktikum/{id}/import-students/preview', [\App\Http\Controllers\PraktikumController::class, 'previewImport'])->name('praktikum.import-preview');
        Route::post('praktikum/{id}/import-students/confirm', [\App\Http\Controllers\PraktikumController::class, 'confirmImport'])->name('praktikum.import-confirm');
        Route::post('praktikum/{id}/import-students', [\App\Http\Controllers\PraktikumController::class, 'importStudents'])->name('praktikum.import-students');
        Route::patch('praktikum/pendaftaran/{pendaftaran_id}/change-session', [\App\Http\Controllers\PraktikumController::class, 'changeStudentSession'])->name('praktikum.pendaftaran.change-session');
        // Jadwal Praktikum
        Route::post('praktikum/{praktikum_id}/jadwal', [\App\Http\Controllers\PraktikumController::class, 'storeJadwal'])->name('praktikum.jadwal.store');
        Route::delete('praktikum/jadwal/{id}', [\App\Http\Controllers\PraktikumController::class, 'destroyJadwal'])->name('praktikum.jadwal.destroy');
        Route::resource('jadwal-praktikum', \App\Http\Controllers\JadwalPraktikumController::class)->except(['create', 'edit', 'show']);

        // Pendaftaran Management (Admin)
        Route::get('/pendaftaran', [\App\Http\Controllers\Admin\PendaftaranController::class, 'index'])->name('pendaftaran.index');
        Route::get('/pendaftaran/{id}', [\App\Http\Controllers\Admin\PendaftaranController::class, 'show'])->name('pendaftaran.show');
        Route::patch('/pendaftaran/{id}/status', [\App\Http\Controllers\Admin\PendaftaranController::class, 'updateStatus'])->name('pendaftaran.update-status');
        Route::delete('/pendaftaran/{id}', [\App\Http\Controllers\Admin\PendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');

        // Profile Management (Admin)
        Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

        // Praktikan Management
        Route::resource('praktikan', \App\Http\Controllers\PraktikanController::class);
        Route::patch('praktikan/{id}/toggle-status', [\App\Http\Controllers\PraktikanController::class, 'toggleStatus'])->name('praktikan.toggle-status');
        Route::post('praktikan/{id}/impersonate', [\App\Http\Controllers\PraktikanController::class, 'impersonate'])->name('praktikan.impersonate');

        // User Actions
        Route::get('/kaprodi', fn() => 'Kaprodi Index')->name('kaprodi.index');
        Route::get('/prodi', fn() => 'Prodi Index')->name('prodi.index');

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
        Route::patch('digit-npm/{digitNpm}/toggle-status', [\App\Http\Controllers\Admin\DigitNpmController::class, 'toggleStatus'])->name('digit-npm.toggle-status');
        Route::resource('digit-npm', \App\Http\Controllers\Admin\DigitNpmController::class)->except(['create', 'show', 'edit']);
        Route::patch('penugasan/praktikan/{pendaftaran}/soal', [\App\Http\Controllers\Admin\PenugasanController::class, 'updateStudentAssignment'])->name('penugasan.praktikan-soal.update');
        Route::post('penugasan/bulk', [\App\Http\Controllers\Admin\PenugasanController::class, 'bulkStore'])->name('penugasan.bulk-store');
        Route::resource('penugasan', \App\Http\Controllers\Admin\PenugasanController::class);
        Route::get('/support', fn() => 'Support Index')->name('support.index');
        Route::get('/laboratorium', fn() => 'Laboratorium Index')->name('laboratorium.index');
        Route::prefix('peminjaman-ruangan')->name('peminjaman-ruangan.')->group(function () {
            Route::get('/monitoring', fn() => 'Monitoring')->name('monitoring');
            Route::get('/', fn() => 'Peminjaman Index')->name('index');
        });
        Route::get('/pengajuan-ruangan', fn() => 'Pengajuan Index')->name('pengajuan-ruangan.index');
        Route::get('/legalisir', fn() => 'Legalisir Index')->name('legalisir.index');
        Route::resource('pengumuman', \App\Http\Controllers\Admin\PengumumanController::class);
        Route::patch('pengumuman/{pengumuman}/toggle-status', [\App\Http\Controllers\Admin\PengumumanController::class, 'toggleStatus'])->name('pengumuman.toggle-status');
        Route::resource('presensi', \App\Http\Controllers\Admin\PresensiController::class)->only(['index', 'destroy']);
        Route::resource('kegiatan', \App\Http\Controllers\Admin\KegiatanController::class);
        Route::patch('kegiatan/{kegiatan}/toggle-status', [\App\Http\Controllers\Admin\KegiatanController::class, 'toggleStatus'])->name('kegiatan.toggle-status');
        
        // Activity Log
        Route::get('logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('logs/{id}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('logs.show');

        // Ratings (Admin)
        Route::get('ratings', [\App\Http\Controllers\Admin\RatingController::class, 'index'])->name('ratings.index');

        // Penilaian Praktikum (Admin)
        Route::get('/penilaian', [\App\Http\Controllers\Admin\PenilaianController::class, 'index'])->name('penilaian.index');
        Route::get('/penilaian/praktikum/{id}', [\App\Http\Controllers\Admin\PenilaianController::class, 'showPraktikum'])->name('penilaian.praktikum');
        Route::get('/penilaian/jadwal/{id}', [\App\Http\Controllers\Admin\PenilaianController::class, 'showJadwal'])->name('penilaian.jadwal');
        Route::post('/penilaian', [\App\Http\Controllers\Admin\PenilaianController::class, 'store'])->name('penilaian.store');

        // Recruitment Management
        Route::resource('recruitment', \App\Http\Controllers\Admin\RecruitmentController::class);
        Route::patch('recruitment/application/{application}/status', [\App\Http\Controllers\Admin\RecruitmentController::class, 'updateApplicationStatus'])->name('recruitment.application.status');
        Route::post('recruitment/{recruitment}/validate-ipk', [\App\Http\Controllers\Admin\RecruitmentController::class, 'validateIpk'])->name('recruitment.validate-ipk');
        Route::post('recruitment/{recruitment}/schedule', [\App\Http\Controllers\Admin\RecruitmentController::class, 'storeSchedule'])->name('recruitment.schedule.store');
        Route::delete('recruitment/schedule/{schedule}', [\App\Http\Controllers\Admin\RecruitmentController::class, 'destroySchedule'])->name('recruitment.schedule.destroy');
        Route::post('recruitment/schedule/{schedule}/assign', [\App\Http\Controllers\Admin\RecruitmentController::class, 'assignApplicants'])->name('recruitment.schedule.assign');

        // Broadcast Notification
        Route::get('notifications/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('notifications.create');
        Route::post('notifications/send', [\App\Http\Controllers\Admin\NotificationController::class, 'send'])->name('notifications.send');
    });

    // Common Presensi Routes (Admin & Aslab)
    Route::get('/presensi/generate-jadwal-qr/{jadwal_id}', [PresensiController::class, 'generateJadwalQR'])->name('presensi.generate-jadwal-qr');
    Route::post('/presensi/manual', [PresensiController::class, 'storeManual'])->name('presensi.manual');
    Route::delete('/presensi/{id}', [PresensiController::class, 'destroy'])->name('presensi.destroy');
    Route::get('/presensi/download-jadwal-pdf/{jadwal_id}', [PresensiController::class, 'downloadJadwalPDF'])->name('presensi.download-jadwal-pdf');

    // Aslab Section
    Route::prefix('aslab')->name('aslab.')->middleware(['role.aslab'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Aslab\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/pendaftaran', [\App\Http\Controllers\Aslab\PendaftaranController::class, 'index'])->name('pendaftaran.index');
        Route::post('tugas/direct', [\App\Http\Controllers\Aslab\TugasController::class, 'storeDirect'])->name('tugas.store-direct');
        Route::resource('tugas', \App\Http\Controllers\Aslab\TugasController::class);

        // Portfolio Management
        Route::get('/portfolio/edit', [\App\Http\Controllers\Aslab\PortfolioController::class, 'edit'])->name('portfolio.edit');
        Route::patch('/portfolio/update', [\App\Http\Controllers\Aslab\PortfolioController::class, 'update'])->name('portfolio.update');

        // Ratings (Aslab)
        Route::get('ratings', [\App\Http\Controllers\Aslab\RatingController::class, 'index'])->name('ratings.index');

        // Presensi (Aslab)
        Route::get('/presensi/scan', [PresensiController::class, 'scan'])->name('presensi.scan');
        Route::post('/presensi/check-in', [PresensiController::class, 'checkIn'])->name('presensi.check-in');

        Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

        // Penugasan
        Route::resource('penugasan', \App\Http\Controllers\Aslab\PenugasanController::class);

        // Penilaian Live
        Route::get('/penilaian', [\App\Http\Controllers\Aslab\PenilaianController::class, 'index'])->name('penilaian.index');
        Route::get('/penilaian/{jadwal_id}', [\App\Http\Controllers\Aslab\PenilaianController::class, 'show'])->name('penilaian.show');
        Route::post('/penilaian', [\App\Http\Controllers\Aslab\PenilaianController::class, 'store'])->name('penilaian.store');
    });

    // Praktikan Section
    Route::prefix('praktikan')->name('praktikan.')->group(function () {
        Route::middleware(['role.praktikan'])->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Praktikan\DashboardController::class, 'index'])->name('dashboard');
            Route::get('/daftar-praktikum/{praktikum_id}', [\App\Http\Controllers\Praktikan\PendaftaranController::class, 'create'])->name('pendaftaran.create');
            Route::post('/daftar-praktikum', [\App\Http\Controllers\Praktikan\PendaftaranController::class, 'store'])->name('pendaftaran.store');
            Route::get('/riwayat-pendaftaran', [\App\Http\Controllers\Praktikan\PendaftaranController::class, 'index'])->name('pendaftaran.index');
            Route::get('/pendaftaran/{id}/progress', [\App\Http\Controllers\Praktikan\PendaftaranController::class, 'progress'])->name('pendaftaran.progress');
            Route::post('/tugas/{tugas_id}/submit', [\App\Http\Controllers\Praktikan\PendaftaranController::class, 'submitTugas'])->name('pendaftaran.submit-tugas');
            Route::post('/pendaftaran/{id}/rating', [\App\Http\Controllers\Praktikan\PendaftaranController::class, 'submitRating'])->name('pendaftaran.submit-rating');

            // Presensi (Praktikan)
            Route::get('/presensi/generate-qr/{jadwal_id}', [PresensiController::class, 'generateQR'])->name('presensi.generate-qr');
            Route::get('/presensi/check-status/{jadwal_id}', [PresensiController::class, 'checkStatus'])->name('presensi.check-status');
            Route::get('/presensi/scan-scanner', [PresensiController::class, 'showScanner'])->name('presensi.scan-view');
            Route::get('/presensi/scan-jadwal/{token}', [PresensiController::class, 'scanJadwal'])->name('presensi.scan-jadwal');

            // Profile Management (Praktikan)
            Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

            // Penugasan
            Route::get('/penugasan', [\App\Http\Controllers\Praktikan\PenugasanController::class, 'index'])->name('penugasan.index');
            Route::get('/penugasan/{id}', [\App\Http\Controllers\Praktikan\PenugasanController::class, 'show'])->name('penugasan.show');
            Route::get('/penugasan/{id}/download', [\App\Http\Controllers\Praktikan\PenugasanController::class, 'download'])->name('penugasan.download');
            // Recruitment
            Route::get('/recruitment', [\App\Http\Controllers\Praktikan\RecruitmentController::class, 'index'])->name('recruitment.index');
            Route::post('/recruitment', [\App\Http\Controllers\Praktikan\RecruitmentController::class, 'store'])->name('recruitment.store');
            Route::get('/recruitment/{id}', [\App\Http\Controllers\Praktikan\RecruitmentController::class, 'show'])->name('recruitment.show');
        });
    });
});

Route::get('/check-npm', [\App\Http\Controllers\AuthController::class, 'checkNpm'])->name('check-npm');

// Portfolio Route (Placed at the end to avoid shadowing preserved keywords like dashboard/portfolio)
Route::get('/aslab/{slug}', [WelcomeController::class, 'aslabPortfolio'])->name('aslab.portfolio');

