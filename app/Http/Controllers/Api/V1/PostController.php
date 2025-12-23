<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends BaseApiController
{
    public function index(Request $request)
    {
        [$perPage, $page] = $this->getPaginationParams($request);

        $query = Blog::query()
            ->with('type');

        $filters = $request->only(['search']);
        $query = $query->filter($filters);

        $allowedSorts = ['id', 'title', 'created_at', 'updated_at'];
        $query = $this->applySorting($query, $request, $allowedSorts, 'title');

        $posts = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($posts);
    }

    public function store(Request $request)
    {
        $requestData = $request->validate([
            'title' => ['required', 'max:150'],
            'is_active' => ['nullable', 'boolean'],
            'type_id' => ['nullable', 'exists:types,id'],
            'image' => ['nullable', 'string'],
            'details' => ['required', 'string'],
            'excerpt' => ['nullable', 'string'],
        ]);

        $requestData['author_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $requestData['image'] = '/files/' . $request->file('image')->store('posts', ['disk' => 'file_uploads']);
        }

        $post = Blog::create($requestData);

        return $this->successResponse($post->load('type'), 201);
    }

    public function show($id)
    {
        $post = Blog::with('type')->find($id);

        if (empty($post)) {
            return $this->notFoundResponse('Post not found');
        }

        return $this->successResponse($post);
    }

    public function update(Request $request, $id)
    {
        $post = Blog::find($id);

        if (empty($post)) {
            return $this->notFoundResponse('Post not found');
        }

        $requestData = $request->validate([
            'title' => ['sometimes', 'required', 'max:150'],
            'is_active' => ['sometimes', 'nullable', 'boolean'],
            'type_id' => ['sometimes', 'nullable', 'exists:types,id'],
            'image' => ['sometimes', 'nullable', 'string'],
            'details' => ['sometimes', 'required', 'string'],
            'excerpt' => ['sometimes', 'nullable', 'string'],
        ]);

        if ($request->hasFile('image')) {
            $requestData['image'] = '/files/' . $request->file('image')->store('posts', ['disk' => 'file_uploads']);
        }

        $post->update($requestData);

        return $this->successResponse($post->load('type'));
    }

    public function destroy($id)
    {
        $post = Blog::find($id);

        if (empty($post)) {
            return $this->notFoundResponse('Post not found');
        }

        $post->delete();

        return $this->successResponse(null, 204);
    }
}

