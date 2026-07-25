<?php

namespace App\Http\Controllers;

use App\Models\TeachingMaterial;
use App\Models\TeachingMaterialAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class TeachingMaterialController extends Controller
{
    /**
     * Download teaching material file (legacy - main file)
     */
    public function download($id)
    {
        $material = TeachingMaterial::findOrFail($id);
        $user = auth()->user();

        // Check if user can access this material using User model method
        if (!$user->canAccessMaterial($material)) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh file ini.');
        }

        // Check if file exists
        if (!$material->file_path) {
            abort(404, 'File tidak ditemukan.');
        }

        if (!Storage::disk('public')->exists($material->file_path)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        // Increment download count
        $material->incrementDownloadCount();

        // Get original filename
        $originalName = basename($material->file_path);
        
        // Download file
        return Storage::disk('public')->download($material->file_path, $originalName);
    }

    /**
     * Download individual attachment
     */
    public function downloadAttachment($materialId, $attachmentId)
    {
        $material = TeachingMaterial::findOrFail($materialId);
        $attachment = TeachingMaterialAttachment::where('teaching_material_id', $materialId)
            ->where('id', $attachmentId)
            ->firstOrFail();

        $user = auth()->user();

        // Check if user can access this material
        if (!$user->canAccessMaterial($material)) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh file ini.');
        }

        // Check if it's a link
        if ($attachment->isLink()) {
            // Redirect to external link
            $attachment->incrementDownloadCount();
            return redirect($attachment->external_link);
        }

        // Check if file exists
        if (!$attachment->file_path || !Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        // Increment download count
        $attachment->incrementDownloadCount();

        // Download file
        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    /**
     * Download all attachments as ZIP
     */
    public function downloadAllAttachments($materialId)
    {
        $material = TeachingMaterial::with('attachments')->findOrFail($materialId);
        $user = auth()->user();

        // Check if user can access this material
        if (!$user->canAccessMaterial($material)) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh file ini.');
        }

        // Check if has attachments
        if (!$material->hasAttachments()) {
            abort(404, 'Tidak ada file untuk diunduh.');
        }

        // Create temporary ZIP file
        $zipFileName = 'perangkat_ajar_' . $material->id . '_' . time() . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive();
        
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            abort(500, 'Tidak dapat membuat file ZIP.');
        }

        // Add files to ZIP (skip links)
        foreach ($material->attachments as $attachment) {
            if ($attachment->isFile() && Storage::disk('public')->exists($attachment->file_path)) {
                $filePath = Storage::disk('public')->path($attachment->file_path);
                $zip->addFile($filePath, $attachment->file_name);
            }
        }

        $zip->close();

        // Increment download count for all attachments
        foreach ($material->attachments as $attachment) {
            $attachment->incrementDownloadCount();
        }

        // Download and delete temp file
        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }
    
    /**
     * Preview file (for inline viewing)
     */
    public function preview(Request $request)
    {
        $path = base64_decode($request->get('path'));
        
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }
        
        // Get file extension
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        // Set appropriate content type
        $contentTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg' => 'video/ogg',
            // Office files
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        
        $contentType = $contentTypes[$extension] ?? 'application/octet-stream';
        
        return response()->file(Storage::disk('public')->path($path), [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
        ]);
    }
}
