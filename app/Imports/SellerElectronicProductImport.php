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
            ->filter(fn($product) => !empty($product['product_name']))
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
            'category' => $this->category($this->value($row, ['category', 'kategori'])),
            'product_model' => $this->value($row, ['product_model', 'model']),
            'sku' => $this->value($row, ['sku', 'kode_produk']),
            'brand' => $this->value($row, ['brand', 'merek']),
            'condition' => $this->condition($this->value($row, ['condition', 'kondisi'])),
            'warranty' => $this->value($row, ['warranty', 'garansi']),
            'color' => $this->value($row, ['color', 'warna']),
            'ram' => $this->value($row, ['ram']),
            'storage' => $this->value($row, ['storage', 'memori', 'penyimpanan']),
            'screen_size' => $this->value($row, ['screen_size', 'ukuran_layar']),
            'battery' => $this->value($row, ['battery', 'baterai']),
            'camera' => $this->value($row, ['camera', 'kamera']),
            'operating_system' => $this->value($row, ['operating_system', 'os', 'sistem_operasi']),
            'network' => $this->value($row, ['network', 'jaringan']),
            'included_items' => $this->value($row, ['included_items', 'kelengkapan']),
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

    private function category(mixed $value): string
    {
        $value = strtolower((string) $value);

        return str_contains($value, 'gadget') ? 'gadget' : 'electronic';
    }

    private function condition(mixed $value): string
    {
        $value = strtolower((string) $value);

        return in_array($value, ['used', 'refurbished'], true) ? $value : 'new';
    }
}
