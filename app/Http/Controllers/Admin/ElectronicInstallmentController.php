<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ElectronicInstallmentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Dummy Data
    |--------------------------------------------------------------------------
    */
    private $installments = [
        [
            'id'          => 1,
            'name'        => 'iPhone 15 Pro',
            'price'       => 18999000,
            'description' => 'Smartphone flagship terbaru',
            'image'       => 'iphone.jpg',
            'tenor'       => 12,
            'interest'    => 2,
        ],
        [
            'id'          => 2,
            'name'        => 'MacBook Air M3',
            'price'       => 21499000,
            'description' => 'Laptop tipis dan powerful',
            'image'       => 'macbook.jpg',
            'tenor'       => 24,
            'interest'    => 1.5,
        ],
        [
            'id'          => 3,
            'name'        => 'Smart TV 55 Inch',
            'price'       => 7499000,
            'description' => 'TV Ultra HD',
            'image'       => 'https://placehold.co/80',
            'tenor'       => 12,
            'interest'    => 1.8,
        ],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $installments = $this->installments;

        return view(
            'admin.electronic-installments.index',
            compact('installments')
        );
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
            ->route('admin.electronic-installments.index')
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
            ->route('admin.electronic-installments.index')
            ->with('success', 'Data berhasil diupdate (dummy)');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return redirect()
            ->route('admin.electronic-installments.index')
            ->with('success', 'Data berhasil dihapus (dummy)');
    }
}
