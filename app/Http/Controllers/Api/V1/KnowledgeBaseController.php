<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\KnowledgeBase;
use Illuminate\Http\Request;

class KnowledgeBaseController extends BaseApiController
{
    public function index(Request $request)
    {
        [$perPage, $page] = $this->getPaginationParams($request);

        $query = KnowledgeBase::query()
            ->with('type');

        $filters = $request->only(['search']);
        $query = $query->filter($filters);

        $allowedSorts = ['id', 'title', 'created_at'];
        $query = $this->applySorting($query, $request, $allowedSorts, 'title');

        $knowledgeBase = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($knowledgeBase);
    }

    public function store(Request $request)
    {
        $requestData = $request->validate([
            'title' => ['required', 'max:150'],
            'type_id' => ['required', 'exists:types,id'],
            'details' => ['required', 'string'],
        ]);

        $knowledgeBase = KnowledgeBase::create($requestData);

        return $this->successResponse($knowledgeBase->load('type'), 201);
    }

    public function show($id)
    {
        $knowledgeBase = KnowledgeBase::with('type')->find($id);

        if (empty($knowledgeBase)) {
            return $this->notFoundResponse('Knowledge base article not found');
        }

        return $this->successResponse($knowledgeBase);
    }

    public function update(Request $request, $id)
    {
        $knowledgeBase = KnowledgeBase::find($id);

        if (empty($knowledgeBase)) {
            return $this->notFoundResponse('Knowledge base article not found');
        }

        $requestData = $request->validate([
            'title' => ['sometimes', 'required', 'max:150'],
            'type_id' => ['sometimes', 'required', 'exists:types,id'],
            'details' => ['sometimes', 'required', 'string'],
        ]);

        $knowledgeBase->update($requestData);

        return $this->successResponse($knowledgeBase->load('type'));
    }

    public function destroy($id)
    {
        $knowledgeBase = KnowledgeBase::find($id);

        if (empty($knowledgeBase)) {
            return $this->notFoundResponse('Knowledge base article not found');
        }

        $knowledgeBase->delete();

        return $this->successResponse(null, 204);
    }
}

