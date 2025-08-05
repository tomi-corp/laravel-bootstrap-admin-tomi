<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\cost;
use App\Models\history_disbursement_tomi;
use App\Models\history_invoicing_payment;
use App\Models\invoicing_pinjaman;
use App\Models\jatuh_tempo;
use App\Models\notification;
use App\Models\pinjaman;
use App\Models\rekening_tomi;
use App\Models\User;
use App\Models\xenditApi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CallbackController extends Controller
{
    // public function callbackDisbursement(Request $req)
    // {
    //     $header = $req->header();
    //     if ($header['x-callback-token'][0] == "ZDqJmP4QXulDq144VTvrPDAyvlYchFfz0E99ZFjnD2YjtAQM") {
    //         $datees =  Carbon::now();

    //         $pinjaman = pinjaman::where('disbursement_id', $req['id'])->first();
    //         $history = history_disbursement_tomi::where('disbursement_id', $req['id'])->first();
    //         if (isset($pinjaman)) {

    //             $cost = cost::get()->toArray();

    //             if ($req->status == "COMPLETED") {
    //                 $jatuh_tempos = jatuh_tempo::where('id_pinjaman', $pinjaman->id)->get();
    //                 foreach ($jatuh_tempos as $jatuh_tempo) {
    //                     $invoicing = invoicing_pinjaman::whereYear('period', '=', Carbon::parse($jatuh_tempo->jatuh_tempo)->format('Y'))
    //                         ->whereMonth('created_at', '=', Carbon::parse($jatuh_tempo->jatuh_tempo)->format('m'))
    //                         ->where('id_list_perusahaan', $pinjaman->id_perusahaan)
    //                         ->first();

    //                     if (!isset($invoicing)) {
    //                         $invoicing = new invoicing_pinjaman();
    //                         $invoicing->id_list_perusahaan = $pinjaman->id_perusahaan;
    //                         $invoicing->period = Carbon::parse($jatuh_tempo->jatuh_tempo)->format('Y-m-d');
    //                         // $invoicing->total_transaksi_approve = 1;
    //                         $invoicing->status = 'belum_lunas';
    //                         $invoicing->save();
    //                     } else {
    //                         // $invoicing->total_transaksi_approve = $invoicing->total_transaksi_approve + 1;
    //                         $invoicing->status = 'belum_lunas';
    //                         $invoicing->save();
    //                     }
    //                 };


    //                 // kemungkinan bakal pakai lagi untuk privy cost privy
    //                 // check pinjaman yang udah ada identity verification (karna hanya sekali identity verification)
    //                 // $pinjamanCheckFirst = pinjaman::where(function ($q) {
    //                 //     $q->where('status', '=', 'approve')
    //                 //         ->orWhere('status', '=', 'done');
    //                 // })->where('user_id', $pinjaman->user_id)
    //                 //     ->where('id_list_data_ksp', $pinjaman->id_list_data_ksp)
    //                 //     ->first();
    //                 // if (!isset($pinjamanCheckFirst)) {
    //                 //     $pinjaman->cost_tomi = $cost[0]['biaya'] + $cost[2]['biaya'];
    //                 //     $pinjaman->description_cost_tomi = "- identity verifikasi (" . $cost[0]['biaya'] . ") - disbursement rekening tomi (" . $cost[2]['biaya'] . ")";
    //                 //     $pinjaman->cost_ksp = $cost[1]['biaya'];
    //                 //     $pinjaman->description_cost_ksp = "- disbursement peminjam (" . $cost[1]['biaya'] . ")";
    //                 // } else {
    //                 $pinjaman->cost_tomi =  $cost[2]['biaya'];
    //                 $pinjaman->description_cost_tomi = "- disbursement rekening tomi (" . $cost[2]['biaya'] . ")";
    //                 $pinjaman->cost_ksp = $cost[1]['biaya'];
    //                 $pinjaman->description_cost_ksp = "- disbursement peminjam (" . $cost[1]['biaya'] . ")";
    //                 // }
    //                 $pinjaman->status =  'approve';

    //                 // disbursmenet ke rekening tomi
    //                 $rekeningTomi = rekening_tomi::with('bank')->first();
    //                 $xendit = xenditApi::first();
    //                 $url = $xendit->url . "/disbursements";
    //                 $time = Carbon::now();

    //                 $data = [
    //                     "external_id" => "disb-" . $time,
    //                     "amount" => $pinjaman->dpp_adm_pandu - $pinjaman->cost_tomi,
    //                     "bank_code" => $rekeningTomi->bank->code_bank,
    //                     "account_holder_name" => $rekeningTomi->atas_nama,
    //                     "account_number" => $rekeningTomi->nomor_rekening,
    //                     "description" => "pendapatan tomi dengan id pinjaman :" . $pinjaman->id_pinjaman
    //                 ];

    //                 $response = Http::withoutVerifying()
    //                     ->withOptions(["verify" => false])
    //                     ->withBasicAuth($xendit->secret, '')
    //                     // ->withHeaders([])
    //                     ->withBody(json_encode($data), 'application/json')
    //                     ->post($url);

    //                 $response_json = json_decode($response);
    //                 // history
    //                 $history = new history_disbursement_tomi();
    //                 $history->disbursement_id = $response_json->id;
    //                 $history->external_id = $response_json->external_id;
    //                 $history->bank_code = $response_json->bank_code;
    //                 $history->amount = $response_json->amount;
    //                 $history->account_holder_name = $response_json->account_holder_name;
    //                 $history->disbursement_description = $response_json->disbursement_description;
    //                 $history->status = $response_json->status;
    //                 $history->save();

    //                 $notif = new notification();
    //                 $notif->user_id = $pinjaman->user_id;
    //                 $notif->title = "Pengajuan Anda Telah Diperbarui";
    //                 $notif->description = "Selamat! Status pengajuan Anda telah berhasil dikirimkan ke rekening anda!";
    //                 $notif->type = 'pinjaman_payroll';
    //                 $notif->read = 'belum';
    //                 $notif->save();
    //             } elseif ($req->status == "FAILED") {
    //                 $pinjaman->status = 'pending';
    //             }
    //             $pinjaman->save();
    //             return response()->json([
    //                 'status' => 200,
    //                 'message' => $pinjaman->id_pinjaman . " - " . $pinjaman->id_pinjaman,
    //             ], 200);
    //         } elseif (isset($history)) {
    //             $history->status = $req->status;
    //             $history->save();
    //         } else {
    //             abort(404);
    //         }
    //     } else {
    //         abort(404);
    //     }
    // }

    // public function callbackPaymentInvoicing(Request $req)
    // {
    //     $header = $req->header();
    //     if ($header['x-callback-token'][0] == "ZDqJmP4QXulDq144VTvrPDAyvlYchFfz0E99ZFjnD2YjtAQM") {
    //         $cekHistory = history_invoicing_payment::where('payment_id',  $req->data['id'])->where('payment_request_id', $req->data['payment_request_id'])->first();
    //         if (!isset($cekHistory)) {
    //             $history = new history_invoicing_payment();
    //             $history->payment_id = $req->data['id'];
    //             $history->amount = $req->data['amount'];
    //             $history->status = $req->data['status'];
    //             $history->metadata_sku = $req->data['metadata']['sku'];
    //             $history->reference_id = $req->data['reference_id'];
    //             $history->payment_method_id = $req->data['payment_method']['id'];
    //             $history->created_trx = Carbon::parse($req->data['created'])->format('Y-m-d H:i:s');
    //             $history->updated_trx = Carbon::parse($req->data['updated'])->format('Y-m-d H:i:s');
    //             $history->payment_request_id = $req->data['payment_request_id'];
    //             $history->save();

    //             if ($req->data['status'] == "SUCCEEDED") {
    //                 // check status
    //                 // $pinjaman = pinjaman

    //                 $historyWithPerusahaan = history_invoicing_payment::where('payment_id', $req->data['id'])
    //                     ->with('perusahaan')
    //                     ->first();

    //                 $getPeriod = invoicing_pinjaman::where('status', 'belum_lunas')->orderBy('period', 'asc')->first();

    //                 $invoice = invoicing_pinjaman::where('id_list_perusahaan', $historyWithPerusahaan->perusahaan->id)
    //                     ->where('id', $getPeriod->id)
    //                     ->with(['perusahaan' =>  function ($q) use ($getPeriod) {
    //                         $q->with(['pinjamanBerjalan' => function ($w) use ($getPeriod) {
    //                             $w->with(['jatuh_tempoSesuaiInvoice' =>  function ($e) use ($getPeriod) {
    //                                 $e->whereMonth('jatuh_tempo', Carbon::parse($getPeriod->period)->format('m'));
    //                             }, 'data_karyawan']);
    //                         }]);
    //                     }])
    //                     ->first();

    //                 $dataArray = [];
    //                 foreach ($invoice->perusahaan->pinjamanBerjalan as $pinjamanJatuhTempo) {
    //                     $dataArray[] = [
    //                         $pinjamanJatuhTempo->jatuh_tempoSesuaiInvoice->id,
    //                     ];
    //                 }
    //                 jatuh_tempo::whereIn('id', $dataArray)->update(['status' => 'lunas']);

    //                 foreach ($dataArray as $idJatuhTempo) {
    //                     $perbaruiStatus_pinjaman =  jatuh_tempo::where('id', $idJatuhTempo)
    //                         ->with('pinjaman')
    //                         ->first();

    //                     $pinjaman = pinjaman::where('id', $perbaruiStatus_pinjaman->pinjaman->id)->first();
    //                     $countJatuhTempo = jatuh_tempo::where('id_pinjaman', $pinjaman->id)
    //                         ->where('status', 'lunas')
    //                         ->get()
    //                         ->count();
    //                     if ($pinjaman->tenor == $countJatuhTempo) {
    //                         $pinjaman->status =  "done";
    //                         $pinjaman->save();
    //                     }
    //                 }
    //                 $getPeriod->status = 'lunas';
    //                 $getPeriod->save();
    //             }
    //         }
    //         return response()->json([
    //             'status' => 200,
    //             'message' => $req->data['payment_method']['id'] . "-" .   Carbon::now()->format('Y-m-d H:i:s'),
    //         ], 200);
    //     } else {
    //         abort(404);
    //     }
    // }

    public function privyRegisterCallback(Request $req)
    {
        $user = User::where('register_token_privy', $req->register_token)->first();
        if (isset($user)) {
            if ($req->status == 'rejected') {
                $user->verified_privy = 'n';
                $user->reason_privy = $req->reject_reason;
                $user->resend_privy = $req->resend == true ? 1 : 0;
                $user->save();
            } else {
                $user->id_privy = $req->privy_id;
                $user->enterprise_token_privy = $req->enterprise_token;
                $user->reason_privy = NULL;
                $user->resend_privy = NULL;
                $user->verified_privy = 'y';
                $user->save();
            }
        }
        return response()->json([
            'status' => 200,
            'message' => true,
        ], 200);
    }
}
