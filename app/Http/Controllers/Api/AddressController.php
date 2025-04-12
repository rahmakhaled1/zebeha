<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
        ]);

        $user_id = request()->header('user-id');
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User ID not provided or invalid'], 400);
        }

        $address = $user->addresses()->create([
            'street' => $request->street,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'country' => $request->country,
        ]);

        return response()->json(['status' => true, 'address' => $address], 201);

    }
}
