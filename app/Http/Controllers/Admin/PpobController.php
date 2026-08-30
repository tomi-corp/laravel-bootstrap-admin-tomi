<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListPerusahaan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class PpobController extends Controller
{
    private function buylinkUrl(string $path): string
    {
        return rtrim(config('services.buylink.base_url'), '/') . $path;
    }

    private function buylinkUserHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . config('services.buylink.user_token')
        ];
    }

    private function buylinkLeaderHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . config('services.buylink.leader_token')
        ];
    }

    public function komisiIndex()
    {
        return view('admin.ppob.komisi.index');
    }

    public function prepaidIndex()
    {
        return view('admin.ppob.prepaid.index');
    }

    public function prepaidTansactionSummary(Request $req)
    {
        if (isset($req->filterRangePicker)) {
            $time = explode(" - ", $req->filterRangePicker);
            $time1 = Carbon::parse($time[0])->format('Y-m-d');
            $time2 = Carbon::parse($time[1])->format('Y-m-d');
        } else {
            $time = Carbon::now();
            $time1 = $time->format('Y-m-d');
            $time2 = $time->format('Y-m-d');
        }

        $url = $this->buylinkUrl("/api/user/report/transaction/summary?start_date=" . $time1 . "&end_date=" . $time2 . "&status=sukses");
        $response = Http::withoutVerifying()
            ->withOptions(["verify" => false])
            ->withHeaders($this->buylinkUserHeaders())
            ->get($url);
        $data = json_decode($response);
        return response()->json([
            'status' => 200,
            'data' => [
                'date' => $time1 . ' - ' . $time2,
                'all' => $data->data->all,
                'today' => $data->data->today
            ],
        ], 200);
    }

    public function postpaidTransactionSummary()
    {

        $time = Carbon::now();
        $url = $this->buylinkUrl("/api/user/report/postpaid/transaction/summary?start_date=" . $time->format("Y-m-d") . "&end_date=" . $time->format("Y-m-d") . "&status=sukses");
        $response = Http::withoutVerifying()
            ->withOptions(["verify" => false])
            ->withHeaders($this->buylinkUserHeaders())
            ->get($url);
        $data = json_decode($response);
        return response()->json([
            'status' => 200,
            'data' => [
                'date' => $time->format('Y-m-d'),
                'all' => $data->data->all,
                'today' => $data->data->today
            ],
        ], 200);
    }

    public function postpaidIndex()
    {
        return view('admin.ppob.postpaid.index');
    }


    public function prepaidTransactionTable(Request $req)
    {

        $urlBase = $this->buylinkUrl("/api/user/report/transaction");

        if (isset($req->filterRangePicker)) {
            $time = explode(" - ", $req->filterRangePicker);
            $time1 = Carbon::parse($time[0])->format('Y-m-d');
            $time2 = Carbon::parse($time[1])->format('Y-m-d');
        } else {
            $time = Carbon::now();
            $time1 = $time->format('Y-m-d');
            $time2 = $time->format('Y-m-d');
        }
        // get time
        $urlBase = $urlBase . '?start_date=' . $time1 . '&end_date=' . $time2;
        if ($req->nama_produk) {
            $urlBase = $urlBase . '&product_name=' . $req->nama_produk;
        };
        if ($req->id_order) {
            $urlBase = $urlBase . '&ref_id=' . $req->id_order;
        };
        if ($req->nama_pelanggan) {
            $urlBase = $urlBase . '&customer_name=' . $req->customer_name;
        };
        if ($req->metode_pembayaran) {
            $urlBase = $urlBase . '&payment_method=' . $req->metode_pembayaran;
        };
        $urlBase = $urlBase . '&page=' . $req->pageTable;
        $response = Http::withoutVerifying()
            ->withOptions(["verify" => false])
            ->withHeaders($this->buylinkUserHeaders())
            ->get($urlBase);
        $data = json_decode($response);

        return response()->json([
            'status' => 200,
            'data' => $data->data,
            'link' => $data->links,
            'meta' => $data->meta
        ], 200);
    }

    public function postpaidTransactionTable(Request $req)
    {
        $urlBase = $this->buylinkUrl("/api/user/report/postpaid/transaction");
        if (isset($req->filterRangePicker)) {
            $time = explode(" - ", $req->filterRangePicker);
            $time1 = Carbon::parse($time[0])->format('Y-m-d');
            $time2 = Carbon::parse($time[1])->format('Y-m-d');
        } else {
            $time = Carbon::now();
            $time1 = $time->format('Y-m-d');
            $time2 = $time->format('Y-m-d');
        }
        // get time
        $urlBase = $urlBase . '?start_date=' . $time1 . '&end_date=' . $time2;
        if ($req->nama_produk) {
            $urlBase = $urlBase . '&product_name=' . $req->nama_produk;
        };
        if ($req->id_order) {
            $urlBase = $urlBase . '&ref_id=' . $req->id_order;
        };
        if ($req->nama_pelanggan) {
            $urlBase = $urlBase . '&customer_name=' . $req->customer_name;
        };
        if ($req->metode_pembayaran) {
            $urlBase = $urlBase . '&payment_method=' . $req->metode_pembayaran;
        };
        $urlBase = $urlBase . '&page=' . $req->pageTable;
        $response = Http::withoutVerifying()
            ->withOptions(["verify" => false])
            ->withHeaders($this->buylinkUserHeaders())
            ->get($urlBase);
        $data = json_decode($response);

        return response()->json([
            'status' => 200,
            'data' => $data->data,
            'link' => $data->links,
            'meta' => $data->meta
        ], 200);
    }

    public function komisiSummaryAll(Request $req)
    {
        // ini data leader
        $time = explode(" - ", $req->filterRangePicker);
        $time1 = Carbon::parse($time[0])->format('Y-m-d');
        $time2 = Carbon::parse($time[1])->format('Y-m-d');

        $urlPostPaid = $this->buylinkUrl('/api/user/leader/transaction/summary?start_date=' . $time1 . '&end_date=' . $time2 . '&type=postpaid');
        $urlPrePaid = $this->buylinkUrl('/api/user/leader/transaction/summary?start_date=' . $time1 . '&end_date=' . $time2 . '&type=prepaid');

        // postpaid
        $responsePostPaid = Http::withoutVerifying()
            ->withOptions(["verify" => false])
            ->withHeaders($this->buylinkLeaderHeaders())
            ->get($urlPostPaid);
        $dataPostpaid = json_decode($responsePostPaid);
        // prepaid
        $responsePrePaid = Http::withoutVerifying()
            ->withOptions(["verify" => false])
            ->withHeaders($this->buylinkLeaderHeaders())
            ->get($urlPrePaid);
        $dataPrepaid = json_decode($responsePrePaid);

        return response()->json([
            'status' => 200,
            'date' => $time1 . " - " . $time2,
            'dataPostPaid' => $dataPostpaid,
            'dataPrepaid' => $dataPrepaid,
        ], 200);
    }
}
