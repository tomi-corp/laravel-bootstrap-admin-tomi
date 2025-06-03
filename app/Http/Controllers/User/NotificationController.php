<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\Notification\NotificationResource;
use App\Models\notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function listNotif()
    {
        $notif = notification::where('user_id', auth('api')->user()->id)->get();
        return response()->json([
            'status' => 200,
            'unread' => $notif->where('read', 'belum')->count()  == null ? 0 : $notif->where('read', 'belum')->count(),
            'notification' => NotificationResource::collection($notif),
        ], 200);
    }

    public function readNotif()
    {
        notification::where('user_id', auth('api')->user()->id)
            ->where('read', 'belum')->update(['read' => 'sudah']);
        return response()->json([
            'status' => 200,
            'message' => 'sukses',
        ], 200);
    }

    public function deleteNOtif()
    {
        notification::where('user_id', auth('api')->user()->id)
            ->delete();
        return response()->json([
            'status' => 200,
            'message' => 'sukses delete notification',
        ], 200);
    }
}
