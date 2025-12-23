<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends BaseApiController
{
    public function index(Request $request)
    {
        [$perPage, $page] = $this->getPaginationParams($request);

        $query = Faq::query();

        $filters = $request->only(['search']);
        $query = $query->filter($filters);

        $allowedSorts = ['id', 'name', 'created_at'];
        $query = $this->applySorting($query, $request, $allowedSorts, 'name');

        $faqs = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($faqs);
    }

    public function store(Request $request)
    {
        $requestData = $request->validate([
            'name' => ['required', 'max:150'],
            'status' => ['nullable', 'boolean'],
            'details' => ['required', 'string'],
        ]);

        $faq = Faq::create($requestData);

        return $this->successResponse($faq, 201);
    }

    public function show($id)
    {
        $faq = Faq::find($id);

        if (empty($faq)) {
            return $this->notFoundResponse('FAQ not found');
        }

        return $this->successResponse($faq);
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::find($id);

        if (empty($faq)) {
            return $this->notFoundResponse('FAQ not found');
        }

        $requestData = $request->validate([
            'name' => ['sometimes', 'required', 'max:150'],
            'status' => ['sometimes', 'nullable', 'boolean'],
            'details' => ['sometimes', 'required', 'string'],
        ]);

        $faq->update($requestData);

        return $this->successResponse($faq);
    }

    public function destroy($id)
    {
        $faq = Faq::find($id);

        if (empty($faq)) {
            return $this->notFoundResponse('FAQ not found');
        }

        $faq->delete();

        return $this->successResponse(null, 204);
    }

    public function restore($id)
    {
        $faq = Faq::withTrashed()->find($id);

        if (empty($faq)) {
            return $this->notFoundResponse('FAQ not found');
        }

        $faq->restore();

        return $this->successResponse($faq);
    }
}

