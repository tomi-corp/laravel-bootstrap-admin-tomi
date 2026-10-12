<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Role\RoleRequest;
use App\Models\Project;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::query()
                ->with('project')
                ->orderBy('project_id')
                ->orderBy('name')
                ->get(),
            'projects' => Project::query()
                ->orderBy('name')
                ->get(['id', 'name', 'code']),
        ]);
    }

    public function store(RoleRequest $request): JsonResponse
    {
        $role = Role::create($request->validated());

        return response()->json([
            'message' => 'Peran berhasil ditambahkan.',
            'data' => ['id' => $role->id],
        ]);
    }

    public function show(Role $role): JsonResponse
    {
        return response()->json([
            'data' => $role->load('project'),
        ]);
    }

    public function update(RoleRequest $request, Role $role): JsonResponse
    {
        $role->update($request->validated());

        return response()->json([
            'message' => 'Peran berhasil diperbarui.',
            'data' => ['id' => $role->id],
        ]);
    }

    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return response()->json([
            'message' => 'Peran berhasil dihapus.',
        ]);
    }
}
