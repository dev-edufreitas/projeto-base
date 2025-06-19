<?php
// app/GraphQL/Mutations/AuthMutation.php
namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthMutation
{
    public function login(mixed $root, array $args): array
    {
        $credentials = $args['input'];
        
        if (!Auth::attempt($credentials)) {
            throw new AuthenticationException('Credenciais inválidas.');
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration', 525600), // minutos
            'user' => $user,
        ];
    }

    public function logout(): array
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();

        return [
            'message' => 'Successfully logged out',
            'status' => 'success',
        ];
    }

    public function register(mixed $root, array $args): array
    {
        $input = $args['input'];
        
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'], // Já hashada pela diretiva @hash
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration', 525600),
            'user' => $user,
        ];
    }
}
