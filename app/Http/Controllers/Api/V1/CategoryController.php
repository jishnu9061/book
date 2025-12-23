<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BaseApiController
{
    public function index(Request $request)
    {
        [$perPage, $page] = $this->getPaginationParams($request);

        $query = Category::query()
            ->with(['department', 'parent']);

        $filters = $request->only(['search']);
        $query = $query->filter($filters);

        $allowedSorts = ['id', 'name', 'created_at'];
        $query = $this->applySorting($query, $request, $allowedSorts, 'name');

        $categories = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($categories);
    }

    public function store(Request $request)
    {
        $requestData = $request->validate([
            'name' => ['required', 'max:100'],
            'color' => ['nullable', 'max:50'],
            'department_id' => ['nullable'],
            'parent_id' => ['nullable'],
        ]);

        $category = Category::create($requestData);

        return $this->successResponse($category->load(['department', 'parent']), 201);
    }

    public function show($id)
    {
        $category = Category::with(['department', 'parent'])->find($id);

        if (empty($category)) {
            return $this->notFoundResponse('Category not found');
        }

        return $this->successResponse($category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (empty($category)) {
            return $this->notFoundResponse('Category not found');
        }

        $requestData = $request->validate([
            'name' => ['sometimes', 'required', 'max:100'],
            'color' => ['sometimes', 'nullable', 'max:50'],
            'department_id' => ['sometimes', 'nullable'],
            'parent_id' => ['sometimes', 'nullable'],
        ]);

        $category->update($requestData);

        return $this->successResponse($category->load(['department', 'parent']));
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (empty($category)) {
            return $this->notFoundResponse('Category not found');
        }

        $category->delete();

        return $this->successResponse(null, 204);
    }

    public function restore($id)
    {
        $category = Category::withTrashed()->find($id);

        if (empty($category)) {
            return $this->notFoundResponse('Category not found');
        }

        $category->restore();

        return $this->successResponse($category->load(['department', 'parent']));
    }
}

