<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthenticationService
{
    /**
     * Registrar um novo usuário
     *
     * @param array $userData
     * @return User
     * @throws \Exception
     */
    public function register(array $userData): User
    {
        try {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
            ]);

            Log::info('AuthenticationService: Usuário registrado', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return $user;

        } catch (\Exception $e) {
            Log::error('AuthenticationService: Erro ao registrar usuário', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Validar credenciais do usuário
     *
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function validateCredentials(string $email, string $password): ?User
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            Log::warning('AuthenticationService: Tentativa de login com credenciais inválidas', [
                'email' => $email
            ]);
            return null;
        }

        return $user;
    }

    /**
     * Gerar token de acesso para o usuário
     *
     * @param User $user
     * @param string $tokenName
     * @return string
     */
    public function generateToken(User $user, string $tokenName = 'auth_token'): string
    {
        $token = $user->createToken($tokenName)->plainTextToken;

        Log::info('AuthenticationService: Token gerado', [
            'user_id' => $user->id,
            'token_name' => $tokenName
        ]);

        return $token;
    }

    /**
     * Revogar token atual do usuário
     *
     * @param User $user
     * @return bool
     */
    public function revokeCurrentToken(User $user): bool
    {
        try {
            $user->currentAccessToken()->delete();

            Log::info('AuthenticationService: Token atual revogado', [
                'user_id' => $user->id
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('AuthenticationService: Erro ao revogar token', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Revogar todos os tokens do usuário
     *
     * @param User $user
     * @return bool
     */
    public function revokeAllTokens(User $user): bool
    {
        try {
            $user->tokens()->delete();

            Log::info('AuthenticationService: Todos os tokens revogados', [
                'user_id' => $user->id
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('AuthenticationService: Erro ao revogar todos os tokens', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Verificar se o email já está cadastrado
     *
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    /**
     * Buscar usuário por ID
     *
     * @param int $userId
     * @return User|null
     */
    public function getUserById(int $userId): ?User
    {
        return User::find($userId);
    }

    /**
     * Buscar usuário por email
     *
     * @param string $email
     * @return User|null
     */
    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
