<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Priority;
use Illuminate\Http\Request;

class PriorityController extends BaseApiController
{
    public function index(Request $request)
    {
        [$perPage, $page] = $this->getPaginationParams($request);

        $query = Priority::query();

        $filters = $request->only(['search']);
        $query = $query->filter($filters);

        $allowedSorts = ['id', 'name', 'created_at'];
        $query = $this->applySorting($query, $request, $allowedSorts, 'name');

        $priorities = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($priorities);
    }

    public function store(Request $request)
    {
        $requestData = $request->validate([
            'name' => ['required', 'max:100'],
        ]);

        $priority = Priority::create($requestData);

        return $this->successResponse($priority, 201);
    }

    public function show($id)
    {
        $priority = Priority::find($id);

        if (empty($priority)) {
            return $this->notFoundResponse('Priority not found');
        }

        return $this->successResponse($priority);
    }

    public function update(Request $request, $id)
    {
        $priority = Priority::find($id);

        if (empty($priority)) {
            return $this->notFoundResponse('Priority not found');
        }

        $requestData = $request->validate([
            'name' => ['sometimes', 'required', 'max:100'],
        ]);

        $priority->update($requestData);

        return $this->successResponse($priority);
    }

    public function destroy($id)
    {
        $priority = Priority::find($id);

        if (empty($priority)) {
            return $this->notFoundResponse('Priority not found');
        }

        $priority->delete();

        return $this->successResponse(null, 204);
    }

    public function restore($id)
    {
        $priority = Priority::withTrashed()->find($id);

        if (empty($priority)) {
            return $this->notFoundResponse('Priority not found');
        }

        $priority->restore();

        return $this->successResponse($priority);
    }
}

