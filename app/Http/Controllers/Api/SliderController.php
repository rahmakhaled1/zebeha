<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSliderRequest;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function create(CreateSliderRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/images', $filename);
            $data['image'] = 'storage/images/' . $filename;
        }

        $slider = Slider::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Slider created successfully',
            'slider' => $slider,
        ], 201);
    }

    public function fetch_sliders()
    {
        $user_id = request()->header('user-id');

        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User ID not provided or invalid',
            ], 400);
        }
        $sliders = Slider::orderBy('created_at', 'desc')
            ->paginate(10);

        if ($sliders->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No sliders found for this user',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Sliders fetched successfully',
            'sliders' => $sliders,
        ], 200);
    }




}
