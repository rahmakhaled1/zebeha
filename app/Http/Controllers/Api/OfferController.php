<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOfferRequest;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function create(CreateOfferRequest $request)
    {
        $data = $request->validated();

        $offer = Offer::create($data);

        if (!$offer) {
            return response()->json([
                'status' => false,
                'message' => 'Offer creation failed',
            ], 500);
        }

        if ($request->has('images')) {
            foreach ($request->images as $imageData) {
                if (isset($imageData['path']) && $imageData['path']->isValid()) {
                    $path = $imageData['path']->store('images', 'public');

                    $offer->images()->create([
                        'path' => $path,
                        'imageable_id' => $offer->id,
                        'imageable_type' => Offer::class,
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
            'message' => 'Offer created successfully',
            'offer' => $offer
        ], 201);
    }

    public function fetch_offers()
    {
        $user_id = request()->header('user-id');
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User ID not provided or invalid',
            ], 400);
        }

        $offer =Offer::with(['images', 'product', 'superCategory'])
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($offer->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No offers found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Offers fetched successfully',
            'offers' => $offer,
        ], 200);
    }
    public function fetch_offer($id)
    {
        $user_id = request()->header('user-id');
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User ID not provided or invalid',
            ], 400);
        }
        $offer = Offer::with(['images', 'product', 'superCategory'])->find($id);

        if (!$offer) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'offer' => $offer
        ], 200);
    }

}
