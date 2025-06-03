<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\Point\PointHistoryResource;
use App\Models\pointHistory;
use Illuminate\Http\Request;

class PointController extends Controller
{
    public function pointHistory()
    {
        $point = pointHistory::where('user_id', auth('api')->user()->id)->get();

        return response()->json([
            'status' => 200,
            'history' => PointHistoryResource::collection($point)
        ], 200);
    }
}
