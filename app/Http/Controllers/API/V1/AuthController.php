<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\API\V1\Auth\LoginRequest;
use App\Http\Requests\API\V1\Auth\RegisterRequest;
use App\Http\Resources\API\V1\UserResource;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => (string) $request->string('name'),
            'email' => (string) $request->string('email'),
            'phone' => $request->input('phone'),
            'password' => (string) $request->string('password'),
            'type' => 'customer',
            'status' => 'active',
        ]);

        $this->cartService->mergeSessionCartIntoUser($user, $request->input('cart_session'));

        return $this->success([
            'user' => new UserResource($user),
            'token' => $user->createToken('nova-mobile')->plainTextToken,
        ], 'تم إنشاء الحساب بنجاح', status: 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()
            ->where('email', (string) $request->string('email'))
            ->first();

        if (! $user || ! Hash::check((string) $request->string('password'), $user->password)) {
            return $this->error('بيانات الدخول غير صحيحة', 422);
        }

        if ($user->status !== 'active') {
            return $this->error('الحساب غير نشط', 403);
        }

        $user->forceFill(['last_login_at' => now()])->save();
        $this->cartService->mergeSessionCartIntoUser($user, $request->input('cart_session'));

        return $this->success([
            'user' => new UserResource($user),
            'token' => $user->createToken('nova-mobile')->plainTextToken,
        ], 'تم تسجيل الدخول بنجاح');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->success(message: 'تم تسجيل الخروج بنجاح');
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->success(new UserResource($request->user()));
    }
}
