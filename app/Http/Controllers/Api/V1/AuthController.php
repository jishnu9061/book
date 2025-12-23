<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class AuthController extends BaseApiController
{
    /**
     * Login and get token.
     *
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     summary="Login user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="token", type="string", example="1|xxxxxxxxxxxx"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        // Revoke existing tokens if needed (optional - for single device login)
        // $user->tokens()->delete();

        $token = $user->createToken('api-token', ['*'])->plainTextToken;

        return $this->successResponse([
            'user' => $user->load('role'),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * Register new user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        // Get customer role if no role specified
        $roleId = $request->role_id;
        if (!$roleId) {
            $customerRole = Role::where('slug', 'customer')->first();
            $roleId = $customerRole ? $customerRole->id : null;
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $roleId,
        ]);

        $token = $user->createToken('api-token', ['*'])->plainTextToken;

        return $this->successResponse([
            'user' => $user->load('role'),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Logout and revoke current token.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 204);
    }

    /**
     * Get current authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return $this->successResponse($request->user()->load('role'));
    }

    /**
     * Request password reset.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function passwordReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return $this->successResponse([
                'message' => 'Password reset link sent to your email',
            ]);
        }

        return $this->errorResponse('Unable to send password reset link', 400);
    }

    /**
     * Reset password with token.
     *
     * @param Request $request
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function passwordResetWithToken(Request $request, string $token)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $status = Password::reset(
            array_merge($request->only('email', 'password', 'password_confirmation'), ['token' => $token]),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->successResponse([
                'message' => 'Password reset successfully',
            ]);
        }

        return $this->errorResponse('Unable to reset password', 400);
    }
}

