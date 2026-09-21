<?php

namespace App\Exports;

use App\Models\Assessment;
use App\Models\SchoolClass;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NilaiTemplateExport implements
    FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    public function __construct(
        private Assessment $assessment,
        private SchoolClass $class
    ) {}

    public function collection()
    {
        $q = User::where('role', 'siswa')
            ->where('class_id', $this->class->id)
            ->orderBy('name');
        if (!empty($this->assessment->target_grades)) {
            $q->whereIn('grade', $this->assessment->target_grades);
        }
        if (!empty($this->assessment->target_majors)) {
            $q->whereIn('major', $this->assessment->target_majors);
        }
        if ($this->assessment->subject && $this->assessment->subject->agama_filter) {
            $q->where('agama', $this->assessment->subject->agama_filter);
        }
        return $q->get()->map(fn ($s, $i) => [
            'no'    => $i + 1,
            'nis'   => $s->nis ?? '-',
            'nama'  => $s->name,
            'nilai' => '',
        ]);
    }

    public function headings(): array
    {
        return ['NO', 'NIS', 'NAMA SISWA', 'NILAI (0-100)'];
    }

    public function title(): string { return 'Nilai'; }

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 18, 'C' => 36, 'D' => 16];
    }

    public function styles(Worksheet $sheet): array
    {
        // Info rows
        $sheet->insertNewRowBefore(1, 2);
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', 'Template Import Nilai — ' . $this->assessment->title);
        $sheet->getStyle('A1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEF2FF']],
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '3730A3']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->mergeCells('A2:D2');
        $sheet->setCellValue('A2', 'Kelas: ' . $this->class->name . ' | Jangan ubah kolom NO, NIS, dan NAMA SISWA');
        $sheet->getStyle('A2')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '92400E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        // Header row
        $sheet->getStyle('A3:D3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B:B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D:D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        return [];
    }
}