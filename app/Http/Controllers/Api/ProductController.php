<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\ImageRequest;
use App\Models\Images;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function create(CreateProductRequest $request)
    {
        $data = $request->validated();
        $product = Product::create($data);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product creation failed',
            ], 500);
        }

        if ($request->has('images')) {
            foreach ($request->images as $imageData) {
                if (isset($imageData['path']) && $imageData['path']->isValid()) {
                    $path = $imageData['path']->store('images', 'public');

                    $product->images()->create([
                        'path' => $path,
                        'imageable_id' => $product->id,
                        'imageable_type' => Product::class,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'One or more images are invalid.',
                    ], 400);
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }

    public function fetch_product($id)
    {
        $user_id = request()->header('user-id');
        if (!$user_id) {
            return response()->json([
                'status' => false,
                'message' => 'User ID not provided',
            ], 400);
        }

        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid user ID',
            ], 400);
        }

        $product = Product::with(['images', 'supcategory'])->find($id);
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $product->images->transform(function ($image) {
            $image->url = asset('storage/' . $image->path);
            return $image;
        });

        return response()->json([
            'status' => true,
            'product' => $product
        ], 200);
    }


    public function fetch_products()
    {
        $user_id = request()->header('user-id');
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User ID not provided or invalid',
            ], 400);
        }

        $products = Product::with(['images', 'supcategory'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $products->getCollection()->transform(function ($product) {
            $product->images->transform(function ($image) {
                $image->url = asset('storage/' . $image->path);
                return $image;
            });
            return $product;
        });

        return response()->json([
            'status' => true,
            'message' => 'Products fetched successfully',
            'products' => $products,
        ], 200);
    }


    public function getTopSellingProducts()
    {
        $products = Product::withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => true,
            'top_selling_products' => $products
        ]);
    }



}
