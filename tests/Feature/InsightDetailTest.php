<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Panel rincian dasbor (/backoffice/insight/detail): total rincian = angka kartunya, tahap alur, baris perusahaan,
 * cari/urut/halaman/CSV. Butuh database lokal berisi data. Semua di-rollback.
 */
class InsightDetailTest extends TestCase
{
    use DatabaseTransactions;

    private const SCOPES = ['overview', 'tomidana', 'tomigaji', 'tomiinvoice', 'tomigawai'];

    protected function setUp(): void
    {
        parent::setUp();
        if (! Schema::hasTable('pinjamen') || ! Schema::hasTable('disbursements')) {
            $this->markTestSkipped('Skema data dasbor belum ada di database test.');
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

    public function test_every_kpi_detail_matches_its_card_value(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin, 'admin');

        foreach (['mode=all', 'mode=year&year=2026', 'mode=month&month=2026-09'] as $period) {
            foreach (self::SCOPES as $scope) {
                $kpis = $this->getJson("/backoffice/insight/data?scope=$scope&$period&lender=all")->assertOk()->json('data.kpis');
                foreach ($kpis as $k) {
                    $this->assertNotEmpty($k['metric'], "$scope {$k['label']} tanpa metric");
                    $d = $this->getJson("/backoffice/insight/detail?scope=$scope&metric={$k['metric']}&$period&lender=all")->assertOk()->json('data');
                    $msg = "$period $scope {$k['label']}";
                    if ($k['value'] === null) {
                        $this->assertNull($d['summary']['value'], $msg);
                    } else {
                        $this->assertEqualsWithDelta((float) $k['value'], (float) $d['summary']['value'], $k['format'] === 'pct' ? 0.0001 : 1, $msg);
                    }
                    $this->assertSame($d['pagination']['all'], $d['summary']['count'], $msg);
                    $this->assertLessThanOrEqual(20, count($d['rows']), $msg);
                    $this->assertNotEmpty($d['definition'], $msg);
                }
            }
        }

        // Kartu hero "Total dana tersalurkan" = metrik disbursed
        $hero = $this->getJson('/backoffice/insight/data?scope=overview&mode=all&lender=all')->json('data.hero.value');
        $d = $this->getJson('/backoffice/insight/detail?scope=overview&metric=disbursed&mode=all&lender=all')->json('data');
        $this->assertEqualsWithDelta($hero, $d['summary']['value'], 1);
        $this->assertSame('product', $d['breakdowns'][0]['key']);
        $this->assertEqualsWithDelta($hero, array_sum(array_column($d['breakdowns'][0]['items'], 'value')), 1);
    }

    public function test_funnel_stages_and_company_rows_match_their_values(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin, 'admin');

        foreach (['mode=all', 'mode=year&year=2026'] as $period) {
            foreach (['tomidana', 'tomigaji', 'tomiinvoice', 'tomigawai'] as $scope) {
                $data = $this->getJson("/backoffice/insight/data?scope=$scope&$period&lender=all")->assertOk()->json('data');

                // setiap tahap alur pengajuan
                foreach ($data['funnel']['stages'] as $i => $st) {
                    $d = $this->getJson("/backoffice/insight/detail?scope=$scope&metric=funnel&stage=$i&$period&lender=all")->assertOk()->json('data');
                    $this->assertSame($st['value'], $d['summary']['value'], "$period $scope tahap {$st['name']}");
                    $this->assertSame($i, $d['stage']);
                    $this->assertStringContainsString($st['name'], $d['definition']);
                }

                // setiap baris "Per perusahaan / pelanggan"
                foreach ($data['breakdown']['rows'] as $row) {
                    $q = http_build_query(['scope' => $scope, 'metric' => 'disbursed', 'f' => $row['filters'], 'lender' => 'all']);
                    $d = $this->getJson("/backoffice/insight/detail?$q&$period")->assertOk()->json('data');
                    $this->assertEqualsWithDelta($row['value'], $d['summary']['value'], 1, "$period $scope {$row['name']} · {$row['sub']}");
                    $this->assertEquals($row['filters'], $d['filters']);
                }
            }

            // dasbor utama: baris "Perusahaan mitra teratas" (TomiDana + TomiGaji)
            $overview = $this->getJson("/backoffice/insight/data?scope=overview&$period&lender=all")->json('data');
            foreach ($overview['companies'] as $c) {
                $q = http_build_query(['scope' => 'overview', 'metric' => 'disbursed', 'f' => ['company' => $c['name']], 'only' => 'tomidana,tomigaji', 'lender' => 'all']);
                $d = $this->getJson("/backoffice/insight/detail?$q&$period")->assertOk()->json('data');
                $this->assertEqualsWithDelta($c['dana'] + $c['gaji'], $d['summary']['value'], 1, "$period {$c['name']}");
            }
        }

        // klik item pecahan di panel = saring daftar; total tersaring = nilai item
        $d = $this->getJson('/backoffice/insight/detail?scope=overview&metric=disbursed&mode=all&lender=all')->json('data');
        foreach ($d['breakdowns'] as $b) {
            foreach ($b['items'] as $it) {
                if (str_starts_with($it['label'], 'Lainnya (')) {
                    continue;
                }
                $q = http_build_query(['scope' => 'overview', 'metric' => 'disbursed', 'mode' => 'all', 'lender' => 'all', 'f' => [$b['key'] => $it['label']]]);
                $f = $this->getJson("/backoffice/insight/detail?$q")->json('data');
                $this->assertEqualsWithDelta($it['value'], $f['summary']['value'], 1, "{$b['key']} {$it['label']}");
                $this->assertSame($it['count'], $f['summary']['count']);
            }
        }

        // kunci filter di luar daftar diabaikan
        $x = $this->getJson('/backoffice/insight/detail?scope=overview&metric=disbursed&mode=all&lender=all&f[password]=x&f[company][]=y')->json('data');
        $this->assertEmpty((array) $x['filters']);
    }

    public function test_search_sort_paging_and_csv(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin, 'admin');
        $base = '/backoffice/insight/detail?scope=tomidana&metric=noa&mode=all&lender=all';

        $all = $this->getJson($base)->json('data');
        if ($all['pagination']['all'] < 2) {
            $this->markTestSkipped('Butuh minimal 2 pencairan TomiDana.');
        }
        $p2 = $this->getJson($base.'&page=2')->json('data');
        $this->assertSame(2, $p2['pagination']['page']);
        $asc = $this->getJson($base.'&sort=amount&dir=asc')->json('data.rows');
        $this->assertLessThanOrEqual($asc[1]['amount'], $asc[0]['amount']);

        $ref = $all['rows'][0]['ref'];
        $found = $this->getJson($base.'&q='.urlencode($ref))->json('data');
        $this->assertGreaterThanOrEqual(1, $found['pagination']['total']);
        $this->assertSame($all['pagination']['all'], $found['pagination']['all']);

        $csv = $this->get($base.'&format=csv');
        $csv->assertOk();
        $this->assertStringContainsString('text/csv', $csv->headers->get('content-type'));
        $body = $csv->streamedContent();
        $this->assertSame($all['pagination']['all'] + 1, substr_count(trim($body), "\n") + 1);
        $this->assertStringContainsString('Akun;', $body);
    }

    public function test_unknown_metric_is_404(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin, 'admin')->getJson('/backoffice/insight/detail?scope=overview&metric=applications')->assertNotFound();
        $this->actingAs($admin, 'admin')->getJson('/backoffice/insight/detail?scope=tomigawai&metric=late')->assertNotFound();
        $this->actingAs($admin, 'admin')->getJson('/backoffice/insight/detail?scope=unknown&metric=disbursed')->assertNotFound();
    }
}
