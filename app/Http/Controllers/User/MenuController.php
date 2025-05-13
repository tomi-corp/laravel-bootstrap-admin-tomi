<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\Menu\MenulistResource;
use App\Models\menu_mobile;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function listMenu()
    {
        $menu = menu_mobile::get();
        return response()->json([
            'status' => 200,
            'menu' =>  MenulistResource::collection($menu)
        ], 200);
    }
}
