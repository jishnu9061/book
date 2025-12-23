<?php

namespace App\Http\Traits;

use App\Models\MediaFolder;
use App\Services\EmailNotificationService;
use App\Mail\Media\FileUploadedEmail;
use App\Mail\Media\LargeFileUploadedEmail;
use Illuminate\Http\Request;

trait FileUpload
{
    public function handleFileUpload(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240',
            'folder_id' => 'nullable|integer|exists:media_folders,id',
        ]);

        $folderId = $validated['folder_id'] ?? null;

        // --- THIS IS THE KEY CHANGE ---
        // Instead of a new instance, find the persistent "System" folder with ID=1.
        $ownerModel = MediaFolder::find(1);
        // -----------------------------

        $media = $ownerModel
            ->addMediaFromRequest('file')
            ->toMediaCollection('default');

        // Manually associate the file with the visual folder selected in the UI
        $media->folder_id = $folderId;
        $media->save();

        // Send email notifications
        $uploader = auth()->user();
        if ($uploader) {
            $folderName = $folderId ? MediaFolder::find($folderId)?->name : null;
            
            // File uploaded notification
            $this->emailService->sendToUser(
                $uploader,
                'file_uploaded',
                new FileUploadedEmail($media, $uploader, $folderName),
                ['rate_limit' => 1, 'decay_minutes' => 60]
            );

            // Large file notification (if file exceeds 5MB)
            if ($media->size > 5 * 1024 * 1024) {
                $this->emailService->sendToUser(
                    $uploader,
                    'large_file_uploaded',
                    new LargeFileUploadedEmail($media, $uploader, 5 * 1024 * 1024, $folderName),
                    ['rate_limit' => 1, 'decay_minutes' => 60]
                );
            }
        }

        return response()->json($media, 201);
    }
}
