<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends BaseApiController
{
    /**
     * Display a listing of organizations.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        [$perPage, $page] = $this->getPaginationParams($request);

        $query = Organization::query()
            ->orderBy('name');

        // Apply filters
        $filters = $request->only(['search']);
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                ->orWhere('phone', 'like', '%' . $filters['search'] . '%');
        }

        // Apply sorting
        $allowedSorts = ['id', 'name', 'email', 'created_at'];
        $query = $this->applySorting($query, $request, $allowedSorts, 'name');

        $organizations = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($organizations, [
            'filters' => $filters,
        ]);
    }

    /**
     * Store a newly created organization.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $requestData = $request->validate([
            'name' => ['required', 'max:100'],
            'email' => ['nullable', 'max:50', 'email'],
            'phone' => ['nullable', 'max:50'],
            'address' => ['nullable', 'max:150'],
            'city' => ['nullable', 'max:50'],
            'region' => ['nullable', 'max:50'],
            'country' => ['nullable', 'max:2'],
            'postal_code' => ['nullable', 'max:25'],
        ]);

        $organization = Organization::create($requestData);

        return $this->successResponse($organization, 201);
    }

    /**
     * Display the specified organization.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $organization = Organization::with('contacts')->find($id);

        if (empty($organization)) {
            return $this->notFoundResponse('Organization not found');
        }

        return $this->successResponse($organization);
    }

    /**
     * Update the specified organization.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $organization = Organization::find($id);

        if (empty($organization)) {
            return $this->notFoundResponse('Organization not found');
        }

        $requestData = $request->validate([
            'name' => ['sometimes', 'required', 'max:100'],
            'email' => ['sometimes', 'nullable', 'max:50', 'email'],
            'phone' => ['sometimes', 'nullable', 'max:50'],
            'address' => ['sometimes', 'nullable', 'max:150'],
            'city' => ['sometimes', 'nullable', 'max:50'],
            'region' => ['sometimes', 'nullable', 'max:50'],
            'country' => ['sometimes', 'nullable', 'max:2'],
            'postal_code' => ['sometimes', 'nullable', 'max:25'],
        ]);

        $organization->update($requestData);

        return $this->successResponse($organization->load('contacts'));
    }

    /**
     * Remove the specified organization.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $organization = Organization::find($id);

        if (empty($organization)) {
            return $this->notFoundResponse('Organization not found');
        }

        $organization->delete();

        return $this->successResponse(null, 204);
    }

    /**
     * Restore a soft-deleted organization.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $organization = Organization::withTrashed()->find($id);

        if (empty($organization)) {
            return $this->notFoundResponse('Organization not found');
        }

        $organization->restore();

        return $this->successResponse($organization->load('contacts'));
    }
}

