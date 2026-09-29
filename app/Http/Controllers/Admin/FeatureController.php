<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FeatureRequest;
use App\Models\Feature;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeatureController extends Controller
{
    private const STATUSES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'develop' => 'Develop',
        'maintenance' => 'Maintenance',
    ];

    public function index(): View
    {
        $features = Feature::query()
            ->latest()
            ->get();

        return view('admin.features.index', [
            'features' => $features,
            'statuses' => self::STATUSES,
        ]);
    }

    public function create(): View
    {
        return view('admin.features.create', [
            'feature' => new Feature(['status' => 'active']),
            'statuses' => self::STATUSES,
        ]);
    }

    public function store(FeatureRequest $request): RedirectResponse
    {
        Feature::create($request->validated());

        return redirect()->route('admin.features.index')->with('success', 'Fitur berhasil ditambahkan.');
    }

    public function show(Feature $feature): View
    {
        return view('admin.features.show', [
            'feature' => $feature,
            'statuses' => self::STATUSES,
        ]);
    }

    public function edit(Feature $feature): View
    {
        return view('admin.features.edit', [
            'feature' => $feature,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(FeatureRequest $request, Feature $feature): RedirectResponse
    {
        $feature->update($request->validated());

        return redirect()->route('admin.features.index')->with('success', 'Fitur berhasil diperbarui.');
    }

    public function destroy(Feature $feature): RedirectResponse
    {
        $feature->delete();

        return redirect()->route('admin.features.index')->with('success', 'Fitur berhasil dihapus.');
    }
}
