<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\DailyCheckin\DailyCheckinResource;
use App\Models\daily_checkin;
use App\Models\daily_checkin_history;
use App\Models\pointHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyCheckinController extends Controller
{

    private function getHari()
    {
        $now = Carbon::now();

        $x = 1;
        $hari = array(
            "senin" => array(
                'tanggal' => '',
                'reward' => '',
                'status' => ''
            ),
            "selasa" => array(
                'tanggal' => '',
                'reward' => '',
                'status' => ''
            ),
            "rabu" => array(
                'tanggal' => '',
                'reward' => '',
                'status' => ''
            ),
            "kamis" => array(
                'tanggal' => '',
                'reward' => '',
                'status' => ''
            ),
            "jumat" => array(
                'tanggal' => '',
                'reward' => '',
                'status' => ''
            ),
            "sabtu" => array(
                'tanggal' => '',
                'reward' => '',
                'status' => ''
            ),
            "minggu" => array(
                'tanggal' => '',
                'reward' => '',
                'status' => ''
            ),
        );

        $weekStartDate = $now->startOfWeek()->format('Y-m-d');
        // $weekEndDate = $now->endOfWeek()->format('Y-m-d');
        $tanggal = '';
        $kumpulanSatuMinggu = [];
        $x = 1;

        $daily_checkin = daily_checkin::get();


        foreach ($hari as &$yyy) {
            if ($x == 1) {
                $tanggal = $weekStartDate;
            } else {
                $tanggal = Carbon::parse($tanggal)->addDay(1)->format('Y-m-d');
            }

            $yyy["tanggal"] = $tanggal;
            $yyy["reward"] = $daily_checkin[$x - 1]->reward;
            if ($tanggal == Carbon::now()->format("Y-m-d")) {
                $yyy["status"] = "avaiable absen";
            } else {
                $yyy["status"] = "disabled";
            }

            // return $yyy;

            $daily_check_history = daily_checkin_history::whereDate("created_at", $tanggal)
                ->where('user_id', auth('api')->user()->id)
                ->orderBy('created_at')
                ->first();


            if (isset($daily_check_history)) {
                if ($tanggal == Carbon::parse($daily_check_history->created_at)->format('Y-m-d')) {
                    $yyy["status"] = "sudah";
                } elseif ($tanggal == Carbon::now()->format("Y-m-d")) {
                    $yyy["status"] = "avaiable absen";
                } else {
                    $yyy["status"] = "disabled";
                }
            }

            $kumpulanSatuMinggu[] = $tanggal;
            $x++;
        }

        return $hari;
    }

    public function getList()
    {
        $hari = $this->getHari();

        return response()->json([
            'status' => 200,
            'dailyCheckin' => $hari,
        ], 200);
    }

    public function doDailyCheckin()
    {
        $hari = $this->getHari();

        $hitunganHari = 1;
        foreach ($hari as $key => $value) {
            if ($value['status'] == "avaiable absen") {
                echo $key;
                $dailyCheckin = daily_checkin::where('hari_ke', $hitunganHari)->first();
                $dailyCheckinHistory = new daily_checkin_history();
                $dailyCheckinHistory->daily_checkin_id = $dailyCheckin->id;
                $dailyCheckinHistory->user_id = auth('api')->user()->id;
                $dailyCheckinHistory->ticket = $dailyCheckin->reward;
                $dailyCheckinHistory->description = "Daily checkin";
                $dailyCheckinHistory->expired = Carbon::now()->addMonth(1)->format('Y-m-d');
                // add point history
                $point = new pointHistory();
                $point->user_id = auth('api')->user()->id;
                $point->description = "get Point Daily Checkin";
                $point->point = $dailyCheckin->reward;
                $point->type = "daily_checkin";
                $point->save();
                $user = User::where('id', auth('api')->user()->id)->first();
                $user->point = $user->point + $dailyCheckin->reward;
                $user->save();

                $dailyCheckinHistory->save();
                return response()->json([
                    'status' => 200,
                    'message' => "succes claim daily checkin",
                ], 200);
            }
            $hitunganHari++;
        }

        // ini ga avaiable untuk absen
        return response()->json([
            'status' => 400,
            'message' => "Daily checkin sudah dilakukan",
        ], 400);
    }
}
