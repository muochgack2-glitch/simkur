<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AstsScheduleTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Jadwal ASTS';
    }

    public function headings(): array
    {
        return ['Hari', 'Sesi', 'Kelas', 'Jurusan', 'Nama Mapel', 'Nama Guru'];
    }

    public function array(): array
    {
        return [
            ['Senin', '1', 'X',  'AKL',    'Nama Mata Pelajaran', 'Nama Guru, S.Pd.'],
            ['Senin', '2', 'X',  'AKL',    '', ''],
            ['Selasa','1', 'X',  'AKL',    '', ''],
            ['Rabu',  '1', 'X',  'AKL',    '', ''],
            ['Kamis', '1', 'X',  'AKL',    '', ''],
            ['Jumat', '1', 'X',  'AKL',    '', ''],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e40af']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '93c5fd']]],
        ]);
        $sheet->getStyle('A2:F100')->applyFromArray([
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'd1d5db']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);
        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 12, 'B' => 8, 'C' => 8, 'D' => 12, 'E' => 40, 'F' => 35];
    }
}