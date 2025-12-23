<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends BaseApiController
{
    public function index(Request $request)
    {
        [$perPage, $page] = $this->getPaginationParams($request);

        $query = Department::query();

        $filters = $request->only(['search']);
        $query = $query->filter($filters);

        $allowedSorts = ['id', 'name', 'created_at'];
        $query = $this->applySorting($query, $request, $allowedSorts, 'name');

        $departments = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($departments);
    }

    public function store(Request $request)
    {
        $requestData = $request->validate([
            'name' => ['required', 'max:100'],
        ]);

        $department = Department::create($requestData);

        return $this->successResponse($department, 201);
    }

    public function show($id)
    {
        $department = Department::find($id);

        if (empty($department)) {
            return $this->notFoundResponse('Department not found');
        }

        return $this->successResponse($department);
    }

    public function update(Request $request, $id)
    {
        $department = Department::find($id);

        if (empty($department)) {
            return $this->notFoundResponse('Department not found');
        }

        $requestData = $request->validate([
            'name' => ['sometimes', 'required', 'max:100'],
        ]);

        $department->update($requestData);

        return $this->successResponse($department);
    }

    public function destroy($id)
    {
        $department = Department::find($id);

        if (empty($department)) {
            return $this->notFoundResponse('Department not found');
        }

        $department->delete();

        return $this->successResponse(null, 204);
    }

    public function restore($id)
    {
        $department = Department::withTrashed()->find($id);

        if (empty($department)) {
            return $this->notFoundResponse('Department not found');
        }

        $department->restore();

        return $this->successResponse($department);
    }
}

