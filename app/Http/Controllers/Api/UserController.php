<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Mail\SendOtpMail;
use App\Models\UserOtp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
//    public function index()
//    {
//        $users = User::all();
//        return response()->json(['data'=>$users]);
//    }
//
//    public function register(CreateUserRequest $request)
//    {
//        $data = $request->validated();
//
//
//        if ($request->hasFile('image')) {
//            $file = $request->file('image');
//            $filename = time() . '.' . $file->getClientOriginalExtension();
//            $path = $file->storeAs('public/images', $filename);
//            $data['image'] = 'storage/images/' . $filename;
//        }
//
//        $user = User::create([
//            'f_name'    => $data['f_name']    ?? null,
//            'l_name'    => $data['l_name']    ?? null,
//            'email'     => $data['email']     ?? null,
//            'phone'     => $data['phone']     ?? null,
//            'gender'    => $data['gender']    ?? null,
//            'image'     => $data['image']     ?? null,
//            'is_admin'  => $data['is_admin']  ?? false,
//            'password'  => isset($data['password']) ? Hash::make($data['password']) : null,
//        ]);
//
//        $otp = rand(100000, 999999);
//        UserOtp::updateOrCreate(
//            ['user_id' => $user->id],
//            [
//                'otp' => $otp,
//                'expires_at' => now()->addMinutes(5)
//            ]);
//        Mail::to($user->email)->send(new SendOtpMail($otp));
//        return response()->json(['message' => 'User registered successfully. Please verify your email.', 'user' => $user], 201);
//    }
//    public function login(LoginUserRequest $request)
//    {
//        $data = $request->validated();
//        if (!Auth::attempt(["email" =>$data["email"],"password" => $data["password"]])){
//            return response()->json(['message' => 'Invalid credentials'], 401);
//        }
//
//        $user = Auth::user();
//        $userOtp = UserOtp::where('user_id', $user->id)
//                ->where('is_verified', true)
//                ->latest()
//                ->first();
//
//            if (!$userOtp) {
//                return response()->json([
//                    'message' => 'Account not verified. Please verify your OTP.'
//                ], 403);
//            }
//
//            if ($user->is_banned ?? false) {
//                return response()->json([
//                    'message' => 'Your account is banned. Contact support.'
//                ], 403);
//            }
//        $token = $user->createToken('token')->plainTextToken;
//        $user["token"] = $token;
//        return response()->json([
//            "status" => true,
//            "message" => " Welcome Back Again. ",
//            "data" => $user,
//        ]);
//    }
//
//    public function verifyOtp(Request $request)
//    {
//        $request->validate([
//            'user_id' => 'required|exists:users,id',
//            'otp' => 'required|digits:6',
//        ]);
//
//        $otpRecord = UserOtp::where('user_id', $request->user_id)
//            ->where('otp', $request->otp)
//            ->where('expires_at', '>', now())
//            ->latest()
//            ->first();
//
//        if (!$otpRecord) {
//            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
//        }
//
//        $otpRecord->update(['is_verified' => true]);
//
//        return response()->json(['message' => 'OTP verified successfully.']);
//    }
//    public function forgotPassword(Request $request)
//    {
//        $request->validate(['email' => 'required|email|exists:users,email']);
//
//        $user = User::where('email', $request->email)->first();
//        $otp = rand(100000, 999999);
//
//        UserOtp::updateOrCreate(
//            ['user_id' => $user->id],
//            [
//                'otp' => $otp,
//                'expires_at' => now()->addMinutes(5),
//                'is_verified' => false
//            ]
//        );
//
//        Mail::to($user->email)->send(new SendOtpMail($otp));
//
//        return response()->json(['message' => 'OTP sent to your email.']);
//    }
//
//    public function resetPassword(Request $request)
//    {
//        $request->validate([
//            'email' => 'required|email|exists:users,email',
//            'otp' => 'required|digits:6',
//            'new_password' => 'required|min:6|confirmed',
//        ]);
//
//        $user = User::where('email', $request->email)->first();
//
//        $otpRecord = UserOtp::where('user_id', $user->id)
//            ->where('otp', $request->otp)
//            ->where('expires_at', '>', now())
//            ->latest()
//            ->first();
//
//        if (!$otpRecord) {
//            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
//        }
//
//        $user->update([
//            'password' => Hash::make($request->new_password)
//        ]);
//
//        $otpRecord->update(['is_verified' => true]);
//
//        return response()->json(['message' => 'Password reset successfully.']);
//    }
//    public function logout(Request $request)
//    {
//        $request->user()->currentAccessToken()->delete();
//
//        return response()->json(['message' => 'Logged out successfully.']);
//    }


    public function index()
    {
        $users = User::all();
        return response()->json(['data' => $users]);
    }

    public function register(CreateUserRequest $request)
    {
        $data = $request->validated();


        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/images', $filename);
            $data['image'] = 'storage/images/' . $filename;
        }

        $user = User::create([
            'f_name' => $data['f_name'] ?? null,
            'l_name' => $data['l_name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'gender' => $data['gender'] ?? null,
            'image' => $data['image'] ?? null,
            'is_admin' => $data['is_admin'] ?? false,
            'password' => isset($data['password']) ? Hash::make($data['password']) : null,
        ]);


        return response()->json(['message' => 'User registered successfully. Please verify your email.', 'user' => $user], 201);
    }

    public function login(LoginUserRequest $request)
    {
        $data = $request->validated();

        if (!Auth::attempt(["email" => $data["email"], "password" => $data["password"]])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        if ($user->is_banned ?? false) {
            return response()->json([
                'message' => 'Your account is banned. Contact support.'
            ], 403);
        }

        $token = $user->createToken('token')->plainTextToken;
        $user["token"] = $token;

        return response()->json([
            "status" => true,
            "message" => "Welcome back!",
            "data" => $user,
        ]);
    }


    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required|digits:6',
        ]);

        $otpRecord = UserOtp::where('user_id', $request->user_id)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        $otpRecord->update(['is_verified' => true]);

        return response()->json(['message' => 'OTP verified successfully.']);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        // هنا ممكن تبعتي رسالة تفيد إن الرابط اتبعت (لو هتعملي UI)
        return response()->json(['message' => 'Please reset your password using the designated form.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json(['message' => 'Password reset successfully.']);
    }

    public function logout(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $user->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }


}

