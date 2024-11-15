<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Authentication User",
 *     description="API Endpoints para órdenes"
 * )
 */
class AuthController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Users"},
     *     summary="Registrar nuevo usuario",
     *     description="Crear nuevo usuario",
     *     operationId="createUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email", "password"},
     *             @OA\Property(property="name", type="string", example="username"),
     *             @OA\Property(property="email", type="email", example="example@gmail.com"),
     *             @OA\Property(property="password", type="string", example="password"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Token obtained",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="your-jwt-token"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     security={{"sanctum": {}}}
     * )
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Users"},
     *     summary="Inicio de sesión",
     *     description="Iniciar sesión para obtener un token de acceso",
     *     operationId="loginUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="email", example="example@gmail.com"),
     *             @OA\Property(property="password", type="string", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token obtained",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="your-jwt-token"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     security={{"sanctum": {}}}
     * )
     */

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!auth()->attempt($credentials)) {
            return response()->json(['message' => 'Invalid login details'], 401);
        }

        $token = auth()->user()->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
