<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Support\BackofficeNav;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Kerangka backoffice: semua halaman menu dibuka di kerangka baru (sidebar & header baru, tanpa sidebar/topbar lama),
 * tidak ada istilah versi/pilihan tampilan di HTML, dan login diarahkan ke Dasbor utama.
 * Butuh database lokal berisi tabel admins dan `npm run build` (manifest Vite). Semua di-rollback.
 */
class BackofficeShellTest extends TestCase
{
    use DatabaseTransactions;

    /** Halaman yang memang sudah rusak di prod (bukan karena kerangka): view memanggil route yang tidak ada. */
    private const KNOWN_BROKEN = ['admin.website.invoice.index'];

    protected function setUp(): void
    {
        parent::setUp();
        if (! Schema::hasTable('admins')) {
            $this->markTestSkipped('Tabel admins belum ada di database test.');
        }
    }

    private function makeAdmin(): Admin
    {
        $admin = new Admin;
        $admin->username = 'shell_test_'.Str::random(8);
        $admin->email = $admin->username.'@example.test';
        $admin->type = 'master';
        $admin->password = Hash::make('Secret#123');
        $admin->saveQuietly();

        return $admin->fresh();
    }

    /** @return array<string, string> route => label untuk semua menu di sidebar */
    private function menuRoutes(Admin $admin): array
    {
        $this->actingAs($admin, 'admin');
        $routes = [];
        foreach (BackofficeNav::build($admin) as $item) {
            if ($item['type'] === 'link') {
                $routes[$item['route']] = $item['label'];
            } elseif ($item['type'] === 'product') {
                foreach ($item['children'] as $c) {
                    $routes[$c['route']] = $c['label'];
                }
            }
        }

        return $routes;
    }

    public function test_every_menu_page_renders_in_the_new_shell(): void
    {
        $admin = $this->makeAdmin();
        $routes = $this->menuRoutes($admin);
        $this->assertArrayHasKey('admin.insight.index', $routes);
        $this->assertArrayHasKey('admin.manage.pic-lender.index', $routes);
        $this->assertArrayHasKey('admin.website.invoice.index', $routes);

        foreach ($routes as $route => $label) {
            if (in_array($route, self::KNOWN_BROKEN, true)) {
                continue;
            }
            $html = $this->actingAs($admin, 'admin')->get(route($route))->assertOk()->getContent();

            $this->assertStringContainsString('id="page-title"', $html, $route);
            $this->assertStringContainsString('aria-current="page"', $html, "$route: menu aktif");
            $this->assertStringContainsString('<title>'.e($label).' · TOMI</title>', $html, $route);
            $this->assertStringNotContainsString('left-side-menu', $html, "$route: sidebar lama");
            $this->assertStringNotContainsString('navbar-custom', $html, "$route: topbar lama");
            $this->assertDoesNotMatchRegularExpression('/\bv2\b|app-v2|-v2\b|\bv2-/i', $html, "$route: istilah v2");
            foreach (['Tampilan lama', 'Tampilan baru', 'Dasbor baru'] as $text) {
                $this->assertStringNotContainsString($text, $html, "$route: $text");
            }
        }
    }

    public function test_menu_targets_are_unique(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin, 'admin');
        $urls = [];
        foreach (BackofficeNav::build($admin) as $item) {
            if ($item['type'] === 'link') {
                $urls[] = route($item['route']);
            } elseif ($item['type'] === 'product') {
                if ($item['dashboard']) {
                    $urls[] = route($item['dashboard']['route'], $item['dashboard']['params']);
                }
                foreach ($item['children'] as $c) {
                    $urls[] = route($c['route']);
                }
            }
        }
        $this->assertSame(count($urls), count(array_unique($urls)));
    }

    public function test_detail_page_marks_parent_menu_and_keeps_its_own_title(): void
    {
        $companyId = DB::table('list_perusahaans')->whereNull('deleted_at')->orderBy('id')->value('id');
        if (! $companyId) {
            $this->markTestSkipped('Butuh minimal satu perusahaan.');
        }
        $admin = $this->makeAdmin();
        $html = $this->actingAs($admin, 'admin')->get('/backoffice/salary-financing/'.$companyId)->assertOk()->getContent();
        $this->assertStringContainsString('TomiGaji / Pemantauan', $html);
        $this->assertStringContainsString('data-from-nav="0"', $html);
    }

    public function test_login_redirects_to_main_dashboard(): void
    {
        $admin = $this->makeAdmin();
        $this->post('/backoffice/login', ['identity' => $admin->username, 'password' => 'Secret#123'])
            ->assertRedirect(route('admin.insight.index'));
        $this->get('/backoffice/login')->assertRedirect(route('admin.insight.index'));
    }
}
