<?php

namespace App\Exports;

use App\Models\Author;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AuthorExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection()
    {
        return Author::with('admin')->get()->map(function ($author) {
            return [
                $author->registration_number,
                $author->admin->name,
                $author->admin->email,
                $author->institution ?? '-',
                $author->phone ?? '-',
                $author->admin->is_active ? 'Aktif' : 'Nonaktif',
                $author->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No. Registrasi',
            'Nama Author',
            'Email',
            'Institusi',
            'Telepon/WA',
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
            'A:G' => [
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
            'A' => 18,
            'B' => 25,
            'C' => 30,
            'D' => 25,
            'E' => 18,
            'F' => 12,
            'G' => 20,
        ];
    }
}
