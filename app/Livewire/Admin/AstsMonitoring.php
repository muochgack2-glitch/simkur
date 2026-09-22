<?php

namespace App\Livewire\Admin;

use App\Models\Assessment;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AstsMonitoring extends Component
{
    use WithFileUploads;

    public $file;
    public array $rows    = [];
    public string $error  = '';
    public bool $parsed   = false;

    // Filter
    public string $filterHari    = '';
    public string $filterKelas   = '';
    public string $filterJurusan = '';
    public string $filterStatus  = '';

    public function updatedFile(): void
    {
        $this->error  = '';
        $this->parsed = false;
        $this->rows   = [];

        if (!$this->file) return;

        try {
            $path = $this->file->getRealPath();
            $spreadsheet = IOFactory::load($path);
            $sheet       = $spreadsheet->getActiveSheet();
            $data        = $sheet->toArray(null, true, true, false);

            // Hapus header (baris 1)
            array_shift($data);

            $results = [];
            foreach ($data as $row) {
                $hari    = trim((string) ($row[0] ?? ''));
                $sesi    = trim((string) ($row[1] ?? ''));
                $kelas   = trim((string) ($row[2] ?? ''));
                $jurusan = trim((string) ($row[3] ?? ''));
                $mapel   = trim((string) ($row[4] ?? ''));
                $guru    = trim((string) ($row[5] ?? ''));

                if (!$hari || !$mapel || !$guru) continue;

                // Cari guru by name (fuzzy — ambil kata pertama+terakhir)
                $guruUser = User::where('name', 'LIKE', '%' . explode(',', $guru)[0] . '%')
                               ->whereIn('role', ['guru', 'admin', 'waka_kurikulum'])
                               ->first();

                // Cari assessment ASTS milik guru ini untuk mapel ini
                $assessment = null;
                $qCount     = 0;

                if ($guruUser) {
                    $assessment = Assessment::where('teacher_id', $guruUser->id)
                        ->whereHas('assessmentLabel', fn($q) => $q->where('name', 'LIKE', '%ASTS%'))
                        ->where(function ($q) use ($mapel) {
                            $q->whereHas('subject', fn($s) => $s->where('name', 'LIKE', '%' . $this->simplifyMapel($mapel) . '%'))
                              ->orWhere('title', 'LIKE', '%' . $this->simplifyMapel($mapel) . '%');
                        })
                        ->first();

                    if ($assessment) {
                        $qCount = $assessment->questions()->count();
                    }
                }

                // Status
                if (!$guruUser) {
                    $status = 'not_found';   // guru tidak ada di sistem
                } elseif (!$assessment) {
                    $status = 'no_assessment'; // assessment belum dibuat
                } elseif ($qCount === 0) {
                    $status = 'no_questions';  // assessment ada tapi 0 soal
                } else {
                    $status = 'ok';            // lengkap
                }

                $results[] = [
                    'hari'          => $hari,
                    'sesi'          => $sesi,
                    'kelas'         => $kelas,
                    'jurusan'       => $jurusan,
                    'mapel'         => $mapel,
                    'guru_input'    => $guru,
                    'guru_sistem'   => $guruUser?->name ?? '-',
                    'assessment_id' => $assessment?->id,
                    'q_count'       => $qCount,
                    'status'        => $status,
                ];
            }

            $this->rows   = $results;
            $this->parsed = true;

        } catch (\Throwable $e) {
            $this->error = 'Gagal membaca file: ' . $e->getMessage();
        }
    }

    private function simplifyMapel(string $name): string
    {
        // Ambil 2 kata pertama agar fuzzy match lebih fleksibel
        $words = preg_split('/\s+/', trim($name));
        return implode(' ', array_slice($words, 0, 2));
    }

    public function getFilteredRowsProperty(): array
    {
        return array_filter($this->rows, function ($r) {
            if ($this->filterHari    && $r['hari']    !== $this->filterHari)    return false;
            if ($this->filterKelas   && $r['kelas']   !== $this->filterKelas)   return false;
            if ($this->filterJurusan && $r['jurusan'] !== $this->filterJurusan) return false;
            if ($this->filterStatus  && $r['status']  !== $this->filterStatus)  return false;
            return true;
        });
    }

    public function getSummaryProperty(): array
    {
        $ok    = count(array_filter($this->rows, fn($r) => $r['status'] === 'ok'));
        $noQ   = count(array_filter($this->rows, fn($r) => $r['status'] === 'no_questions'));
        $noA   = count(array_filter($this->rows, fn($r) => $r['status'] === 'no_assessment'));
        $notF  = count(array_filter($this->rows, fn($r) => $r['status'] === 'not_found'));
        return compact('ok', 'noQ', 'noA', 'notF');
    }

    public function render()
    {
        return view('livewire.admin.asts-monitoring')
            ->layout('components.layouts.app');
    }
}