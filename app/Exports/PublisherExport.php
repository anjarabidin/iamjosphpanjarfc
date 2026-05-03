<?php

namespace App\Exports;

use App\Models\Publisher;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PublisherExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection()
    {
        return Publisher::with('admin')->get()->map(function ($publisher) {
            return [
                $publisher->code,
                $publisher->alias,
                $publisher->admin->name,
                $publisher->admin->email,
                $publisher->type,
                $publisher->contact_name,
                $publisher->phone,
                $publisher->city,
                $publisher->address,
                $publisher->website ?? '-',
                $publisher->prefix_doi,
                is_array($publisher->additional_prefixes) ? implode(', ', $publisher->additional_prefixes) : '-',
                $publisher->sk_kemenkumham_link ?? '-',
                $publisher->akta_notaris_link ?? '-',
                $publisher->admin->is_active ? 'Aktif' : 'Nonaktif',
                $publisher->created_at->format('Y-m-d H:i:s'),
            ];
        });
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
            'Status',
            'Dibuat Pada',
        ];
    }

    public function styles($sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3F51B5'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
            'A:P' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                    'wrapText' => true,
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
            'O' => 12, // Status
            'P' => 20, // Dibuat Pada
        ];
    }
}
