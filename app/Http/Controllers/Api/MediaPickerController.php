<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\FileUpload;
use App\Models\MediaFolder;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPickerController extends Controller
{
    use FileUpload;

    /**
     * A map of simple keywords to their corresponding MIME types.
     * This makes the component easy to use while keeping the logic on the backend.
     */
    private function getTypeMimeMap(): array
    {
        return [
            // General Keywords
            'image' => ['image/%'],
            'video' => ['video/%'],
            'audio' => ['audio/%'],
            'pdf'   => ['application/pdf'],

            // Document Group
            'document' => [
                'application/pdf',
                'application/msword', // .doc
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
                'application/vnd.oasis.opendocument.text', // .odt
                'text/plain', // .txt
                'text/rtf',   // .rtf
            ],

            // Spreadsheet Group
            'spreadsheet' => [
                'application/vnd.ms-excel', // .xls
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                'application/vnd.oasis.opendocument.spreadsheet', // .ods
                'text/csv', // .csv
            ],

            // Presentation Group
            'presentation' => [
                'application/vnd.ms-powerpoint', // .ppt
                'application/vnd.openxmlformats-officedocument.presentationml.presentation', // .pptx
                'application/vnd.oasis.opendocument.presentation', // .odp
            ],

            // Archive Group
            'archive' => [
                'application/zip',
                'application/x-rar-compressed',
                'application/x-7z-compressed',
                'application/gzip',
            ],

            // Vector Graphics
            'vector' => [
                'image/svg+xml', // .svg
                'application/postscript', // .ai, .eps
            ]
        ];
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'folder_id' => 'nullable|integer|exists:media_folders,id',
            'types' => 'nullable|array',
            'types.*' => 'string',
        ]);

        $currentFolderId = $validated['folder_id'] ?? null;
        $acceptedTypes = $validated['types'] ?? [];
        $typeMap = $this->getTypeMimeMap();

        $folders = MediaFolder::where('parent_id', $currentFolderId)->orderBy('name')->get();

        $mediaQuery = Media::where('folder_id', $currentFolderId)
            ->when(!empty($acceptedTypes), function ($query) use ($acceptedTypes, $typeMap) {
                $query->where(function ($q) use ($acceptedTypes, $typeMap) {
                    foreach ($acceptedTypes as $type) {
                        $type = strtolower(trim($type));

                        // 1. Check if it's a predefined keyword/group
                        if (array_key_exists($type, $typeMap)) {
                            foreach ($typeMap[$type] as $mime) {
                                $q->orWhere('mime_type', 'like', $mime);
                            }
                            continue;
                        }

                        // 2. Fallback for specific extensions if not a keyword
                        if (!str_contains($type, '/')) {
                            $extension = ltrim($type, '.');
                            $q->orWhere('file_name', 'like', '%.' . $extension);
                        }
                    }
                });
            })
            ->latest();

        $media = $mediaQuery->paginate(18)->withQueryString();

        // Transform media to include URLs
        $media->getCollection()->transform(function ($item) {
            $item->url = $item->getFullUrl();
            if ($item->hasGeneratedConversion('thumb')) {
                $item->thumb_url = $item->getFullUrl('thumb');
            } else {
                $item->thumb_url = $item->url; // Fallback to full URL if no thumb
            }
            return $item;
        });

        // Generate breadcrumbs
        $breadcrumbs = $this->generateBreadcrumbs($currentFolderId);

        // Return everything as a JSON response
        return response()->json([
            'folders' => $folders,
            'media' => $media,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function upload(Request $request)
    {
        return $this->handleFileUpload($request);
    }

    private function generateBreadcrumbs($folderId): array
    {
        $breadcrumbs = [];
        $current = $folderId ? MediaFolder::find($folderId) : null;

        while ($current) {
            array_unshift($breadcrumbs, [ 'id' => $current->id, 'name' => $current->name ]);
            $current = $current->parent;
        }

        array_unshift($breadcrumbs, ['id' => null, 'name' => 'Root']);
        return $breadcrumbs;
    }
}
