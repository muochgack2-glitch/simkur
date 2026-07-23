<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Models\Subject;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Import extends Component
{
    use WithFileUploads;

    public $file;
    public $importType = 'guru'; // guru or siswa
    public $preview = [];
    public $errors = [];
    public $successCount = 0;
    public $errorCount = 0;
    public $isProcessing = false;

    public function updatedFile()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        $this->preview = [];
        $this->errors = [];

        try {
            $spreadsheet = IOFactory::load($this->file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Get headers (first row)
            $headers = array_shift($rows);

            // Preview first 5 rows
            $this->preview = array_slice($rows, 0, 5);
            
            session()->flash('info', 'File berhasil diupload. Silakan preview data sebelum import.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error membaca file: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $filename = $this->importType === 'guru' 
            ? 'template_import_guru.xlsx' 
            : 'template_import_siswa.xlsx';

        return response()->download(
            storage_path('app/templates/' . $filename)
        );
    }

    public function import()
    {
        if (!$this->file) {
            session()->flash('error', 'Silakan upload file terlebih dahulu.');
            return;
        }

        $this->isProcessing = true;
        $this->successCount = 0;
        $this->errorCount = 0;
        $this->errors = [];

        try {
            $spreadsheet = IOFactory::load($this->file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove headers
            array_shift($rows);

            if ($this->importType === 'guru') {
                $this->importTeachers($rows);
            } else {
                $this->importStudents($rows);
            }

            if ($this->errorCount > 0) {
                session()->flash('warning', "Import selesai dengan {$this->successCount} sukses dan {$this->errorCount} error.");
            } else {
                session()->flash('success', "Berhasil import {$this->successCount} data {$this->importType}!");
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Error saat import: ' . $e->getMessage());
        }

        $this->isProcessing = false;
    }

    private function importTeachers($rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because index 0 and header row

            // Skip empty rows
            if (empty($row[0]) && empty($row[1])) {
                continue;
            }

            try {
                // Validate required fields
                $validator = Validator::make([
                    'name' => $row[0] ?? null,
                    'username' => $row[1] ?? null,
                    'nip_nuptk' => $row[2] ?? null,
                    'email' => $row[3] ?? null,
                ], [
                    'name' => 'required|string|max:255',
                    'username' => 'required|string|max:255|unique:users,username',
                    'email' => 'nullable|email|unique:users,email',
                ]);

                if ($validator->fails()) {
                    $this->errors[] = "Baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    $this->errorCount++;
                    continue;
                }

                // Create user
                $user = User::create([
                    'name' => $row[0],
                    'username' => $row[1],
                    'nip_nuptk' => $row[2] ?: null,
                    'email' => $row[3] ?: null,
                    'password' => Hash::make('password'),
                    'role' => 'guru',
                    'beban_mengajar' => is_numeric($row[4]) ? $row[4] : null,
                    'is_active' => true,
                ]);

                // Parse and attach subjects if provided (column 5)
                if (!empty($row[5])) {
                    $subjectNames = array_map('trim', explode(',', $row[5]));
                    $subjects = Subject::whereIn('name', $subjectNames)->get();
                    if ($subjects->count() > 0) {
                        $user->subjects()->attach($subjects->pluck('id'));
                    }
                }

                // Parse taught majors if provided (column 6)
                if (!empty($row[6])) {
                    $majors = array_map('trim', explode(',', $row[6]));
                    $validMajors = array_intersect($majors, ['MPLB', 'AKL', 'BUSANA']);
                    if (!empty($validMajors)) {
                        $user->taught_majors = array_values($validMajors);
                        $user->save();
                    }
                }

                $this->successCount++;

            } catch (\Exception $e) {
                $this->errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                $this->errorCount++;
            }
        }
    }

    private function importStudents($rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            // Skip empty rows
            if (empty($row[0]) && empty($row[1])) {
                continue;
            }

            try {
                // Validate required fields
                $validator = Validator::make([
                    'name' => $row[0] ?? null,
                    'username' => $row[1] ?? null,
                    'nisn' => $row[2] ?? null,
                    'nis' => $row[3] ?? null,
                    'grade' => $row[6] ?? null,
                    'major' => $row[7] ?? null,
                ], [
                    'name' => 'required|string|max:255',
                    'username' => 'required|string|max:255|unique:users,username',
                    'nisn' => 'nullable|string|max:10|unique:users,nisn',
                    'nis' => 'nullable|string|max:10|unique:users,nis',
                    'grade' => 'required|in:X,XI,XII',
                    'major' => 'required|in:MPLB,AKL,BUSANA',
                ]);

                if ($validator->fails()) {
                    $this->errors[] = "Baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    $this->errorCount++;
                    continue;
                }

                // Create user
                User::create([
                    'name' => $row[0],
                    'username' => $row[1],
                    'nisn' => $row[2] ?: null,
                    'nis' => $row[3] ?: null,
                    'email' => $row[4] ?: null,
                    'no_hp' => $row[5] ?: null,
                    'grade' => $row[6],
                    'major' => $row[7],
                    'parent_name' => $row[8] ?: null,
                    'parent_phone' => $row[9] ?: null,
                    'password' => Hash::make('password'),
                    'role' => 'siswa',
                    'is_pkl' => strtolower($row[10] ?? '') === 'ya',
                    'is_teaching_factory' => strtolower($row[11] ?? '') === 'ya',
                    'is_active' => true,
                ]);

                $this->successCount++;

            } catch (\Exception $e) {
                $this->errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                $this->errorCount++;
            }
        }
    }

    #[Layout('components.layouts.app')]
    #[Title('Import Data Pengguna - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        return view('livewire.user.import');
    }
}
