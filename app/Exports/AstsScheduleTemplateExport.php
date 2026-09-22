<?php

namespace App\Exports;

use App\Models\AstsSchedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AstsScheduleTemplateExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Jadwal ASTS';
    }

    public function collection()
    {
        $hariOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5];

        return AstsSchedule::all()
            ->sortBy(fn($r) => ($hariOrder[$r->hari] ?? 9) * 10 + $r->sesi);
    }

    public function map($row): array
    {
        return [
            $row->hari,
            $row->sesi,
            $row->kelas,
            $row->jurusan,
            $row->mapel,
            $row->nama_guru,
        ];
    }

    public function headings(): array
    {
        return ['Hari', 'Sesi', 'Kelas', 'Jurusan', 'Nama Mapel', 'Nama Guru'];
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

        $lastRow = AstsSchedule::count() + 1;
        $sheet->getStyle("A2:F{$lastRow}")->applyFromArray([
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'd1d5db']]],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(22);
        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 12, 'B' => 8, 'C' => 8, 'D' => 12, 'E' => 45, 'F' => 38];
    }
}