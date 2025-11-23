<?php

namespace App\Http\Controllers;

use App\Services\CoreBackendClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientesController extends Controller
{
    private CoreBackendClient $coreBackendClient;

    public function __construct(CoreBackendClient $coreBackendClient)
    {
        $this->coreBackendClient = $coreBackendClient;
    }

    /**
     * Atualizar perfil do cliente
     * IMPORTANTE: O backend valida que o usuário só pode atualizar o próprio perfil
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            // Validação dos dados de entrada
            $validated = $request->validate([
                'nome' => 'nullable|string|min:3|max:150',
                'telefone' => 'nullable|string|max:30',
                'cpf' => 'nullable|string|min:11|max:20',
                'endereco' => 'nullable|string|max:255',
                'dataNascimento' => 'nullable|date_format:Y-m-d|before:today',
                'avatarDataUrl' => 'nullable|string',
                'senha' => 'nullable|string|min:6',
            ]);

            Log::info('BFF: Tentando atualizar perfil via backend', [
                'clienteId' => $id,
                'campos' => array_keys($validated),
            ]);

            // Obter token do header Authorization
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token de autenticação não fornecido',
                ], 401);
            }

            // 1. Buscar dados atuais do cliente do backend
            $clienteAtual = $this->coreBackendClient->obterUsuarioAutenticado($token);

            if (!$clienteAtual) {
                Log::warning('BFF: Não foi possível buscar dados do cliente');
                return response()->json([
                    'success' => false,
                    'message' => 'Não foi possível buscar dados do cliente',
                ], 400);
            }

            // 2. Mesclar dados atuais com os novos (apenas campos enviados)
            $dadosMesclados = [
                'nome' => $validated['nome'] ?? $clienteAtual['nome'],
                'username' => $clienteAtual['username'], // Nunca muda
                'email' => $clienteAtual['email'], // Nunca muda
                'telefone' => $validated['telefone'] ?? $clienteAtual['telefone'],
                'cpf' => $validated['cpf'] ?? $clienteAtual['cpf'],
                'endereco' => $validated['endereco'] ?? $clienteAtual['endereco'],
                'dataNascimento' => $validated['dataNascimento'] ?? $clienteAtual['dataNascimento'],
                'avatarDataUrl' => $validated['avatarDataUrl'] ?? $clienteAtual['avatarDataUrl'],
            ];

            // Se senha foi enviada, adicionar
            if (isset($validated['senha'])) {
                $dadosMesclados['password'] = $validated['senha'];
            }

            Log::info('BFF: Dados mesclados para enviar ao backend', [
                'campos' => array_keys($dadosMesclados),
            ]);

            // 3. Chamar o backend (que valida se o usuário pode atualizar este perfil)
            $cliente = $this->coreBackendClient->atualizarCliente($id, $dadosMesclados, $token);

            if (!$cliente) {
                Log::warning('BFF: Backend retornou null ao atualizar perfil');
                return response()->json([
                    'success' => false,
                    'message' => 'Falha ao atualizar perfil',
                ], 400);
            }

            Log::info('BFF: Perfil atualizado com sucesso via backend', [
                'clienteId' => $cliente['id'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'data' => $cliente,
                'message' => 'Perfil atualizado com sucesso',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('BFF: Erro ao atualizar perfil', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar perfil',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
