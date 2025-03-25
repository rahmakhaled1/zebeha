<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Services\Auth\AuthService;

class AuthController extends Controller
{
    public function __construct(protected AuthService $auth_service){}

    public function register(RegisterRequest $request)
    {
        return $this->auth_service->register($request);
    }

    public function login(LoginRequest $request)
    {
        return $this->auth_service->login($request);
    }

    public function sendOtp(SendOtpRequest $request)
    {
        return $this->auth_service->sendOtp($request);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        return $this->auth_service->verifyOtp($request);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        return $this->auth_service->forgotPassword($request);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        return $this->auth_service->resetPassword($request);
    }

    public function logout()
    {
        return $this->auth_service->logout();
    }

}
