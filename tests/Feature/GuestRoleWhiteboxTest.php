<?php

namespace Tests\Feature;

use App\Models\GuestVisit;
use App\Models\Kegiatan;
use App\Models\Pengumuman;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GuestRoleWhiteboxTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'lab_rpl',
        ]);
    }

    private function getOrCreateUser(): User
    {
        $role = Role::firstOrCreate(['name' => 'Admin']);

        return User::factory()->create([
            'role_id' => $role->id,
            'username' => 'author_'.uniqid(),
        ]);
    }

    public function test_guest_can_access_landing_page_with_view_data(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('welcome');
        $response->assertViewHas(['stats', 'latestPraktikum', 'latestKegiatans']);
    }

    public function test_guest_can_access_live_mode_monitoring_dashboard(): void
    {
        $response = $this->get('/live-mode');

        $response->assertStatus(200);
        $response->assertViewIs('live-mode-public');
        $response->assertViewHas([
            'selectedYear',
            'years',
            'months',
            'monthlyCounts',
            'totalRegistered',
            'verifiedCount',
            'pendingCount',
            'rejectedCount',
        ]);
    }

    public function test_guest_can_access_public_informational_pages(): void
    {
        $this->get('/tentang')->assertStatus(200)->assertViewIs('about');
        $this->get('/praktikum')->assertStatus(200)->assertViewIs('praktikum');
        $this->get('/aslab')->assertStatus(200)->assertViewIs('aslab-public');
        $this->get('/struktur-organisasi')->assertStatus(200)->assertViewIs('struktur-organisasi');
        $this->get('/impostor')->assertStatus(200)->assertViewIs('impostor');
    }

    public function test_guest_can_access_sitemap_xml(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringContainsString('text/xml', (string) $response->headers->get('Content-Type'));
    }

    public function test_guest_can_view_active_announcements_and_detail_or_404(): void
    {
        $user = $this->getOrCreateUser();

        $active = Pengumuman::create([
            'judul' => 'Pengumuman Uji Coba Whitebox '.uniqid(),
            'slug' => 'pengumuman-uji-coba-'.uniqid(),
            'konten' => 'Isi materi pengumuman publik untuk mahasiswa.',
            'kategori' => 'Akademik',
            'is_active' => true,
            'user_id' => $user->id,
        ]);

        $inactive = Pengumuman::create([
            'judul' => 'Pengumuman Nonaktif '.uniqid(),
            'slug' => 'pengumuman-nonaktif-'.uniqid(),
            'konten' => 'Pengumuman arsip privat yang dinonaktifkan.',
            'kategori' => 'Internal',
            'is_active' => false,
            'user_id' => $user->id,
        ]);

        $this->get('/pengumuman')->assertStatus(200)->assertViewIs('pengumuman.index');
        $this->get('/pengumuman/'.$active->slug)->assertStatus(200)->assertSee($active->judul);
        $this->get('/pengumuman/'.$inactive->slug)->assertStatus(404);
        $this->get('/pengumuman/slug-tidak-ditemukan-'.uniqid())->assertStatus(404);
    }

    public function test_guest_can_view_active_activities_and_detail_or_404(): void
    {
        $user = $this->getOrCreateUser();

        $active = Kegiatan::create([
            'judul' => 'Workshop DevOps Mahasiswa '.uniqid(),
            'slug' => 'workshop-devops-'.uniqid(),
            'konten' => 'Dokumentasi kegiatan workshop CI/CD di lab RPL.',
            'tanggal_kegiatan' => now()->toDateString(),
            'lokasi' => 'Ruang Lab RPL',
            'is_active' => true,
            'user_id' => $user->id,
        ]);

        $inactive = Kegiatan::create([
            'judul' => 'Kegiatan Draf Internal '.uniqid(),
            'slug' => 'kegiatan-draf-'.uniqid(),
            'konten' => 'Dokumentasi tertutup.',
            'tanggal_kegiatan' => now()->toDateString(),
            'lokasi' => 'Ruang Rapat',
            'is_active' => false,
            'user_id' => $user->id,
        ]);

        $this->get('/kegiatan')->assertStatus(200)->assertViewIs('kegiatan.index');
        $this->get('/kegiatan/'.$active->slug)->assertStatus(200)->assertSee($active->judul);
        $this->get('/kegiatan/'.$inactive->slug)->assertStatus(404);
        $this->get('/kegiatan/slug-kegiatan-palsu-'.uniqid())->assertStatus(404);
    }

    public function test_guest_can_view_guest_book_portal(): void
    {
        $response = $this->get('/portal-tamu');

        $response->assertStatus(200);
        $response->assertViewIs('guest-visits.index');
        $response->assertViewHas(['activeVisits', 'completedVisits']);
    }

    public function test_guest_can_checkin_to_digital_guest_book_with_valid_data(): void
    {
        $payload = [
            'guest_name' => 'Prof. Dr. Bambang Supriyadi',
            'guest_count' => 4,
            'activity_purpose' => 'Audit kurikulum dan sarana praktikum komputer',
            'lab_condition' => 'Baik',
            'additional_note' => 'Kunjungan resmi delegasi fakultas',
        ];

        $response = $this->post('/portal-tamu', $payload);

        $response->assertRedirect(route('portal-tamu.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('guest_visits', [
            'guest_name' => 'Prof. Dr. Bambang Supriyadi',
            'guest_count' => 4,
            'lab_condition' => 'Baik',
        ]);
    }

    public function test_guest_checkin_validation_fails_on_empty_and_out_of_bound_inputs(): void
    {
        $response = $this->post('/portal-tamu', []);

        $response->assertSessionHasErrors([
            'guest_name',
            'guest_count',
            'activity_purpose',
            'lab_condition',
        ]);

        $outOfBoundResponse = $this->post('/portal-tamu', [
            'guest_name' => 'Tamu Over Kuota',
            'guest_count' => 501,
            'activity_purpose' => 'Rombongan melebihi batas sistem',
            'lab_condition' => 'Baik',
        ]);

        $outOfBoundResponse->assertSessionHasErrors('guest_count');
    }

    public function test_guest_can_checkout_active_visit_record(): void
    {
        $visit = GuestVisit::create([
            'visit_date' => now()->toDateString(),
            'started_at' => now(),
            'guest_name' => 'Ir. Susanto MBA',
            'guest_count' => 2,
            'activity_purpose' => 'Konsultasi riset',
            'lab_condition' => 'Baik',
        ]);

        $response = $this->patch('/portal-tamu/'.$visit->id.'/checkout');

        $response->assertRedirect(route('portal-tamu.index', ['mode' => 'checkout']));
        $response->assertSessionHas('success');

        $this->assertNotNull($visit->fresh()->ended_at);
    }

    public function test_guest_cannot_re_checkout_already_completed_visit(): void
    {
        $endedTime = now()->subHour();

        $visit = GuestVisit::create([
            'visit_date' => now()->toDateString(),
            'started_at' => now()->subHours(2),
            'ended_at' => $endedTime,
            'guest_name' => 'Budi Santoso',
            'guest_count' => 1,
            'activity_purpose' => 'Kunjungan selesai',
            'lab_condition' => 'Baik',
        ]);

        $response = $this->patch('/portal-tamu/'.$visit->id.'/checkout');

        $response->assertRedirect(route('portal-tamu.index', ['mode' => 'checkout']));
        $response->assertSessionHas('info');
    }

    public function test_guest_can_access_authentication_and_onboarding_portals(): void
    {
        $this->get('/login-praktikan')->assertStatus(200);
        $this->get('/login-aslab')->assertStatus(200);
        $this->get('/bahlil')->assertStatus(200);
        $this->get('/register')->assertStatus(200);
        $this->get('/forgot-password')->assertStatus(200);
    }

    public function test_guest_can_check_npm_availability_asynchronously(): void
    {
        $emptyResponse = $this->get('/check-npm');
        $emptyResponse->assertStatus(200);
        $emptyResponse->assertJson(['exists' => false]);

        $unregisteredNpm = '06.2099.1.99999';
        $unregisteredResponse = $this->get('/check-npm?npm='.$unregisteredNpm);
        $unregisteredResponse->assertStatus(200);
        $unregisteredResponse->assertJson(['exists' => false]);

        $registeredUser = $this->getOrCreateUser();
        $registeredResponse = $this->get('/check-npm?npm='.$registeredUser->username);
        $registeredResponse->assertStatus(200);
        $registeredResponse->assertJson(['exists' => true]);
    }

    public function test_guest_accessing_expired_or_invalid_public_verify_token_shows_graceful_notice(): void
    {
        $response = $this->get('/p/token-acak-tidak-ada-'.uniqid());

        $response->assertStatus(200);
        $response->assertViewIs('presensi.public-verify');
        $response->assertSee('tidak berlaku');
    }

    public function test_guest_accessing_scan_landing_with_invalid_token_returns_404(): void
    {
        $response = $this->get('/s/token-jadwal-palsu-'.uniqid());

        $response->assertStatus(404);
    }

    public function test_guest_is_prevented_from_accessing_internal_admin_aslab_and_student_routes(): void
    {
        $this->get('/administrator/dashboard')->assertRedirect(route('login.praktikan'));
        $this->get('/administrator/praktikum')->assertRedirect(route('login.praktikan'));
        $this->get('/aslab/dashboard')->assertRedirect(route('login.praktikan'));
        $this->get('/aslab/penugasan')->assertRedirect(route('login.praktikan'));
        $this->get('/praktikan/dashboard')->assertRedirect(route('login.praktikan'));
        $this->get('/praktikan/penugasan')->assertRedirect(route('login.praktikan'));
    }

    public function test_guest_accessing_home_redirect_is_sent_to_public_welcome(): void
    {
        $response = $this->get('/home');

        $response->assertRedirect(route('home'));
    }

    public function test_guest_cannot_access_secure_storage_route_unauthenticated(): void
    {
        $response = $this->get('/storage/krs/dokumen-rahasia-mahasiswa.pdf');

        $response->assertStatus(403);
    }

    public function test_guest_registration_validation_enforces_prodi_npm_regex_and_password_strength(): void
    {
        Role::firstOrCreate(['name' => 'Praktikan']);

        $invalidNpmResponse = $this->post('/register', [
            'npm' => '99.2025.1.01111',
            'name' => 'Calon Mahasiswa',
            'email' => 'calon@itats.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $invalidNpmResponse->assertSessionHasErrors('npm');

        $shortPasswordResponse = $this->post('/register', [
            'npm' => '06.2026.1.07777',
            'name' => 'Calon Mahasiswa',
            'email' => 'calon2@itats.ac.id',
            'password' => '12345',
            'password_confirmation' => '12345',
        ]);
        $shortPasswordResponse->assertSessionHasErrors('password');

        $unconfirmedPasswordResponse = $this->post('/register', [
            'npm' => '06.2026.1.07777',
            'name' => 'Calon Mahasiswa',
            'email' => 'calon2@itats.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'passwordBeda',
        ]);
        $unconfirmedPasswordResponse->assertSessionHasErrors('password');

        $validNpm = '06.'.date('Y').'.1.'.rand(10000, 99999);
        $validResponse = $this->post('/register', [
            'npm' => $validNpm,
            'name' => 'Praktikan Baru Terdaftar',
            'email' => 'praktikan.'.uniqid().'@itats.ac.id',
            'password' => 'SecurePass2026!',
            'password_confirmation' => 'SecurePass2026!',
        ]);

        $validResponse->assertRedirect(route('login.praktikan'));
        $validResponse->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['username' => $validNpm]);
        $this->assertDatabaseHas('praktikans', ['npm' => $validNpm]);
    }

    public function test_guest_book_safely_persists_xss_and_sql_injection_payloads(): void
    {
        $xssName = '<script>alert("xss")</script>';
        $sqlInjectionPurpose = "' OR '1'='1' -- DROP TABLE users;";

        $response = $this->post('/portal-tamu', [
            'guest_name' => $xssName,
            'guest_count' => 1,
            'activity_purpose' => $sqlInjectionPurpose,
            'lab_condition' => 'Baik',
            'additional_note' => 'Penetrasi string berbahaya oleh audit testing',
        ]);

        $response->assertRedirect(route('portal-tamu.index'));

        $this->assertDatabaseHas('guest_visits', [
            'guest_name' => $xssName,
            'activity_purpose' => $sqlInjectionPurpose,
        ]);

        $portalResponse = $this->get('/portal-tamu');
        $portalResponse->assertStatus(200);
        $portalResponse->assertDontSee($xssName, false);
    }

    public function test_route_throttle_middleware_is_configured_on_guest_mutations(): void
    {
        $routes = app('router')->getRoutes();

        $portalTamuStore = $routes->getByName('portal-tamu.store');
        $this->assertNotNull($portalTamuStore);
        $this->assertContains('throttle:20,1', $portalTamuStore->middleware());

        $portalTamuCheckout = $routes->getByName('portal-tamu.checkout');
        $this->assertNotNull($portalTamuCheckout);
        $this->assertContains('throttle:30,1', $portalTamuCheckout->middleware());

        $registerPost = $routes->getByName('register.post');
        $this->assertNotNull($registerPost);
        $this->assertContains('throttle:5,1', $registerPost->middleware());

        $loginPost = $routes->getByName('login.post');
        $this->assertNotNull($loginPost);
        $this->assertContains('throttle:10,1', $loginPost->middleware());
    }
}
