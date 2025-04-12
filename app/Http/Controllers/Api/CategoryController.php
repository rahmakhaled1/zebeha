<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoriesRequest;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function fetch_categories()
    {
        $user_id = request()->header('user-id');
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User ID not provided or invalid',
            ], 400);
        }
        $categories = Category::orderBy('created_at', 'desc')
            ->paginate(10);

        if ($categories->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No categories found ',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Categories fetched successfully',
            'data' => $categories,
        ], 200);
    }

    public function fetch_category($id)
    {
        $user_id = request()->header('user-id');
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User ID not provided or invalid',
            ], 400);
        }
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Category fetched successfully',
            'data' => $category,
        ], 200);
    }
  public function create(CreateCategoriesRequest $request)
  {
    $data = $request->validated();

    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/images', $filename);
        $data['image'] = 'storage/images/' . $filename;
    }

    $category = Category::create($data);

    return response()->json([
        'status' => true,
        'message' => 'Category created successfully',
        'data' => $category,
    ], 201);
  }
}
