<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Menu\MenuRequest;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        return view('admin.menus.index', [
            'menus' => $this->menuQuery()->get(),
            'parentMenus' => Menu::query()
                ->orderBy('sort_order')
                ->orderBy('label')
                ->get(['id', 'label', 'name']),
        ]);
    }

    public function store(MenuRequest $request): JsonResponse
    {
        $menu = Menu::create($request->validated());

        return response()->json([
            'message' => 'Menu berhasil ditambahkan.',
            'data' => ['id' => $menu->id],
        ]);
    }

    public function show(Menu $menu): JsonResponse
    {
        return response()->json([
            'data' => $menu->load('parent'),
        ]);
    }

    public function update(MenuRequest $request, Menu $menu): JsonResponse
    {
        $menu->update($request->validated());

        return response()->json([
            'message' => 'Menu berhasil diperbarui.',
            'data' => ['id' => $menu->id],
        ]);
    }

    public function destroy(Menu $menu): JsonResponse
    {
        $menu->delete();

        return response()->json([
            'message' => 'Menu berhasil dihapus.',
        ]);
    }

    private function menuQuery()
    {
        return Menu::query()
            ->with('parent')
            ->orderBy('sort_order')
            ->orderBy('label');
    }
}
