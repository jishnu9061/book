<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends BaseApiController
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        [$perPage, $page] = $this->getPaginationParams($request);

        $query = Note::query()
            ->byUser($userId);

        $filters = $request->only(['search']);
        $query = $query->filter($filters);

        $allowedSorts = ['id', 'name', 'created_at'];
        $query = $this->applySorting($query, $request, $allowedSorts, 'name');

        $notes = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($notes);
    }

    public function store(Request $request)
    {
        $requestData = $request->validate([
            'name' => ['required', 'max:100'],
            'details' => ['required', 'string'],
            'color' => ['nullable', 'string', 'max:50'],
        ]);

        $requestData['user_id'] = Auth::id();

        $note = Note::create($requestData);

        return $this->successResponse($note, 201);
    }

    public function show($id)
    {
        $userId = Auth::id();
        $note = Note::byUser($userId)->find($id);

        if (empty($note)) {
            return $this->notFoundResponse('Note not found');
        }

        return $this->successResponse($note);
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        $note = Note::byUser($userId)->find($id);

        if (empty($note)) {
            return $this->notFoundResponse('Note not found');
        }

        $requestData = $request->validate([
            'name' => ['sometimes', 'required', 'max:100'],
            'details' => ['sometimes', 'required', 'string'],
            'color' => ['sometimes', 'nullable', 'string', 'max:50'],
        ]);

        $note->update($requestData);

        return $this->successResponse($note);
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $note = Note::byUser($userId)->find($id);

        if (empty($note)) {
            return $this->notFoundResponse('Note not found');
        }

        $note->delete();

        return $this->successResponse(null, 204);
    }
}

