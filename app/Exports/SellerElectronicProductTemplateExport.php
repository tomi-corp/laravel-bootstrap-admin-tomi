<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SellerElectronicProductTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'product_name',
            'brand',
            'product_model',
            'sku',
            'ram_gb',
            'storage_gb',
            'color',
            'price',
            'stock',
            'description',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Samsung Galaxy A55',
                'Samsung',
                'SM-A556E',
                'GDG-A55-001',
                8,
                256,
                'Awesome Navy',
                5999000,
                10,
                'New unit, official warranty.',
            ],
            [
                'LG Smart TV 43 Inch',
                'LG',
                '43UR7500',
                'ELC-LGTV-001',
                '',
                '',
                'Black',
                4299000,
                5,
                'Smart TV ready stock.',
            ],
        ];
    }
}
