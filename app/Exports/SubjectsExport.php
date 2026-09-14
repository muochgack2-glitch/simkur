<?php

namespace App\Exports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SubjectsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected string $search;
    protected string $filterStatus;

    public function __construct(string $search = '', string $filterStatus = 'all')
    {
        $this->search       = $search;
        $this->filterStatus = $filterStatus;
    }

    public function query()
    {
        $query = Subject::with('teachers')->orderBy('name');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus === 'active') {
            $query->where('is_active', true);
        } elseif ($this->filterStatus === 'inactive') {
            $query->where('is_active', false);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode',
            'Nama Mata Pelajaran',
            'Deskripsi',
            'Jumlah Guru',
            'Nama Guru',
            'Status',
        ];
    }

    public function map($subject): array
    {
        static $no = 0;
        $no++;

        $teacherNames = $subject->teachers->pluck('name')->implode(', ');

        return [
            $no,
            $subject->code ?: '-',
            $subject->name,
            $subject->description ?: '-',
            $subject->teachers->count(),
            $teacherNames ?: '-',
            $subject->is_active ? 'Aktif' : 'Tidak Aktif',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Mata Pelajaran';
    }
}