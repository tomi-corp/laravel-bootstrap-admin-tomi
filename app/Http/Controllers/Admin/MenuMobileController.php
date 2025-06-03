<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MobileMenu\AddMobileMenuRequest;
use App\Http\Requests\Admin\MobileMenu\UpdateMobileMenuRequest;
use App\Http\Resources\Admin\MobileMenu\ShowMenuResource;
use App\Models\menu_mobile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class MenuMobileController extends Controller
{
    public function index()
    {
        return view("admin.menuMobile.index");
    }

    public function addMenu(AddMobileMenuRequest $req)
    {
        $str = $req->icon;
        $replace = preg_replace('/^data:image\/(?:jpeg|jpg|png|gif|svg|svg\+xml|webp);base64,/', '', $str);
        if ($replace !== $str) {
            if (preg_match('/^data:image\/(.+?);base64,/', $str, $matches)) {
                $mimeType = $matches[1];
                if ($mimeType != "php") {
                    if ($mimeType == "svg+xml") {
                        $mimeType = "svg";
                    }
                    $cs = base64_decode($replace);
                    $sekarang = Carbon::now();
                    $fileName =  $sekarang->format('Y_m_d_H_i_s') . "." . $mimeType;
                    File::put(public_path('/assets/images/uploaded/' . $fileName), $cs);
                    $menuMobile = new menu_mobile();
                    $menuMobile->title = $req->title;
                    $menuMobile->icon = $fileName;
                    $menuMobile->path = $req->path;
                    $menuMobile->status = $req->status;
                    $menuMobile->save();
                    return response()->json([
                        'status' => 200,
                        'message' => 'Menu Mobile Berhasil Ditambahkan'
                    ], 200);
                }
            }
        } else {
            return response()->json([
                'status' => 422,
                'message' => 'file tidak didukung',
                'errors' => [
                    'icon' => [
                        'icon tidak didukung'
                    ]
                ]
            ], 422);
        }
    }


    public function updateMenu(UpdateMobileMenuRequest $req)
    {
        $menuMobile =  menu_mobile::where('id', $req->id)->first();
        if (isset($req->icon)) {
            $str = $req->icon;
            $replace = preg_replace('/^data:image\/(?:jpeg|jpg|png|gif|svg|svg\+xml|webp);base64,/', '', $str);
            if ($replace !== $str) {
                if (preg_match('/^data:image\/(.+?);base64,/', $str, $matches)) {
                    $mimeType = $matches[1];
                    if ($mimeType != "php") {
                        if ($mimeType == "svg+xml") {
                            $mimeType = "svg";
                        }
                        $cs = base64_decode($replace);
                        $sekarang = Carbon::now();
                        $fileName =  $sekarang->format('Y_m_d_H_i_s') . "." . $mimeType;
                        File::put(public_path('/assets/images/uploaded/' . $fileName), $cs);
                        $menuMobile->icon = $fileName;
                    }
                }
            }
        }

        $menuMobile->title = $req->title;
        $menuMobile->path = $req->path;
        $menuMobile->status = $req->status;
        $menuMobile->save();
        return response()->json([
            'status' => 200,
            'message' => 'Menu Mobile Berhasil diUpdate'
        ], 200);
    }

    public function listMenu()
    {
        $menu = menu_mobile::get();
        return response()->json([
            'status' => 200,
            'menu' => ShowMenuResource::collection($menu)
        ], 200);
    }

    public function deleteMenu(Request $req)
    {
        $menu = menu_mobile::where('id', $req->id)->first()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Success delete menu mobile'
        ], 200);
    }

    public function detailMenu(Request $req)
    {
        $menu = menu_mobile::where('id', $req->id)->first();
        return response()->json([
            'status' => 200,
            'menu' => new ShowMenuResource($menu)
        ], 200);
    }
}
