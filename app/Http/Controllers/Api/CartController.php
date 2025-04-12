<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user_id = request()->header('user-id');
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User ID not provided or invalid'], 400);
        }

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity;

        // Check for active offer
        $offer = Offer::where('product_id', $product->id)
            ->where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();

        $price = $offer ? $offer->price : $product->price;
        $offer_id = $offer ? $offer->id : null;
        $total_price = $price * $quantity;

        $cartItem = CartItem::updateOrCreate(
            ['user_id' => $user->id, 'product_id' => $product->id],
            ['offer_id' => $offer_id, 'quantity' => $quantity, 'price' => $price, 'total_price' => $total_price]
        );

        return response()->json(['status' => true, 'cart_item' => $cartItem], 201);
    }

    public function viewCart()
    {
        $user_id = request()->header('user-id');
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User ID not provided or invalid'], 400);
        }

        $cartItems = CartItem::with('product')->where('user_id', $user->id)->get();
        return response()->json(['status' => true, 'cart' => $cartItems]);
    }

    public function removeFromCart($id)
    {
        $user_id = request()->header('user-id');
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User ID not provided or invalid'], 400);
        }

        $item = CartItem::where('id', $id)->where('user_id', $user->id)->first();
        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Item not found'], 404);
        }
        $item->delete();
        return response()->json(['status' => true, 'message' => 'Item removed from cart']);
    }

    public function clearCart()
    {
        $user_id = request()->header('user-id');
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User ID not provided or invalid'], 400);
        }

        CartItem::where('user_id', $user->id)->delete();
        return response()->json(['status' => true, 'message' => 'Cart cleared']);
    }
}

