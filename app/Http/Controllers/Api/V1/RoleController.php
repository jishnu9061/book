<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends BaseApiController
{
    public function index(Request $request)
    {
        [$perPage, $page] = $this->getPaginationParams($request);

        $query = Role::query();

        $filters = $request->only(['search']);
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('slug', 'like', '%' . $filters['search'] . '%');
        }

        $allowedSorts = ['id', 'name', 'slug', 'created_at'];
        $query = $this->applySorting($query, $request, $allowedSorts, 'name');

        $roles = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($roles);
    }

    public function store(Request $request)
    {
        $requestData = $request->validate([
            'name' => ['required', 'max:50'],
            'slug' => ['required', 'max:50'],
            'access' => ['required', 'array'],
        ]);

        $accessData = [];
        foreach ($requestData['access'] as $ak => $av) {
            $accessData[$ak] = [
                'read' => filter_var($av['read'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'update' => filter_var($av['update'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'create' => filter_var($av['create'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'delete' => filter_var($av['delete'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ];
        }

        $role = Role::create([
            'access' => json_encode($accessData),
            'slug' => $requestData['slug'],
            'name' => $requestData['name'],
        ]);

        return $this->successResponse($role, 201);
    }

    public function show($id)
    {
        $role = Role::find($id);

        if (empty($role)) {
            return $this->notFoundResponse('Role not found');
        }

        return $this->successResponse($role);
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        if (empty($role)) {
            return $this->notFoundResponse('Role not found');
        }

        $requestData = $request->validate([
            'name' => ['sometimes', 'required', 'max:50'],
            'slug' => ['sometimes', 'required', 'max:50'],
            'access' => ['sometimes', 'required', 'array'],
        ]);

        if (isset($requestData['access'])) {
            $accessData = [];
            foreach ($requestData['access'] as $ak => $av) {
                $accessData[$ak] = [
                    'read' => filter_var($av['read'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'update' => filter_var($av['update'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'create' => filter_var($av['create'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'delete' => filter_var($av['delete'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ];
            }
            $requestData['access'] = json_encode($accessData);
        }

        $role->update($requestData);

        return $this->successResponse($role);
    }

    public function destroy($id)
    {
        $role = Role::find($id);

        if (empty($role)) {
            return $this->notFoundResponse('Role not found');
        }

        $role->delete();

        return $this->successResponse(null, 204);
    }
}

