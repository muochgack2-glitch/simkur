<?php

namespace Database\Seeders;

use App\Models\TeachingMaterial;
use App\Models\TeachingMaterialAttachment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TestAttachmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first material
        $material = TeachingMaterial::first();
        
        if (!$material) {
            $this->command->error('No teaching materials found. Please create a material first.');
            return;
        }

        $this->command->info("Testing attachments for material: {$material->title}");

        // Create test attachments
        $attachments = [
            [
                'teaching_material_id' => $material->id,
                'file_name' => 'LKPD_Pertemuan_1.pdf',
                'file_path' => 'teaching-materials/' . $material->id . '/attachments/lkpd_test.pdf',
                'file_type' => 'pdf',
                'file_size' => 1024000, // 1MB
                'attachment_type' => 'lkpd',
                'is_primary' => false,
                'description' => 'LKPD untuk pertemuan pertama',
                'uploaded_by' => $material->created_by,
            ],
            [
                'teaching_material_id' => $material->id,
                'file_name' => 'Presentasi_Materi.pptx',
                'file_path' => 'teaching-materials/' . $material->id . '/attachments/presentasi_test.pptx',
                'file_type' => 'pptx',
                'file_size' => 2048000, // 2MB
                'attachment_type' => 'presentation',
                'is_primary' => false,
                'description' => 'Slide presentasi untuk pembelajaran',
                'uploaded_by' => $material->created_by,
            ],
            [
                'teaching_material_id' => $material->id,
                'file_name' => 'Video Tutorial',
                'file_type' => 'link',
                'external_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'attachment_type' => 'video',
                'is_primary' => false,
                'description' => 'Video tutorial dari YouTube',
                'uploaded_by' => $material->created_by,
            ],
        ];

        foreach ($attachments as $attachmentData) {
            TeachingMaterialAttachment::create($attachmentData);
        }

        $this->command->info('✅ Created 3 test attachments:');
        $this->command->info('   1. LKPD (PDF file)');
        $this->command->info('   2. Presentasi (PPTX file)');
        $this->command->info('   3. Video (YouTube link)');
        $this->command->info('');
        $this->command->info('📝 Now visit: /teaching-materials/' . $material->id);
        $this->command->info('   You should see "📎 LAMPIRAN (3)" section');
    }
}
