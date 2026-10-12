<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleMenuPermission\RoleMenuPermissionRequest;
use App\Models\Menu;
use App\Models\Role;
use App\Models\RoleMenuPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RoleMenuPermissionController extends Controller
{
    public function index(): View
    {
        return view('admin.role-menu-permissions.index', [
            'roleMenuPermissions' => RoleMenuPermission::query()
                ->with(['role.project', 'menu.project'])
                ->latest()
                ->get(),
            'roles' => Role::query()
                ->with('project')
                ->orderBy('project_id')
                ->orderBy('name')
                ->get(),
            'menus' => Menu::query()
                ->with('project')
                ->orderBy('project_id')
                ->orderBy('parent_id')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'permissions' => RoleMenuPermission::PERMISSIONS,
        ]);
    }

    public function store(RoleMenuPermissionRequest $request): JsonResponse
    {
        $roleMenuPermission = RoleMenuPermission::create($request->validated());

        return response()->json([
            'message' => 'Hak akses menu berhasil ditambahkan.',
            'data' => ['id' => $roleMenuPermission->id],
        ]);
    }

    public function show(RoleMenuPermission $roleMenuPermission): JsonResponse
    {
        return response()->json([
            'data' => $roleMenuPermission->load(['role.project', 'menu.project']),
        ]);
    }

    public function update(
        RoleMenuPermissionRequest $request,
        RoleMenuPermission $roleMenuPermission
    ): JsonResponse {
        $roleMenuPermission->update($request->validated());

        return response()->json([
            'message' => 'Hak akses menu berhasil diperbarui.',
            'data' => ['id' => $roleMenuPermission->id],
        ]);
    }

    public function destroy(RoleMenuPermission $roleMenuPermission): JsonResponse
    {
        $roleMenuPermission->delete();

        return response()->json([
            'message' => 'Hak akses menu berhasil dihapus.',
        ]);
    }
}
