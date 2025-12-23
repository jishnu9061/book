<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Type;
use Illuminate\Http\Request;

class TypeController extends BaseApiController
{
    public function index(Request $request)
    {
        [$perPage, $page] = $this->getPaginationParams($request);

        $query = Type::query();

        $filters = $request->only(['search']);
        $query = $query->filter($filters);

        $allowedSorts = ['id', 'name', 'created_at'];
        $query = $this->applySorting($query, $request, $allowedSorts, 'name');

        $types = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($types);
    }

    public function store(Request $request)
    {
        $requestData = $request->validate([
            'name' => ['required', 'max:100'],
        ]);

        $type = Type::create($requestData);

        return $this->successResponse($type, 201);
    }

    public function show($id)
    {
        $type = Type::find($id);

        if (empty($type)) {
            return $this->notFoundResponse('Type not found');
        }

        return $this->successResponse($type);
    }

    public function update(Request $request, $id)
    {
        $type = Type::find($id);

        if (empty($type)) {
            return $this->notFoundResponse('Type not found');
        }

        $requestData = $request->validate([
            'name' => ['sometimes', 'required', 'max:100'],
        ]);

        $type->update($requestData);

        return $this->successResponse($type);
    }

    public function destroy($id)
    {
        $type = Type::find($id);

        if (empty($type)) {
            return $this->notFoundResponse('Type not found');
        }

        $type->delete();

        return $this->successResponse(null, 204);
    }

    public function restore($id)
    {
        $type = Type::withTrashed()->find($id);

        if (empty($type)) {
            return $this->notFoundResponse('Type not found');
        }

        $type->restore();

        return $this->successResponse($type);
    }
}

