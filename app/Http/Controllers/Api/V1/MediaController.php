<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends BaseApiController
{
    public function index(Request $request)
    {
        [$perPage, $page] = $this->getPaginationParams($request);

        $query = Attachment::query();

        $allowedSorts = ['id', 'name', 'created_at'];
        $query = $this->applySorting($query, $request, $allowedSorts, 'created_at');

        $media = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($media);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file'],
            'ticket_id' => ['nullable', 'exists:tickets,id'],
        ]);

        $file = $request->file('file');
        $filePath = $file->store('media', ['disk' => 'file_uploads']);

        $attachment = Attachment::create([
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'path' => $filePath,
            'ticket_id' => $request->input('ticket_id'),
        ]);

        return $this->successResponse($attachment, 201);
    }

    public function show($id)
    {
        $media = Attachment::find($id);

        if (empty($media)) {
            return $this->notFoundResponse('Media not found');
        }

        return $this->successResponse($media);
    }

    public function update(Request $request, $id)
    {
        $media = Attachment::find($id);

        if (empty($media)) {
            return $this->notFoundResponse('Media not found');
        }

        $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'ticket_id' => ['sometimes', 'nullable', 'exists:tickets,id'],
        ]);

        $media->update($request->only(['name', 'ticket_id']));

        return $this->successResponse($media);
    }

    public function destroy($id)
    {
        $media = Attachment::find($id);

        if (empty($media)) {
            return $this->notFoundResponse('Media not found');
        }

        // Delete file from storage
        if (Storage::disk('file_uploads')->exists($media->path)) {
            Storage::disk('file_uploads')->delete($media->path);
        }

        $media->delete();

        return $this->successResponse(null, 204);
    }
}

