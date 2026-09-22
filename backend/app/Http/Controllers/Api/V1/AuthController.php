<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /** Always creates a guest user; role is never accepted from input. */
    public function register(RegisterRequest $request)
    {
        $v = $request->validated();

        $user = User::create([
            'name' => $v['name'],
            'email' => strtolower(trim($v['email'])),
            'password' => $v['password'],
            'role' => UserRole::Guest,
        ]);

        return response()->json(['data' => [
            'user' => new UserResource($user),
            'token' => $user->createToken('api')->plainTextToken,
        ]], 201);
    }

    public function login(LoginRequest $request)
    {
        $v = $request->validated();

        $user = User::where('email', strtolower(trim($v['email'])))->first();

        // Generic response: unknown email, null-password guest row, or bad
        // password are indistinguishable (no user-enumeration delta).
        if ($user === null || $user->password === null || ! Hash::check($v['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => ['Invalid credentials.']]);
        }

        return response()->json(['data' => [
            'user' => new UserResource($user),
            'token' => $user->createToken('api')->plainTextToken,
        ]]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
