<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Notif\NotificationActionRequest;
use App\Models\history_notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class NotificationController extends Controller
{
    public function notificationPushIndex()
    {
        return view('admin.notification.index');
    }

    public function notificationPushAction(NotificationActionRequest $req)
    {
        try {
            $response = sendNotification('news', $req->title, $req->isiPesan);
            dd($response);
            // $newHistoryNotification = new history_notification();
            // $newHistoryNotification->title = $req->title;
            // $newHistoryNotification->isipesan = $req->isiPesan;
            // $newHistoryNotification->save();
            // return response()->json([
            //     'status' => 200,
            //     'message' => 'Pesan berhasil dikirim!'
            // ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
    }


    public function notificationPushTable()
    {
        $data = history_notification::latest();

        return DataTables::of($data)

            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
            })


            ->rawColumns(['file', 'action'])
            ->toJson();
    }
}
