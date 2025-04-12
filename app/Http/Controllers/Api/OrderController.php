<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'shipping_address' => 'nullable|string',
            'coupon_code' => 'nullable|string'
        ]);

        $user_id = request()->header('user-id');
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User ID not provided or invalid',
            ], 400);
        }

        $cartItems = CartItem::where('user_id', $user->id)->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Cart is empty'], 400);
        }

        $coupon = null;
        $discount = 0;
        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)
                ->where('status', 1)
                ->whereDate('expiry_date', '>=', now())
                ->first();

            if (!$coupon) {
                return response()->json(['status' => false, 'message' => 'Invalid coupon code'], 400);
            }

            $discount = $coupon->discount_percentage;
        }

        DB::beginTransaction();

        try {
            $subtotal = $cartItems->sum('total_price');

            $discountAmount = ($discount > 0) ? ($subtotal * $discount / 100) : 0;

            $total_price = $subtotal - $discountAmount;

            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $total_price,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'coupon_id' => $coupon ? $coupon->id : null,
            ]);

            foreach ($cartItems as $item) {
                $total_item_price = $item->price * $item->quantity;


                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'offer_id' => $item->offer_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total_price' => $total_item_price,
                ]);

                $product = Product::find($item->product_id);
                if ($product) {
                    if ($product->stock < $item->quantity) {
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'message' => "الكمية المطلوبة من المنتج '{$product->name}' غير متوفرة في المخزون"
                        ], 400);
                    }

                    $product->stock -= $item->quantity;
                    $product->quantity_sold += $item->quantity;
                    $product->save();
                }
            }
            CartItem::where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Order placed successfully', 'order' => $order]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
        }
    }

    public function listOrders()
    {
        $user_id = request()->header('user-id');
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User ID not provided or invalid',
            ], 400);
        }
        $orders = Order::with('orderItems.product')->where('user_id', $user->id)->get();

        return response()->json(['status' => true, 'orders' => $orders]);
    }
}
