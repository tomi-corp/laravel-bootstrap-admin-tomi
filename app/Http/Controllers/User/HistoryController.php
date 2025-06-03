<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\History\HistoryDetailRequest;
use App\Http\Resources\User\History\HistoryDetailResource;
use App\Http\Resources\User\History\HistoryResource;
use App\Models\menu_mobile;
use App\Models\pinjaman;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function history()
    {
        $historyPinjam = pinjaman::where('user_id', auth('api')->user()->id)
            ->select(['nominal', 'status', 'created_at', 'id'])
            ->get();

        $menu = menu_mobile::where('path', 'pinjaman-payroll')->first();

        $historyPinjam->map(function ($order) use ($menu) {
            $order['type'] =  "ksp";
            $order['img'] = $menu->icon;
            return $order;
        });

        return response()->json([
            'status' => 200,
            'message' => 'success',
            'history' => HistoryResource::collection($historyPinjam)
        ], 200);
    }

    public function historyDetail(HistoryDetailRequest $req)
    {
        if ($req->type == "ksp") {
            $historyPinjam = pinjaman::where('user_id', auth('api')->user()->id)
                ->where('id', $req->id)
                ->first();

            return response()->json([
                'status' => 200,
                'message' => 'success',
                'historyDetail' => new HistoryDetailResource($historyPinjam)
            ], 200);
        }
    }
}
