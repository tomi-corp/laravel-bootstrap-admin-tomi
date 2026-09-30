<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\amount_pinjaman;
use App\Models\ListPerusahaan;
use App\Models\list_karyawan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductConfigurationController extends Controller
{
    private const PRODUCTS = [
        'tomi-dana' => 'Tomi Dana',
        'tomi-dana-plus' => 'Tomi Dana Plus',
    ];

    public function tenorCompanies(string $product): View
    {
        $this->abortInvalidProduct($product);

        return view('admin.product-configurations.tenor-companies', [
            'product' => $product,
            'productLabel' => self::PRODUCTS[$product],
            'companies' => $this->companies()
                ->withCount('tenorPinjaman')
                ->get(),
        ]);
    }

    public function dsrCompanies(string $product): View
    {
        $this->abortInvalidProduct($product);

        return view('admin.product-configurations.dsr-companies', [
            'product' => $product,
            'productLabel' => self::PRODUCTS[$product],
            'companies' => $this->companies()
                ->withExists('amount_pinjaman')
                ->get(),
        ]);
    }

    public function dsrEdit(string $product, ListPerusahaan $company): View
    {
        $this->abortInvalidProduct($product);

        return view('admin.product-configurations.dsr-edit', [
            'product' => $product,
            'productLabel' => self::PRODUCTS[$product],
            'company' => $company->load('amount_pinjaman'),
            'maxPlafon' => list_karyawan::where('id_list_perusahaan', $company->id)->max('max_plafon') ?? 0,
        ]);
    }

    public function dsrUpdate(Request $request, string $product, ListPerusahaan $company): RedirectResponse
    {
        $this->abortInvalidProduct($product);

        $validated = $request->validate([
            'iuran_anggota' => ['required', 'numeric', 'min:0'],
            'minimal' => ['required', 'numeric', 'min:0'],
            'maximal' => ['required', 'numeric', 'min:0', 'gte:minimal'],
            'maximal_persentase' => ['required', 'numeric', 'min:0'],
        ]);

        amount_pinjaman::updateOrCreate(
            ['id_perusahaan' => $company->id],
            $validated + ['id_perusahaan' => $company->id]
        );

        return redirect()
            ->route('admin.product-configurations.dsr.edit', [$product, $company])
            ->with('success', 'Konfigurasi DSR berhasil diperbarui.');
    }

    public function tenorEdit(string $product, ListPerusahaan $company): RedirectResponse
    {
        $this->abortInvalidProduct($product);

        return redirect()->route('admin.ksp.konfigurasi', encryptSecure($company->id));
    }

    private function companies()
    {
        return ListPerusahaan::query()
            ->orderBy('nama')
            ->select(['id', 'nama', 'id_code_perusahaan', 'id_lender']);
    }

    private function abortInvalidProduct(string $product): void
    {
        abort_unless(array_key_exists($product, self::PRODUCTS), 404);
    }
}
