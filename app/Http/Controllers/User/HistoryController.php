<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\History\HistoryDetailRequest;
use App\Http\Resources\User\History\HistoryDetailResource;
use App\Http\Resources\User\History\HistoryMenuTabResource;
use App\Http\Resources\User\History\HistoryPpobResource;
use App\Http\Resources\User\History\HistoryResource;
use App\Models\menu_mobile;
use App\Models\pinjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HistoryController extends Controller
{
    public function history(Request $req)
    {
        $user = auth('api')->user();
        $menuMobile = menu_mobile::get();
        if (isset($req->filterRangePicker)) {
            $time = explode(" - ", $req->filterRangePicker);
            // $req->
            $time1 = Carbon::parse($time[0])->startOfDay()->format('Y-m-d H:i:s');
            $time2 = Carbon::parse($time[1])->endOfDay()->format('Y-m-d H:i:s');
        } else {
            $time1 = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
            $time2 = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
        }


        if ($req->path == "ppob") {
            $menu = menu_mobile::where('path', 'ppob')->first();
            $phone = substr($user->nohp, 2);
            $phoneFinal = '0' . $phone;
            $urlPPOBPrepaid = 'https://mitra.buylink.id/api/user/report/transaction?start_date=' . $time1 . '&end_date=' . $time2 . '&customer_phone=' . $phoneFinal . '&page=' . $req->page . '';
            $urlPPOBPostPaid = 'https://mitra.buylink.id/api/user/report/postpaid/transaction?start_date=' . $time1 . '&end_date=' . $time2 . '&customer_phone=' . $phoneFinal . '&page=' . $req->page . '';
            // postpaid
            $responsePrepaid = Http::withoutVerifying()
                ->withOptions(["verify" => false])
                ->withHeaders([
                    'Authorization' => 'Bearer Vxx2OfFdXfLPjW0X2fHTGkd3MBC3Ldw7Zj7yx64Z474e0df2'
                ])
                ->get($urlPPOBPrepaid);

            $responsePostPaid = Http::withoutVerifying()
                ->withOptions(["verify" => false])
                ->withHeaders([
                    'Authorization' => 'Bearer Vxx2OfFdXfLPjW0X2fHTGkd3MBC3Ldw7Zj7yx64Z474e0df2'
                ])
                ->get($urlPPOBPostPaid);


            // Decode JSON menjadi array asosiatif
            $dataPostpaid = json_decode($responsePostPaid, true);
            $dataPrepaid = json_decode($responsePrepaid, true);

            // Sekarang 'data' adalah kunci dari array, jadi Anda bisa mengaksesnya dengan kurung siku []
            $merged_data = array_merge($dataPostpaid['data'], $dataPrepaid['data']);

            usort($merged_data, function ($a, $b) {
                return strtotime($b['transaction_at']) - strtotime($a['transaction_at']);
            });

            $cccc = [];
            $x = 0;

            foreach ($merged_data as $aaf) {
                $cccc[$x]['transaction_id'] = $aaf['transaction_id'];
                $cccc[$x]['created_at'] = $aaf['transaction_at'];
                $cccc[$x]['status'] = $aaf['status_label'];
                $cccc[$x]['produk'] = $aaf['product_name'];
                $cccc[$x]['price'] = str_replace('Rp', '', $aaf['price']);
                $cccc[$x]['price'] = str_replace('.', '', $cccc[$x]['price']);
                $cccc[$x]['payment_method'] = $aaf['payment_method'];
                $cccc[$x]['type'] =  $menu->title;
                $cccc[$x]['path'] =  $menu->path;
                $cccc[$x]['img'] = $menu->icon;
                $x++;
            }

            return response()->json([
                'status' => 200,
                'message' => 'success',
                'menu_mobile' => HistoryMenuTabResource::collection($menuMobile),
                'history' => HistoryPpobResource::collection($cccc),
                'page' => $dataPostpaid['meta']['current_page']
            ], 200);
        } elseif ($req->path ==  "pinjaman-payroll") {
            $menu = menu_mobile::where('path', 'pinjaman-payroll')->first();
            $historyPinjam = pinjaman::where('user_id', auth('api')->user()->id)
                ->whereBetween('created_at', [$time1, $time2])
                ->select(['nominal', 'status', 'created_at', 'id'])
                ->paginate(15);

            $historyPinjam->map(function ($order) use ($menu) {
                $order['type'] =  $menu->title;
                $order['path'] =  $menu->path;
                $order['img'] = $menu->icon;
                $order['produk'] = 'pinjaman-payroll';
                return $order;
            });

            return response()->json([
                'status' => 200,
                'message' => 'success',
                'menu_mobile' => HistoryMenuTabResource::collection($menuMobile),
                'history' => HistoryResource::collection($historyPinjam),
                'page' => $historyPinjam->currentPage()
            ], 200);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'success',
                'menu_mobile' => HistoryMenuTabResource::collection($menuMobile),
                'history' => HistoryResource::collection([])
            ], 200);
        }
    }

    public function historyDetail(HistoryDetailRequest $req)
    {
        if ($req->path == 'pinjaman-payroll') {
            $historyPinjam = pinjaman::where('user_id', auth('api')->user()->id)
                ->where('id', $req->id)
                ->with('jatuh_tempo')
                ->first();
            if (isset($historyPinjam)) {
                return response()->json([
                    'status' => 200,
                    'message' => 'success',
                    'historyDetail' => new HistoryDetailResource($historyPinjam)
                ], 200);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'success',
                    'historyDetail' => []
                ], 200);
            }
        }
    }
}
