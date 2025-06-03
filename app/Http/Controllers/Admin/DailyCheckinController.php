<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DailyCheckin\DailyCheckinUpdateRequest;
use App\Models\daily_checkin;
use App\Models\daily_checkin_history;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class DailyCheckinController extends Controller
{
    public function dailyIndex()
    {
        $daily = daily_checkin::get();
        return view('admin.daily.index', compact('daily'));
    }

    public function dailyTable()
    {
        $daily = daily_checkin_history::with('user')->get();

        return DataTables::of($daily)
            ->addIndexColumn()

            ->addColumn('datetime', function ($row) {
                return Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
            })
            ->addColumn('name', function ($row) {
                return $row->user->name;
            })

            ->toJson();
    }

    public function dailyUpdate(DailyCheckinUpdateRequest $req)
    {
        $daily = daily_checkin::get();
        $xxyy = 1;
        foreach ($daily as $dail) {
            $dailyNew = daily_checkin::where('id', $dail->id)->first();
            $ticket = $req['hari' . $xxyy];
            $dailyNew->reward = $ticket;
            $dailyNew->save();
            $xxyy++;
        }

        return response()->json([
            'code' => 200,
            'message' => 'Successfully update reward daily checkin',
        ], 200);
    }
}
