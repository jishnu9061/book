<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

abstract class BaseApiController
{
    /**
     * Return a success JSON response.
     *
     * @param mixed $data
     * @param int $statusCode
     * @param array $meta
     * @return JsonResponse
     */
    protected function successResponse($data = null, int $statusCode = 200, array $meta = []): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
            'meta' => array_merge([
                'timestamp' => now()->toIso8601String(),
                'version' => 'v1',
            ], $meta),
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * Return a paginated success JSON response.
     *
     * @param LengthAwarePaginator $paginator
     * @param array $meta
     * @return JsonResponse
     */
    protected function paginatedResponse(LengthAwarePaginator $paginator, array $meta = []): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $paginator->items(),
            'meta' => array_merge([
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                    'from' => $paginator->firstItem(),
                    'to' => $paginator->lastItem(),
                ],
                'timestamp' => now()->toIso8601String(),
                'version' => 'v1',
            ], $meta),
        ];

        return response()->json($response, 200);
    }

    /**
     * Return an error JSON response.
     *
     * @param string $message
     * @param int $statusCode
     * @param array $errors
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $statusCode = 400, array $errors = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'meta' => [
                'timestamp' => now()->toIso8601String(),
                'version' => 'v1',
            ],
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a validation error JSON response.
     *
     * @param \Illuminate\Contracts\Validation\Validator|array $validator
     * @return JsonResponse
     */
    protected function validationErrorResponse($validator): JsonResponse
    {
        $errors = is_array($validator) ? $validator : $validator->errors()->toArray();
        
        return $this->errorResponse(
            'Validation failed',
            422,
            $errors
        );
    }

    /**
     * Return a not found JSON response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Return an unauthorized JSON response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Return a forbidden JSON response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Get pagination parameters from request.
     *
     * @param Request $request
     * @return array
     */
    protected function getPaginationParams(Request $request): array
    {
        $perPage = min((int) $request->get('per_page', 15), 100);
        $page = max((int) $request->get('page', 1), 1);

        return [$perPage, $page];
    }

    /**
     * Apply filters to query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Request $request
     * @param array $allowedFilters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyFilters($query, Request $request, array $allowedFilters = [])
    {
        $filters = $request->get('filter', []);

        if (empty($filters) || empty($allowedFilters)) {
            return $query;
        }

        foreach ($filters as $field => $value) {
            if (!in_array($field, $allowedFilters)) {
                continue;
            }

            if (is_array($value)) {
                // Handle range filters like filter[created_at][from] and filter[created_at][to]
                if (isset($value['from']) || isset($value['to'])) {
                    if (isset($value['from'])) {
                        $query->where($field, '>=', $value['from']);
                    }
                    if (isset($value['to'])) {
                        $query->where($field, '<=', $value['to']);
                    }
                } else {
                    // Handle array values (IN clause)
                    $query->whereIn($field, $value);
                }
            } else {
                $query->where($field, $value);
            }
        }

        return $query;
    }

    /**
     * Apply sorting to query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Request $request
     * @param array $allowedSorts
     * @param string $defaultSort
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applySorting($query, Request $request, array $allowedSorts = [], string $defaultSort = 'id')
    {
        $sort = $request->get('sort', $defaultSort);
        $order = strtolower($request->get('order', 'asc'));

        if (!in_array($sort, $allowedSorts) && !empty($allowedSorts)) {
            $sort = $defaultSort;
        }

        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'asc';
        }

        return $query->orderBy($sort, $order);
    }

    /**
     * Apply eager loading for relationships.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Request $request
     * @param array $defaultIncludes
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyIncludes($query, Request $request, array $defaultIncludes = [])
    {
        $include = $request->get('include', '');

        if (empty($include)) {
            if (!empty($defaultIncludes)) {
                return $query->with($defaultIncludes);
            }
            return $query;
        }

        $includes = array_map('trim', explode(',', $include));
        
        return $query->with($includes);
    }
}

