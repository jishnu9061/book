<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends BaseApiController
{
    /**
     * Display a listing of contacts.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        [$perPage, $page] = $this->getPaginationParams($request);

        $query = Contact::query()
            ->with('organization')
            ->orderByName();

        // Apply filters
        $filters = $request->only(['search']);
        $query = $query->filter($filters);

        // Apply sorting
        $allowedSorts = ['id', 'first_name', 'last_name', 'email', 'created_at'];
        $query = $this->applySorting($query, $request, $allowedSorts, 'id');

        // Apply includes
        $query = $this->applyIncludes($query, $request, ['organization']);

        $contacts = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($contacts, [
            'filters' => $filters,
        ]);
    }

    /**
     * Store a newly created contact.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $requestData = $request->validate([
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'organization_id' => ['nullable', Rule::exists('organizations', 'id')],
            'email' => ['nullable', 'max:50', 'email'],
            'phone' => ['nullable', 'max:50'],
            'address' => ['nullable', 'max:150'],
            'city' => ['nullable', 'max:50'],
            'region' => ['nullable', 'max:50'],
            'country' => ['nullable', 'max:2'],
            'postal_code' => ['nullable', 'max:25'],
        ]);

        $contact = Contact::create($requestData);

        return $this->successResponse($contact->load('organization'), 201);
    }

    /**
     * Display the specified contact.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $contact = Contact::with('organization')->find($id);

        if (empty($contact)) {
            return $this->notFoundResponse('Contact not found');
        }

        return $this->successResponse($contact);
    }

    /**
     * Update the specified contact.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $contact = Contact::find($id);

        if (empty($contact)) {
            return $this->notFoundResponse('Contact not found');
        }

        $requestData = $request->validate([
            'first_name' => ['sometimes', 'required', 'max:50'],
            'last_name' => ['sometimes', 'required', 'max:50'],
            'organization_id' => ['sometimes', 'nullable', Rule::exists('organizations', 'id')],
            'email' => ['sometimes', 'nullable', 'max:50', 'email'],
            'phone' => ['sometimes', 'nullable', 'max:50'],
            'address' => ['sometimes', 'nullable', 'max:150'],
            'city' => ['sometimes', 'nullable', 'max:50'],
            'region' => ['sometimes', 'nullable', 'max:50'],
            'country' => ['sometimes', 'nullable', 'max:2'],
            'postal_code' => ['sometimes', 'nullable', 'max:25'],
        ]);

        $contact->update($requestData);

        return $this->successResponse($contact->load('organization'));
    }

    /**
     * Remove the specified contact.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $contact = Contact::find($id);

        if (empty($contact)) {
            return $this->notFoundResponse('Contact not found');
        }

        $contact->delete();

        return $this->successResponse(null, 204);
    }

    /**
     * Restore a soft-deleted contact.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $contact = Contact::withTrashed()->find($id);

        if (empty($contact)) {
            return $this->notFoundResponse('Contact not found');
        }

        $contact->restore();

        return $this->successResponse($contact->load('organization'));
    }
}

