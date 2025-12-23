<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Events\UserCreated;
use App\Models\PendingUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends BaseApiController
{
    /**
     * Display a listing of users.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        [$perPage, $page] = $this->getPaginationParams($request);

        $query = User::query()
            ->with('role', 'country')
            ->orderByName();

        // Apply filters
        $filters = $request->only(['search', 'role_id']);
        $query = $query->filter($filters);

        // Apply sorting
        $allowedSorts = ['id', 'first_name', 'last_name', 'email', 'created_at'];
        $query = $this->applySorting($query, $request, $allowedSorts, 'id');

        $users = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($users, [
            'filters' => $filters,
        ]);
    }

    /**
     * Store a newly created user.
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

        $user = User::create($requestData);

        event(new UserCreated([
            'user_id' => $user->id,
            'password' => $request->input('password'),
            'created_by' => auth()->id(),
        ]));

        return $this->successResponse($user->load('role', 'country'), 201);
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::with('role', 'country')->find($id);

        if (empty($user)) {
            return $this->notFoundResponse('User not found');
        }

        return $this->successResponse($user);
    }

    /**
     * Update the specified user.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (empty($user)) {
            return $this->notFoundResponse('User not found');
        }

        $requestData = $request->validate([
            'first_name' => ['sometimes', 'required', 'max:50'],
            'last_name' => ['sometimes', 'required', 'max:50'],
            'phone' => ['sometimes', 'nullable', 'max:25'],
            'email' => ['sometimes', 'required', 'max:50', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'city' => ['sometimes', 'nullable'],
            'address' => ['sometimes', 'nullable'],
            'country_id' => ['sometimes', 'nullable'],
            'role_id' => ['sometimes', 'nullable', Rule::exists('roles', 'id')],
        ]);

        if (isset($requestData['password'])) {
            $requestData['password'] = Hash::make($requestData['password']);
        }

        $user->update($requestData);

        return $this->successResponse($user->load('role', 'country'));
    }

    /**
     * Remove the specified user.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (empty($user)) {
            return $this->notFoundResponse('User not found');
        }

        $user->delete();

        return $this->successResponse(null, 204);
    }

    /**
     * Restore a soft-deleted user.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $user = User::withTrashed()->find($id);

        if (empty($user)) {
            return $this->notFoundResponse('User not found');
        }

        $user->restore();

        return $this->successResponse($user->load('role', 'country'));
    }

    /**
     * Get pending users.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pending(Request $request)
    {
        [$perPage, $page] = $this->getPaginationParams($request);

        $query = PendingUser::query();

        // Apply filters
        $filters = $request->only(['search']);
        $query = $query->filter($filters);

        $pendingUsers = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($pendingUsers);
    }

    /**
     * Approve a pending user.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve($id)
    {
        $pendingUser = PendingUser::find($id);

        if (empty($pendingUser)) {
            return $this->notFoundResponse('Pending user not found');
        }

        // This logic should match the web controller
        $customerRole = Role::where('slug', 'customer')->first();
        $plainPassword = $this->generateRandomPassword();

        $user = User::create([
            'first_name' => $pendingUser->first_name,
            'last_name' => $pendingUser->last_name,
            'phone' => $pendingUser->phone,
            'email' => $pendingUser->email,
            'password' => Hash::make($plainPassword),
            'address' => $pendingUser->address,
            'country_id' => $pendingUser->country_id,
            'city' => $pendingUser->city,
            'role_id' => $customerRole ? $customerRole->id : null,
        ]);

        $pendingUser->delete();

        return $this->successResponse([
            'user' => $user->load('role'),
            'password' => $plainPassword, // In production, send via email instead
        ]);
    }

    /**
     * Decline a pending user.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function decline($id)
    {
        $pendingUser = PendingUser::find($id);

        if (empty($pendingUser)) {
            return $this->notFoundResponse('Pending user not found');
        }

        $pendingUser->delete();

        return $this->successResponse(['message' => 'Request has been declined!']);
    }

    /**
     * Generate a random password.
     *
     * @return string
     */
    private function generateRandomPassword()
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 13; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }
}

