<?php

namespace App\Http\Controllers;

use App\Services\CoreBackendClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BffAuthController extends Controller
{
    private CoreBackendClient $coreBackendClient;

    public function __construct(CoreBackendClient $coreBackendClient)
    {
        $this->coreBackendClient = $coreBackendClient;
    }

    /**
     * Registrar novo cliente (cadastro)
     * Faz proxy para o backend core
     */
    public function cadastro(Request $request): JsonResponse
    {
        try {
            // Validação dos dados
            $validated = $request->validate([
                'nome' => 'required|string|min:3|max:150',
                'username' => 'required|string|min:3|max:100',
                'email' => 'required|email|max:150',
                'senha' => 'required|string|min:8',
                'telefone' => 'nullable|string|max:30',
                'cpf' => 'nullable|string|min:11|max:20',
                'endereco' => 'nullable|string|max:255',
                'dataNascimento' => 'nullable|date',
            ]);

            Log::info('BFF: Tentando cadastrar cliente via backend', [
                'username' => $validated['username'],
                'email' => $validated['email']
            ]);

            // Mapear para o formato do backend (Spring Boot espera 'password', 'username' e 'nome')
            $dadosBackend = [
                'nome' => $validated['nome'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => $validated['senha'], // Backend espera 'password'
                'telefone' => $validated['telefone'] ?? null,
                'cpf' => $validated['cpf'] ?? null,
                'endereco' => $validated['endereco'] ?? null,
                'dataNascimento' => $validated['dataNascimento'] ?? null,
            ];

            // Chamar o backend via CoreBackendClient
            $resultado = $this->coreBackendClient->registrarUsuario($dadosBackend);

            if ($resultado === null) {
                Log::error('BFF: Backend retornou null ao cadastrar cliente');
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao cadastrar cliente no sistema'
                ], 500);
            }

            Log::info('BFF: Cliente cadastrado com sucesso via backend', [
                'clienteId' => $resultado['userId'] ?? $resultado['id'] ?? null
            ]);

            // Retornar resposta padronizada para o frontend
            return response()->json([
                'success' => true,
                'data' => [
                    'userId' => $resultado['userId'] ?? $resultado['id'] ?? null,
                    'nome' => $resultado['nome'] ?? $resultado['username'] ?? $validated['nome'],
                    'email' => $resultado['email'] ?? $validated['email'],
                    'token' => $resultado['token'] ?? null,
                ],
                'message' => 'Cadastro realizado com sucesso'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('BFF: Erro ao cadastrar cliente', [
                'error' => $e->getMessage()
            ]);

            // Retornar a mensagem de erro do backend para o usuário
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Login de cliente
     * Faz proxy para o backend core
     */
    public function login(Request $request): JsonResponse
    {
        try {
            // Validação dos dados - aceita email OU username
            $validated = $request->validate([
                'email' => 'required|string', // Aceita email ou username
                'senha' => 'required|string',
            ]);

            $emailOrUsername = $validated['email'];

            Log::info('BFF: Tentando autenticar cliente via backend', [
                'credential' => $emailOrUsername
            ]);

            // Chamar o backend via CoreBackendClient
            $resultado = $this->coreBackendClient->autenticarUsuario(
                $emailOrUsername, // Pode ser email ou username
                $validated['senha']
            );

            if ($resultado === null) {
                Log::warning('BFF: Falha ao autenticar cliente via backend', [
                    'credential' => $emailOrUsername
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Credenciais inválidas'
                ], 401);
            }

            Log::info('BFF: Cliente autenticado com sucesso via backend', [
                'userId' => $resultado['userId'] ?? $resultado['id'] ?? null
            ]);

            // Retornar resposta padronizada para o frontend
            return response()->json([
                'success' => true,
                'data' => [
                    'userId' => $resultado['userId'] ?? $resultado['id'] ?? null,
                    'nome' => $resultado['nome'] ?? $resultado['username'] ?? null,
                    'email' => $resultado['email'] ?? $emailOrUsername,
                    'token' => $resultado['token'] ?? null,
                ],
                'message' => 'Login realizado com sucesso'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('BFF: Erro ao fazer login', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar token do usuário
     */
    public function validarToken(Request $request): JsonResponse
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token não fornecido'
                ], 401);
            }

            $resultado = $this->coreBackendClient->validarToken($token);

            if ($resultado === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token inválido'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'data' => $resultado
            ], 200);

        } catch (\Exception $e) {
            Log::error('BFF: Erro ao validar token', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao validar token'
            ], 500);
        }
    }

    /**
     * Retorna dados do usuário autenticado
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token não fornecido'
                ], 401);
            }

            Log::info('BFF: Buscando dados do usuário autenticado via backend');

            // Chamar o backend via CoreBackendClient
            $resultado = $this->coreBackendClient->obterUsuarioAutenticado($token);

            if ($resultado === null) {
                Log::warning('BFF: Token inválido ou usuário não encontrado');

                return response()->json([
                    'success' => false,
                    'message' => 'Não autenticado'
                ], 401);
            }

            Log::info('BFF: Dados do usuário obtidos com sucesso', [
                'userId' => $resultado['id'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Usuário autenticado'
            ], 200);

        } catch (\Exception $e) {
            Log::error('BFF: Erro ao buscar usuário autenticado', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar usuário autenticado'
            ], 500);
        }
    }

    /**
     * Logout do usuário
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token não fornecido'
                ], 401);
            }

            $sucesso = $this->coreBackendClient->logoutUsuario($token);

            if (!$sucesso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao fazer logout'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Logout realizado com sucesso'
            ], 200);

        } catch (\Exception $e) {
            Log::error('BFF: Erro ao fazer logout', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer logout'
            ], 500);
        }
    }
}
