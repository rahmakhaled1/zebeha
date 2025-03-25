<?php

namespace App\Services\Auth;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\AuthResource;
use App\Mail\SendOtpMail;
use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['phone'] = $data['phone_country_code'] . $data['phone'];
            unset($data['phone_country_code']);

            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);

            $otp = mt_rand(1000, 9999);
            UserOtp::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'otp' => $otp,
                    'phone' => $user->phone,
                    'expires_at' => now()->addMinutes(5)
                ]);
            Mail::to($user->email)->send(new SendOtpMail($otp));


            DB::commit();

            return response()->json([
                'message' => 'User registered successfully. Please verify your email.',
                'data' => new AuthResource($user)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'User registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = ['password' => $request->password];

        if (filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $request->email_or_phone;
        } else {
            $user = User::where('phone', $request->email_or_phone)->first();

            if (!$user) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $credentials['phone'] = $user->phone;
        }

        if (auth()->attempt($credentials)) {
            $user = auth()->user();

            if ($user->is_admin) {
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'message' => 'Welcome Back, Admin!',
                    'data' => new AuthResource($user),
                    'token' => $token
                ], 200);
            }

            $userOtp = UserOtp::where('user_id', $user->id)
                ->where('is_verified', true)
                ->latest()
                ->first();

            if (!$userOtp) {
                return response()->json([
                    'message' => 'Account not verified. Please verify your OTP.'
                ], 403);
            }

            if ($user->is_banned ?? false) {
                return response()->json([
                    'message' => 'Your account is banned. Contact support.'
                ], 403);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User logged in successfully',
                'data' => new AuthResource($user),
                'token' => $token
            ], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }


    public function sendOtp(SendOtpRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $otp = mt_rand(1000, 9999);

        UserOtp::updateOrCreate(
            ['user_id' => $user->id],
            [
                'otp' => $otp,
                'phone' => $user->phone,
                'expires_at' => now()->addMinutes(5)
            ]);
        Mail::to($user->email)->send(new SendOtpMail($otp));

        return response()->json(['message' => 'OTP sent successfully'], 200);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user_otp = UserOtp::where('user_id', $user->id)->latest()->first();

        if (!$user_otp) {
            return response()->json(['message' => 'No OTP found for this user'], 400);
        }

        if ($user_otp->otp == $data['otp'] && $user_otp->expires_at > now()) {
            $user_otp->is_verified = true;
            $user_otp->save();

            return response()->json(['message' => 'OTP verified successfully'], 200);
        }

        return response()->json(['message' => 'Invalid OTP'], 400);
    }


    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'User logged out successfully'], 200);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $otp = mt_rand(1000, 9999);

        UserOtp::updateOrCreate(
            ['user_id' => $user->id],
            ['otp' => $otp, 'otp_expire_at' => now()->addMinutes(5)]
        );
        Mail::to($user->email)->send(new SendOtpMail($otp));

        return response()->json(['message' => 'OTP sent successfully'], 200);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user_otp = UserOtp::where('user_id', $user->id)->latest()->first();

        if (!$user_otp || $user_otp->otp !== $data['otp'] || $user_otp->expires_at <= now()) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json(['message' => 'Password reset successfully'], 200);
    }
}
