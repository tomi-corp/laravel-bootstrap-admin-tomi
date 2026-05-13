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
            'category',
            'brand',
            'product_model',
            'sku',
            'condition',
            'warranty',
            'color',
            'ram',
            'storage',
            'screen_size',
            'battery',
            'camera',
            'operating_system',
            'network',
            'included_items',
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
                'gadget',
                'Samsung',
                'SM-A556E',
                'GDG-A55-001',
                'new',
                'Official 12 months',
                'Awesome Navy',
                '8 GB',
                '256 GB',
                '6.6 inch',
                '5000 mAh',
                '50 MP + 12 MP + 5 MP',
                'Android 14',
                '4G/5G',
                'Box, cable, warranty card',
                5999000,
                10,
                'New unit, official warranty.',
            ],
            [
                'LG Smart TV 43 Inch',
                'electronic',
                'LG',
                '43UR7500',
                'ELC-LGTV-001',
                'new',
                'Official 12 months',
                'Black',
                '',
                '',
                '43 inch',
                '',
                '',
                'webOS',
                'Wi-Fi',
                'Remote, stand, warranty card',
                4299000,
                5,
                'Smart TV ready stock.',
            ],
        ];
    }
}
