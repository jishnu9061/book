<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Events\UserCreated;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerController extends BaseApiController
{
    /**
     * Display a listing of customers.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $customerRole = Role::where('slug', 'customer')->first();
        
        [$perPage, $page] = $this->getPaginationParams($request);

        $query = User::query()
            ->whereRoleId($customerRole ? $customerRole->id : 0)
            ->with('role', 'country')
            ->orderByName();

        // Apply filters
        $filters = $request->only(['search']);
        $query = $query->filter($filters);

        // Apply sorting
        $allowedSorts = ['id', 'first_name', 'last_name', 'email', 'created_at'];
        $query = $this->applySorting($query, $request, $allowedSorts, 'id');

        $customers = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($customers, [
            'filters' => $filters,
        ]);
    }

    /**
     * Store a newly created customer.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $requestData = $request->validate([
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'phone' => ['nullable', 'max:25'],
            'email' => ['required', 'max:50', 'email', Rule::unique('users')],
            'password' => ['nullable', 'string', 'min:8'],
            'city' => ['nullable'],
            'address' => ['nullable'],
            'country_id' => ['nullable'],
            'role_id' => ['nullable'],
        ]);

        $customerRole = Role::where('slug', 'customer')->first();
        if (empty($requestData['role_id']) && !empty($customerRole)) {
            $requestData['role_id'] = $customerRole->id;
        }

        if (isset($requestData['password'])) {
            $requestData['password'] = Hash::make($requestData['password']);
        }

        $customer = User::create($requestData);

        event(new UserCreated([
            'user_id' => $customer->id,
            'password' => $request->input('password'),
            'created_by' => auth()->id(),
        ]));

        return $this->successResponse($customer->load('role', 'country'), 201);
    }

    /**
     * Display the specified customer.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $customerRole = Role::where('slug', 'customer')->first();
        $customer = User::where('id', $id)
            ->whereRoleId($customerRole ? $customerRole->id : 0)
            ->with('role', 'country')
            ->first();

        if (empty($customer)) {
            return $this->notFoundResponse('Customer not found');
        }

        return $this->successResponse($customer);
    }

    /**
     * Update the specified customer.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $customerRole = Role::where('slug', 'customer')->first();
        $customer = User::where('id', $id)
            ->whereRoleId($customerRole ? $customerRole->id : 0)
            ->first();

        if (empty($customer)) {
            return $this->notFoundResponse('Customer not found');
        }

        $requestData = $request->validate([
            'first_name' => ['sometimes', 'required', 'max:50'],
            'last_name' => ['sometimes', 'required', 'max:50'],
            'phone' => ['sometimes', 'nullable', 'max:25'],
            'email' => ['sometimes', 'required', 'max:50', 'email', Rule::unique('users')->ignore($customer->id)],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'city' => ['sometimes', 'nullable'],
            'address' => ['sometimes', 'nullable'],
            'country_id' => ['sometimes', 'nullable'],
        ]);

        if (isset($requestData['password'])) {
            $requestData['password'] = Hash::make($requestData['password']);
        }

        $customer->update($requestData);

        return $this->successResponse($customer->load('role', 'country'));
    }

    /**
     * Remove the specified customer.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $customerRole = Role::where('slug', 'customer')->first();
        $customer = User::where('id', $id)
            ->whereRoleId($customerRole ? $customerRole->id : 0)
            ->first();

        if (empty($customer)) {
            return $this->notFoundResponse('Customer not found');
        }

        $customer->delete();

        return $this->successResponse(null, 204);
    }

    /**
     * Restore a soft-deleted customer.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $customerRole = Role::where('slug', 'customer')->first();
        $customer = User::withTrashed()
            ->where('id', $id)
            ->whereRoleId($customerRole ? $customerRole->id : 0)
            ->first();

        if (empty($customer)) {
            return $this->notFoundResponse('Customer not found');
        }

        $customer->restore();

        return $this->successResponse($customer->load('role', 'country'));
    }
}

