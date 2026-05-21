<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SellerElectronicProductImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    private array $products = [];

    public function collection(Collection $rows)
    {
        $this->products = $rows
            ->map(fn($row) => $this->mapRow($row))
            ->filter(fn($product) => !empty($product['product_name']) && !empty($product['sku']))
            ->values()
            ->toArray();
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    private function mapRow(Collection $row): array
    {
        return [
            'product_name' => $this->value($row, ['product_name', 'nama_produk', 'name']),
            'product_model' => $this->value($row, ['product_model', 'model']),
            'sku' => $this->value($row, ['sku', 'kode_produk']),
            'brand' => $this->value($row, ['brand', 'merek']),
            'color' => $this->value($row, ['color', 'warna']),
            'ram_gb' => $this->nullableInteger($this->value($row, ['ram_gb', 'ram'])),
            'storage_gb' => $this->nullableInteger($this->value($row, ['storage_gb', 'storage', 'memori', 'penyimpanan'])),
            'price' => $this->number($this->value($row, ['price', 'harga'])),
            'stock' => (int) $this->number($this->value($row, ['stock', 'stok'])),
            'description' => $this->value($row, ['description', 'deskripsi']),
        ];
    }

    private function value(Collection $row, array $keys, mixed $default = null): mixed
    {
        foreach ($keys as $key) {
            $value = $row->get($key);

            if ($value !== null && $value !== '') {
                return is_string($value) ? trim($value) : $value;
            }
        }

        return $default;
    }

    private function number(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        return (float) preg_replace('/[^0-9.-]/', '', (string) $value);
    }

    private function nullableInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $this->number($value);
    }
}
