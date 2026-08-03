<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckTeacherNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:teacher-names {--txt-file=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare teacher names from TXT file with database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking Teacher Names');
        $this->info('');

        // Get teachers from database
        $dbTeachers = User::where(function($q) {
                $q->where('role', 'Guru')
                  ->orWhere('role', 'guru')
                  ->orWhere('role', 'LIKE', '%guru%');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'role']);

        $this->info("📊 Found {$dbTeachers->count()} teachers in database:");
        $this->info('');
        
        $this->table(
            ['ID', 'Name', 'Role'],
            $dbTeachers->map(fn($t) => [$t->id, $t->name, $t->role ?? 'NULL'])->toArray()
        );

        // Parse TXT file if provided
        $txtFile = $this->option('txt-file') ?? base_path('Jadwal_Guru_Terintegrasi_FIX.txt');
        
        if (!file_exists($txtFile)) {
            $this->warn('');
            $this->warn("⚠️  TXT file not found: {$txtFile}");
            $this->info('Run with: php artisan check:teacher-names --txt-file=/path/to/file.txt');
            return;
        }

        $this->info('');
        $this->info('📄 Parsing TXT file...');
        
        $content = file_get_contents($txtFile);
        $txtTeachers = $this->parseTeacherNames($content);
        
        $this->info("Found {$txtTeachers->count()} teachers in TXT file");
        $this->info('');

        // Compare
        $this->info('🔄 Matching Results:');
        $this->info('');

        $matched = collect();
        $notFound = collect();

        foreach ($txtTeachers as $txtTeacher) {
            $cleanName = $this->cleanTeacherName($txtTeacher);
            
            // Try to find in database
            $found = $dbTeachers->first(function($dbTeacher) use ($cleanName) {
                return stripos($dbTeacher->name, $cleanName) !== false || 
                       stripos($cleanName, $dbTeacher->name) !== false;
            });

            if ($found) {
                $matched->push([
                    'txt' => $txtTeacher,
                    'db' => $found->name,
                    'id' => $found->id,
                ]);
                $this->line("✅ <info>{$txtTeacher}</info> → <comment>{$found->name}</comment> (ID: {$found->id})");
            } else {
                $notFound->push($txtTeacher);
                $this->line("❌ <error>{$txtTeacher}</error> → NOT FOUND");
            }
        }

        // Summary
        $this->info('');
        $this->info('📈 Summary:');
        $this->info("   ✅ Matched: {$matched->count()}");
        $this->info("   ❌ Not Found: {$notFound->count()}");

        if ($notFound->isNotEmpty()) {
            $this->warn('');
            $this->warn('⚠️  Teachers NOT FOUND in database:');
            foreach ($notFound as $name) {
                $cleanName = $this->cleanTeacherName($name);
                $this->error("   - {$name}");
                $this->line("     Cleaned: {$cleanName}");
            }
            
            $this->info('');
            $this->info('💡 Suggestions:');
            $this->info('   1. Add these teachers to database manually');
            $this->info('   2. Check if names have typos in TXT file or database');
            $this->info('   3. Update seeder to create missing teachers automatically');
        }

        // Export mapping to JSON for seeder reference
        $mappingFile = base_path('teacher-name-mapping.json');
        file_put_contents($mappingFile, json_encode([
            'matched' => $matched->toArray(),
            'not_found' => $notFound->toArray(),
            'generated_at' => now()->toDateTimeString(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info('');
        $this->info("📁 Mapping exported to: {$mappingFile}");
    }

    /**
     * Parse teacher names from TXT content
     */
    private function parseTeacherNames(string $content): \Illuminate\Support\Collection
    {
        $teachers = collect();
        $lines = explode("\n", $content);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Teacher line (starts with number)
            if (preg_match('/^(\d+)\.\s+(.+)$/', $line, $matches)) {
                $teachers->push(trim($matches[2]));
            }
        }
        
        return $teachers->unique()->sort()->values();
    }

    /**
     * Clean teacher name (remove titles)
     */
    private function cleanTeacherName(string $name): string
    {
        // Remove titles
        $name = preg_replace('/^(Drs\.|Dr\.|S\.Pd\.|S\.E\.|S\.Kom\.|A\.Md\.|S\.\s*Pd\.\s*I\.|S\.\s*Pd\.\s*B\.)/', '', $name);
        $name = preg_replace('/(S\.Pd\.|S\.E\.|S\.Kom\.|A\.Md\.|S\.\s*Pd\.\s*I\.|S\.\s*Pd\.\s*B\.)$/', '', $name);
        $name = trim($name);
        $name = preg_replace('/\s+/', ' ', $name);
        $name = rtrim($name, '.');
        
        return $name;
    }
}
