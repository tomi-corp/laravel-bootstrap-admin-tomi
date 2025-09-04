<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceEmail;
use App\Models\invoicing_pinjaman;
use App\Models\pinjaman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;




class DashboardController extends Controller
{
    public function dashboard()
    {
        // register
        $registerBulanIni = User::whereMonth('created_at', Carbon::now()->month)
            ->count();
        $registerBulanKemarin = User::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->count();

        $register = User::orderBy('created_at', 'desc')->take(3)
            ->get()
            ->map(function ($item) {
                $item->type = 'Registrasi';
                return $item;
            });
        $pinjaman = pinjaman::orderBy('created_at', 'desc')->take(3)->with('data_karyawan')
            ->get()
            ->map(function ($item) {
                $item->type = 'Pinjaman Payroll';
                return $item;
            });

        $merged = $register->merge($pinjaman);
        $sorted = $merged->sortByDesc('created_at')->values();
        $dataFinal = [];
        foreach ($sorted as $data) {
            if ($data->type == 'Registrasi') {
                $name = $data->name;
            } else {
                $name = $data->data_karyawan->nama_karyawan ?? '-';
            }
            $dataFinal[] = [
                'type' => $data->type,
                'created_at' => $data->created_at->diffForHumans(),
                'name' => $name,
            ];
        }


        $persentase =  ($registerBulanIni - $registerBulanKemarin) * $registerBulanKemarin * 100 / 100;
        $register = [
            'persentase' => $persentase,
            'registerBulanIni' => $registerBulanIni,
            'activiti' => $dataFinal,
        ];
        // transaction
        return view('admin.dashboard.index', compact('register'));
    }

    public function invoicee($cekkk, $id, $code_perusahaan)
    {
        if ($cekkk == "rLl52H1s1zl") {
            $getPeriod = invoicing_pinjaman::where('id', $id)
                ->with('perusahaan')
                ->first();
            if ($getPeriod) {
                if ($getPeriod->perusahaan->id_code_perusahaan == "#" . $code_perusahaan) {
                    $invoice = invoicing_pinjaman::where('id', $id)
                        ->with(['perusahaan' =>  function ($q) use ($getPeriod) {
                            $q->with(['pinjamanBerjalan' => function ($w) use ($getPeriod) {
                                $w->with(['jatuh_tempoSesuaiInvoice' =>  function ($e) use ($getPeriod) {
                                    $e->whereMonth('jatuh_tempo', Carbon::parse($getPeriod->period)->format('m'));
                                }, 'data_karyawan']);
                            }]);
                        }])
                        ->first();
                    return view("admin.ksp.invoicing.index", compact('invoice'));
                } else {
                    abort(404);
                }
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }

    public function test()
    {

        //     $fileName = 'testtt.pdf';

        //     $disk = Storage::disk('local');
        //     $html = $disk->get('template/pkk.html');

        //     $dataPengganti = [
        //         'nomor' => 4,
        //         'hari' => "Kamis",
        //         'tanggal_sekarang' => "04/09/2025",
        //         'ct' => "2025",
        //         'bulan_romawi' => "IX",
        //         'id_pinjaman' => "#LOAN-2025-09-04-4807147",
        //         't' => 25,
        //         'pinjaman_jatuh_tempo' => '
        //     <table class="table-pinjaman">
        //     <thead>
        //         <tr class="table-pinjaman__row">
        //             <th class="table-pinjaman__header">Angsuran ke -</th>
        //             <th class="table-pinjaman__header">Nilai Angsuran</th>
        //             <th class="table-pinjaman__header">Tanggal Jatuh Tempo</th>
        //         </tr>
        //     </thead>
        //     <tbody>
        //         <tr class="table-pinjaman__row ">
        //             <td class="table-pinjaman__cell">1</td>
        //             <td class="table-pinjaman__cell table-pinjaman__cell--nilai">Rp 2,000,000</td>
        //             <td class="table-pinjaman__cell">2025-09-25</td>
        //         </tr> 
        //          <tr class="table-pinjaman__row ">
        //             <td class="table-pinjaman__cell">1</td>
        //             <td class="table-pinjaman__cell table-pinjaman__cell--nilai">Rp 2,000,000</td>
        //             <td class="table-pinjaman__cell">2025-09-25</td>
        //         </tr> <tr class="table-pinjaman__row ">
        //             <td class="table-pinjaman__cell">1</td>
        //             <td class="table-pinjaman__cell table-pinjaman__cell--nilai">Rp 2,000,000</td>
        //             <td class="table-pinjaman__cell">2025-09-25</td>
        //         </tr>
        //     </tbody>
        //     </table>
        // ',
        //         'pihak_kedua' => '<b>RYAN ARDYANSYAH</b>,26 tahun, beralamat di JL.KOTA BAMBU SELATAN NO.31, RT.011 RW.03 , KEL.KOTA BAMBU SELATAN , KEC.PALMERAH, JAKARTA BARAT , KODE POS : 11420 pemegang kartu tanda penduduk nomor 3173071706990006 karyawan pada PT FIDAC INOVASI TEKNOLOGI dengan NIP 00177052024',
        //         'plafond' => 'Rp ' . number_format(2000000),
        //         'plafond_terbilang' => ' (Dua Juta rupiah)',
        //         'jangka' => '1 bulan',
        //         'rate' => '0%',
        //         'angsuran_perbulan' => 'Rp ' . number_format(2000000),
        //         'angsuran_perbulan_terbilang' => ' (Dua Juta rupiah)',
        //         'administrasi' => 'Rp ' . number_format(60000),
        //         'biaya_layanan' => 'Rp ' . number_format(20000),
        //         'iuran_anggota' => 'Rp ' . number_format(5000),
        //         'rekening_pencairan' => "1336594993",
        //         'bank_pencairan' => "BNI",
        //         'atas_nama_rekening_pencairan' => "RYAN ARDYANSYAH",
        //         'jumlah_pencairan' => 'Rp ' . number_format(1915000),
        //         'terbilang_jumlah_pencairan' => ' (Satu Juta Sembilan Ratus Lima Belas Ribu rupiah)',
        //         'bankTomi' => "BNI",
        //         'rekeningTomi' => "2011113338",
        //         'atasNamaBankTomi' => "KOPERASI SIMPAN PINJAM DUMI DIGITAL TEKNOLOGI",
        //         'penerima_pinjaman' => "RYAN ARDYANSYAH"
        //     ];

        //     foreach ($dataPengganti as $key => $value) {
        //         $html = str_replace("{{ \$$key }}", $value, $html);
        //     }

        //     $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');
        //     $outputPdfPath = 'assets/pk/' . $fileName;
        //     $disk->put($outputPdfPath, $pdf->output());
    }
}
