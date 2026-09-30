<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Menu\MenuRequest;
use App\Models\Feature;
use App\Models\Menu;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $menuColumns = Schema::getColumnListing('menus');
        $parentMenuQuery = Menu::query();

        if (in_array('sort_order', $menuColumns, true)) {
            $parentMenuQuery->orderBy('sort_order');
        }

        if (in_array('status', $menuColumns, true)) {
            $parentMenuQuery->where('status', 'show');
        } elseif (in_array('is_active', $menuColumns, true)) {
            $parentMenuQuery->where('is_active', true);
        }

        $parentMenuQuery->orderBy(in_array('label', $menuColumns, true) ? 'label' : 'name');

        return view('admin.menus.index', [
            'menus' => $this->menuQuery()->get(),
            'menuColumns' => $menuColumns,
            'parentMenus' => $parentMenuQuery->get(array_values(array_intersect(['id', 'label', 'name'], $menuColumns))),
            'projects' => Project::query()
                ->orderBy('name')
                ->get(['id', 'name', 'code']),
            'features' => Feature::query()
                ->orderBy('name')
                ->get(['id', 'name', 'code']),
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
        $relations = ['parent'];

        if (Schema::hasColumn('menus', 'project_id')) {
            $relations[] = 'project';
        }

        if (Schema::hasColumn('menus', 'feature_id')) {
            $relations[] = 'feature';
        }

        return response()->json([
            'data' => $menu->load($relations),
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
        $menuColumns = Schema::getColumnListing('menus');
        $query = Menu::query()->with('parent');

        if (in_array('project_id', $menuColumns, true)) {
            $query->with('project')->orderBy('project_id');
        }

        if (in_array('feature_id', $menuColumns, true)) {
            $query->with('feature')->orderBy('feature_id');
        }

        if (in_array('sort_order', $menuColumns, true)) {
            $query->orderBy('sort_order');
        }

        return $query->orderBy(in_array('label', $menuColumns, true) ? 'label' : 'name');
    }
}
