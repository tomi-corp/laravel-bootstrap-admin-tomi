<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ElectronicInstallmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $installments = collect();
        $tableAvailable = Schema::hasTable('gadget_installments');

        if ($tableAvailable) {
            $query = DB::table('gadget_installments as gi');
            $selects = ['gi.*'];

            if ($this->hasColumn('gadget_installments', 'user_id') && Schema::hasTable('users')) {
                $query->leftJoin('users as u', 'u.id', '=', 'gi.user_id');

                foreach (['name', 'nama_lengkap', 'email', 'nohp'] as $column) {
                    if ($this->hasColumn('users', $column)) {
                        $selects[] = "u.{$column} as user_{$column}";
                    }
                }
            }

            if ($this->hasColumn('gadget_installments', 'product_id') && Schema::hasTable('products')) {
                $query->leftJoin('products as p', 'p.id', '=', 'gi.product_id');

                foreach (['name', 'brand', 'model'] as $column) {
                    if ($this->hasColumn('products', $column)) {
                        $selects[] = "p.{$column} as product_{$column}";
                    }
                }
            }

            if ($this->hasColumn('gadget_installments', 'variant_id') && Schema::hasTable('product_variants')) {
                $query->leftJoin('product_variants as pv', 'pv.id', '=', 'gi.variant_id');

                foreach (['sku', 'ram_gb', 'storage_gb', 'color', 'price'] as $column) {
                    if ($this->hasColumn('product_variants', $column)) {
                        $selects[] = "pv.{$column} as variant_{$column}";
                    }
                }
            }

            if ($this->hasColumn('gadget_installments', 'created_at')) {
                $query->orderByDesc('gi.created_at');
            } elseif ($this->hasColumn('gadget_installments', 'id')) {
                $query->orderByDesc('gi.id');
            }

            $installments = $query
                ->select($selects)
                ->get();
        }

        return view(
            'admin.electronic-installments.index',
            compact('installments', 'tableAvailable')
        );
    }

    private function hasColumn(string $table, string $column): bool
    {
        return Schema::hasTable($table) && Schema::hasColumn($table, $column);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.electronic-installments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'price'    => 'required|numeric',
            'tenor'    => 'required|integer',
            'interest' => 'required|numeric',
        ]);

        return redirect()
            ->route('admin.cicilan-elektronik.index')
            ->with('success', 'Data berhasil ditambahkan (dummy)');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $installment = collect($this->installments)
            ->firstWhere('id', $id);

        if (!$installment) {
            abort(404);
        }

        return view(
            'admin.electronic-installments.show',
            compact('installment')
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $installment = collect($this->installments)
            ->firstWhere('id', $id);

        if (!$installment) {
            abort(404);
        }

        return view(
            'admin.electronic-installments.edit',
            compact('installment')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'     => 'required',
            'price'    => 'required|numeric',
            'tenor'    => 'required|integer',
            'interest' => 'required|numeric',
        ]);

        return redirect()
            ->route('admin.cicilan-elektronik.index')
            ->with('success', 'Data berhasil diupdate (dummy)');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return redirect()
            ->route('admin.cicilan-elektronik.index')
            ->with('success', 'Data berhasil dihapus (dummy)');
    }
}
