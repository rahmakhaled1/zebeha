<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSupCategoriesRequest;
use App\Models\SupCategory;
use App\Models\User;
use Illuminate\Http\Request;

class SupCategoryController extends Controller
{
    public function create(CreateSupCategoriesRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/images', $filename);
            $data['image'] = 'storage/images/' . $filename;
        }

        $supcategory = SupCategory::create($data);

        return response()->json([
            'status' => true,
            'message' => 'SupCategory created successfully',
            'data' => $supcategory,
        ], 201);
    }

    public function fetch_supcategories()
    {
        $user_id = request()->header('user-id');
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User ID not provided or invalid',
            ], 400);
        }
        $supcategories = SupCategory::with('category')->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($supcategories->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No supcategories found ',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'SupCategories fetched successfully',
            'data' => $supcategories,
        ], 200);
    }

    public function fetch_supcategory($id)
    {
        $user_id = request()->header('user-id');
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User ID not provided or invalid',
            ], 400);
        }
        $supcategory = SupCategory::with('category')->find($id);

        if (!$supcategory) {
            return response()->json([
                'status' => false,
                'message' => 'SupCategory not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'SupCategory fetched successfully',
            'data' => $supcategory,
        ], 200);
    }
}
