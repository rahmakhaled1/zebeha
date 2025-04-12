<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCouponRequest;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function create(CreateCouponRequest $request)
    {
        $data = $request->validated();

        $coupon = Coupon::create($data);
        if (!$coupon) {
            return response()->json([
                'status' => false,
                'message' => 'Coupon creation failed',
            ], 500);
        }
        return response()->json([
            'status' => true,
            'message' => 'Coupon created successfully',
            'coupon' => $coupon
        ], 201);
    }
}
