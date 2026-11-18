<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Dasbor utama & dasbor per produk (/backoffice/insight). Butuh database lokal berisi skema & data
 * (pinjamen, disbursements, salary_financings, ...) dan `npm run build` (manifest Vite). Semua di-rollback.
 */
class InsightDashboardTest extends TestCase
{
    use DatabaseTransactions;

    private const PRODUCTS = ['tomidana', 'tomigaji', 'tomiinvoice', 'tomigawai'];

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admins', 'pinjamen', 'disbursements', 'salary_financings', 'financing_request_invoices', 'gadget_installments'] as $table) {
            if (! Schema::hasTable($table)) {
                $this->markTestSkipped("Tabel $table belum ada di database test.");
            }
        }
    }

    private function makeAdmin(): Admin
    {
        $admin = new Admin;
        $admin->username = 'insight_test_'.Str::random(8);
        $admin->email = $admin->username.'@example.test';
        $admin->type = 'master';
        $admin->password = Hash::make('Secret#123');
        $admin->saveQuietly();

        return $admin->fresh();
    }

    public function test_pages_and_data_work_for_every_period_and_product(): void
    {
        $admin = $this->makeAdmin();
        $periods = [
            ['mode' => 'all'], ['mode' => 'year'], ['mode' => 'month', 'month' => now()->format('Y-m')],
            ['mode' => 'day', 'day' => now()->toDateString()],
            ['mode' => 'custom', 'from' => now()->subDays(40)->toDateString(), 'to' => now()->toDateString()],
        ];

        $this->actingAs($admin, 'admin')->get('/backoffice/insight')->assertOk()->assertSee('Dasbor utama');
        foreach (self::PRODUCTS as $p) {
            $this->actingAs($admin, 'admin')->get('/backoffice/insight/'.$p)->assertOk();
        }
        foreach ($periods as $q) {
            foreach (array_merge(['overview'], self::PRODUCTS) as $scope) {
                $this->actingAs($admin, 'admin')->getJson('/backoffice/insight/data?'.http_build_query($q + ['scope' => $scope]))
                    ->assertOk()
                    ->assertJsonStructure(['period' => ['mode', 'from', 'to', 'granularity', 'label'], 'lender' => ['id', 'name'], 'data' => ['trend' => ['categories', 'series'], 'revenue' => ['components', 'total']]]);
            }
        }
    }

    public function test_overview_totals_add_up_and_granularity_follows_period(): void
    {
        $admin = $this->makeAdmin();
        $json = $this->actingAs($admin, 'admin')->getJson('/backoffice/insight/data?scope=overview&mode=all&lender=all')->assertOk()->json();

        $this->assertSame('month', $json['period']['granularity']);
        $this->assertEqualsWithDelta($json['data']['hero']['value'], array_sum(array_column($json['data']['products'], 'disbursed')), 1);
        foreach ($json['data']['trend']['series'] as $series) {
            $this->assertCount(count($json['data']['trend']['categories']), $series['data']);
        }
        $this->assertEqualsWithDelta($json['data']['revenue']['total'], array_sum(array_column($json['data']['revenue']['components'], 'total')), 1);

        $day = $this->actingAs($admin, 'admin')->getJson('/backoffice/insight/data?scope=tomidana&mode=day')->json();
        $this->assertSame('hour', $day['period']['granularity']);
        $this->assertCount(24, $day['data']['trend']['categories']);
    }

    public function test_noa_disbursed_and_outstanding_follow_definition(): void
    {
        $admin = $this->makeAdmin();
        $json = $this->actingAs($admin, 'admin')->getJson('/backoffice/insight/data?scope=overview&mode=all&lender=all')->assertOk()->json();
        $data = $json['data'];
        $kpi = collect($data['kpis'])->keyBy('label');
        $products = collect($data['products'])->keyBy('key');

        // NOA penyaluran = jumlah akun cair semua produk; NOA outstanding = jumlah akun berjalan semua produk
        $this->assertSame((int) $data['hero']['count'], (int) $kpi['NOA penyaluran']['value']);
        $this->assertSame($products->sum('count'), (int) $kpi['NOA penyaluran']['value']);
        $this->assertSame($products->sum('noa_out'), (int) $kpi['NOA outstanding']['value']);
        $this->assertSame((int) $kpi['NOA penyaluran']['value'], array_sum($data['trend']['noa']));

        // NOA penyaluran (catatan kantor) dihitung langsung dari tabel:
        // TomiDana = pinjaman approve/done + jumlah peminjam upload manual; TomiGaji = jumlah karyawan pengajuan yang ditransfer
        $danaDisbursed = DB::table('pinjamen')->whereNull('deleted_at')->whereIn('status', ['approve', 'done'])->count()
            + (int) DB::table('invoice_generate_manuals')->sum('jumlah_peminjam');
        $gajiDisbursed = (int) DB::table('salary_financings')->whereIn('status', ['uploaded_transfer', 'uploaded_bayar', 'lunas'])->sum('total_borrowers');
        $this->assertSame($gajiDisbursed, (int) $products['tomigaji']['count']);
        // NOA outstanding TomiDana: pinjaman cair yang masih punya cicilan belum lunas
        $danaOpen = DB::table('jatuh_tempos as jt')->join('pinjamen as p', 'p.id', '=', 'jt.id_pinjaman')
            ->join('disbursements as d', 'd.pinjaman_id', '=', 'p.id')
            ->whereNull('p.deleted_at')->where('jt.status', '!=', 'lunas')->distinct()->count('p.id');
        $this->assertSame($danaDisbursed, (int) $products['tomidana']['count']);
        $this->assertSame($danaOpen, (int) $products['tomidana']['noa_out']);

        // Dasbor produk memuat kedua NOA + deret per bucket
        foreach (['tomidana', 'tomigaji', 'tomiinvoice', 'tomigawai'] as $p) {
            $pj = $this->actingAs($admin, 'admin')->getJson('/backoffice/insight/data?scope='.$p.'&mode=year')->assertOk()->json('data');
            $labels = array_column($pj['kpis'], 'label');
            $this->assertContains('NOA penyaluran', $labels, $p);
            $this->assertContains('NOA outstanding', $labels, $p);
            $this->assertCount(count($pj['trend']['categories']), $pj['trend']['noa'], $p);
            $this->assertCount(count($pj['trend']['categories']), $pj['trend']['noa_out'], $p);
        }

        // Grafik kecil "Sisa ... berjalan" = posisi sisa per akhir bulan (bukan akumulasi dana cair):
        // titik terakhir harus sama dengan angka KPI-nya.
        $sisa = collect($data['kpis'])->firstWhere('label', 'Sisa pinjaman berjalan');
        $this->assertEqualsWithDelta($sisa['value'], end($sisa['spark']), 1);
        $noaOut = $kpi['NOA outstanding'];
        $this->assertEqualsWithDelta($noaOut['value'], end($noaOut['spark']), 0.1);
        foreach (['tomidana', 'tomigaji', 'tomiinvoice', 'tomigawai'] as $p) {
            $pj = $this->actingAs($admin, 'admin')->getJson('/backoffice/insight/data?scope='.$p.'&mode=year')->json('data');
            $k = collect($pj['kpis'])->firstWhere('label', 'Sisa pembiayaan berjalan');
            $this->assertEqualsWithDelta($k['value'], end($k['spark']), 1, $p);
            $this->assertCount(count($pj['trend']['categories']), $pj['trend']['outstanding'], $p);
        }

        // Per jam tidak ada deret outstanding (posisi saldo per jam tidak bermakna)
        $day = $this->actingAs($admin, 'admin')->getJson('/backoffice/insight/data?scope=tomidana&mode=day')->json('data');
        $this->assertNull($day['trend']['noa_out']);
        $this->assertNull($day['trend']['outstanding']);
    }

    public function test_real_login_session_can_load_dashboard_data(): void
    {
        $admin = $this->makeAdmin();
        $this->post('/backoffice/login', ['identity' => $admin->username, 'password' => 'Secret#123']);

        $this->get('/backoffice/insight')->assertOk()->assertSee('TomiDana');
        $this->getJson('/backoffice/insight/data?scope=tomidana&mode=year')->assertOk();
    }

    public function test_lender_filter_and_scope_are_validated(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin, 'admin')->getJson('/backoffice/insight/data?scope=unknown')->assertNotFound();
        $this->actingAs($admin, 'admin')->get('/backoffice/insight/unknown')->assertNotFound();
        $this->actingAs($admin, 'admin')->getJson('/backoffice/insight/data?scope=overview&lender=999999')->assertStatus(422);

        $lenderId = DB::table('lenders')->orderBy('id')->value('id');
        if ($lenderId) {
            $json = $this->actingAs($admin, 'admin')->getJson('/backoffice/insight/data?scope=overview&mode=all&lender='.$lenderId)->assertOk()->json();
            $this->assertSame((int) $lenderId, $json['lender']['id']);
        }
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/backoffice/insight')->assertRedirect(route('admin.login'));
        $this->get('/backoffice/insight/data?scope=overview')->assertRedirect(route('admin.login'));
    }
}
