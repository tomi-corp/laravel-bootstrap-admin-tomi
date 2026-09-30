<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Project\ProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        return view('admin.projects.index', [
            'projects' => Project::query()
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(ProjectRequest $request): JsonResponse
    {
        $project = Project::create($request->validated());

        return response()->json([
            'message' => 'Project berhasil ditambahkan.',
            'data' => ['id' => $project->id],
        ]);
    }

    public function show(Project $project): JsonResponse
    {
        return response()->json([
            'data' => $project,
        ]);
    }

    public function update(ProjectRequest $request, Project $project): JsonResponse
    {
        $project->update($request->validated());

        return response()->json([
            'message' => 'Project berhasil diperbarui.',
            'data' => ['id' => $project->id],
        ]);
    }

    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return response()->json([
            'message' => 'Project berhasil dihapus.',
        ]);
    }
}
