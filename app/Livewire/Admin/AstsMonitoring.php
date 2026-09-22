<?php

namespace App\Livewire\Admin;

use App\Models\AstsSchedule;
use App\Models\Assessment;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AstsMonitoring extends Component
{
    use WithFileUploads;

    // Re-import Excel (opsional — untuk update jadwal)
    public $file;
    public string $importMsg = '';
    public bool $showImport  = false;

    // Filter
    public string $filterHari    = '';
    public string $filterKelas   = '';
    public string $filterJurusan = '';
    public string $filterStatus  = '';

    // ── Proses Excel re-import ────────────────────────────────────────────
    public function updatedFile(): void
    {
        $this->importMsg = '';
        if (!$this->file) return;

        try {
            $path        = $this->file->getRealPath();
            $spreadsheet = IOFactory::load($path);
            $sheet       = $spreadsheet->getActiveSheet();
            $data        = $sheet->toArray(null, true, true, false);
            array_shift($data); // hapus header

            $count = 0;
            foreach ($data as $row) {
                $hari    = trim((string) ($row[0] ?? ''));
                $sesi    = (int)   ($row[1] ?? 0);
                $kelas   = trim((string) ($row[2] ?? ''));
                $jurusan = trim((string) ($row[3] ?? ''));
                $mapel   = trim((string) ($row[4] ?? ''));
                $guru    = trim((string) ($row[5] ?? ''));
                if (!$hari || !$mapel || !$guru) continue;

                AstsSchedule::updateOrCreate(
                    ['kelas'=>$kelas,'jurusan'=>$jurusan,'hari'=>$hari,'sesi'=>$sesi],
                    ['mapel'=>$mapel,'nama_guru'=>$guru]
                );
                $count++;
            }
            $this->importMsg = "✅ {$count} baris berhasil diperbarui dari Excel.";
        } catch (\Throwable $e) {
            $this->importMsg = '❌ Gagal: ' . $e->getMessage();
        }
    }

    // ── Computed: baca DB + cross-check assessment ────────────────────────
    public function getRowsProperty(): array
    {
        $schedules = AstsSchedule::orderByRaw("FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat')")
            ->orderBy('sesi')
            ->get();

        $results = [];
        foreach ($schedules as $s) {
            $guruUser = User::where('name', 'LIKE', '%' . $this->guruKeyword($s->nama_guru) . '%')
                           ->whereIn('role', ['guru','admin','waka_kurikulum'])
                           ->first();

            $assessment = null;
            $qCount     = 0;

            if ($guruUser) {
                $assessment = Assessment::where('teacher_id', $guruUser->id)
                    ->whereHas('assessmentLabel', fn($q) => $q->where('name','LIKE','%ASTS%'))
                    ->where(function ($q) use ($s) {
                        $kw = $this->mapelKeyword($s->mapel);
                        $q->whereHas('subject', fn($sq) => $sq->where('name','LIKE',"%{$kw}%"))
                          ->orWhere('title','LIKE',"%{$kw}%");
                    })
                    ->first();
                if ($assessment) {
                    $qCount = $assessment->questions()->count();
                }
            }

            $status = match(true) {
                !$guruUser   => 'not_found',
                !$assessment => 'no_assessment',
                $qCount === 0 => 'no_questions',
                default       => 'ok',
            };

            $results[] = [
                'id'            => $s->id,
                'hari'          => $s->hari,
                'sesi'          => $s->sesi,
                'kelas'         => $s->kelas,
                'jurusan'       => $s->jurusan,
                'mapel'         => $s->mapel,
                'guru_input'    => $s->nama_guru,
                'guru_sistem'   => $guruUser?->name ?? '-',
                'assessment_id' => $assessment?->id,
                'q_count'       => $qCount,
                'status'        => $status,
            ];
        }
        return $results;
    }

    public function getFilteredRowsProperty(): array
    {
        return array_values(array_filter($this->rows, function ($r) {
            if ($this->filterHari    && $r['hari']    !== $this->filterHari)    return false;
            if ($this->filterKelas   && $r['kelas']   !== $this->filterKelas)   return false;
            if ($this->filterJurusan && $r['jurusan'] !== $this->filterJurusan) return false;
            if ($this->filterStatus  && $r['status']  !== $this->filterStatus)  return false;
            return true;
        }));
    }

    public function getSummaryProperty(): array
    {
        $rows  = $this->rows;
        $ok    = count(array_filter($rows, fn($r) => $r['status'] === 'ok'));
        $noQ   = count(array_filter($rows, fn($r) => $r['status'] === 'no_questions'));
        $noA   = count(array_filter($rows, fn($r) => $r['status'] === 'no_assessment'));
        $notF  = count(array_filter($rows, fn($r) => $r['status'] === 'not_found'));
        return compact('ok','noQ','noA','notF');
    }

    public function resetFilter(): void
    {
        $this->filterHari = $this->filterKelas = $this->filterJurusan = $this->filterStatus = '';
    }

    private function guruKeyword(string $name): string
    {
        // Ambil nama depan saja untuk fuzzy match
        return explode(',', $name)[0];
    }

    private function mapelKeyword(string $name): string
    {
        $words = preg_split('/\s+/', trim($name));
        return implode(' ', array_slice($words, 0, 2));
    }

    public function render()
    {
        return view('livewire.admin.asts-monitoring')
            ->layout('components.layouts.app');
    }
}