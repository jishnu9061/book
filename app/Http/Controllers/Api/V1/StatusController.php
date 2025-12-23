<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StatusController extends BaseApiController
{
    public function index(Request $request)
    {
        [$perPage, $page] = $this->getPaginationParams($request);

        $query = Status::query();

        $filters = $request->only(['search']);
        $query = $query->filter($filters);

        $allowedSorts = ['id', 'name', 'slug', 'created_at'];
        $query = $this->applySorting($query, $request, $allowedSorts, 'name');

        $statuses = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($statuses);
    }

    public function store(Request $request)
    {
        $requestData = $request->validate([
            'name' => ['required', 'max:50'],
        ]);

        $slug = strtolower(preg_replace('/\s+/', '_', $requestData['name']));

        $status = Status::create([
            'name' => $requestData['name'],
            'slug' => $slug,
        ]);

        return $this->successResponse($status, 201);
    }

    public function show($id)
    {
        $status = Status::find($id);

        if (empty($status)) {
            return $this->notFoundResponse('Status not found');
        }

        return $this->successResponse($status);
    }

    public function update(Request $request, $id)
    {
        $status = Status::find($id);

        if (empty($status)) {
            return $this->notFoundResponse('Status not found');
        }

        $requestData = $request->validate([
            'name' => ['sometimes', 'required', 'max:50'],
            'slug' => ['sometimes', 'required', 'max:50'],
        ]);

        $status->update($requestData);

        return $this->successResponse($status);
    }

    public function destroy($id)
    {
        $status = Status::find($id);

        if (empty($status)) {
            return $this->notFoundResponse('Status not found');
        }

        $status->delete();

        return $this->successResponse(null, 204);
    }

    public function restore($id)
    {
        $status = Status::withTrashed()->find($id);

        if (empty($status)) {
            return $this->notFoundResponse('Status not found');
        }

        $status->restore();

        return $this->successResponse($status);
    }
}

