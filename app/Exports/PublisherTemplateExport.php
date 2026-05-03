<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PublisherTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Contoh data
        return [
            [
                'PUB001',
                'Jaya Pub',
                'PT Jaya Publisher',
                'publisher@jayapub.com',
                'PT',
                'Budi Santoso',
                '+6281234567890',
                'Jakarta',
                'Jl. Sudirman No. 123, Jakarta Pusat',
                'https://www.jayapub.com',
                '10.5555',
                '10.6666, 10.7777',
                'https://drive.google.com/file/d/...',
                'https://drive.google.com/file/d/...',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Kode Publisher',
            'Alias',
            'Nama Publisher',
            'Email',
            'Tipe',
            'Nama Kontak',
            'Telepon/WA',
            'Kota',
            'Alamat',
            'Website',
            'Prefix DOI',
            'Prefix DOI Tambahan',
            'Link SK Kemenkumham',
            'Link AKTA Notaris',
        ];
    }

    public function styles($sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4CAF50'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            // Data row styling
            2 => [
                'font' => [
                    'color' => ['rgb' => '999999'],
                    'italic' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F5F5F5'],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                    'wrapText' => true,
                ],
            ],
            // All cells
            'A:N' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Kode Publisher
            'B' => 15, // Alias
            'C' => 20, // Nama Publisher
            'D' => 25, // Email
            'E' => 15, // Tipe
            'F' => 20, // Nama Kontak
            'G' => 18, // Telepon/WA
            'H' => 15, // Kota
            'I' => 30, // Alamat
            'J' => 25, // Website
            'K' => 15, // Prefix DOI
            'L' => 25, // Prefix DOI Tambahan
            'M' => 25, // Link SK Kemenkumham
            'N' => 25, // Link AKTA Notaris
        ];
    }
}
