<?php

namespace App\Http\Controllers;

use App\Services\CoreBackendClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FrequenciaController extends Controller
{
    private CoreBackendClient $coreBackendClient;

    public function __construct(CoreBackendClient $coreBackendClient)
    {
        $this->coreBackendClient = $coreBackendClient;
    }

    /**
     * Registrar nova frequência de treino
     * Faz proxy para o backend core criando um treino
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validação dos dados
            $validated = $request->validate([
                'usuarioId' => 'required|integer',
                'treino' => 'required|integer',  // ID do treino
                'duracao' => 'required|integer', // duração em minutos
                'data' => 'required|date',       // data do treino
                'nivel' => 'required|string|in:INICIANTE,INTERMEDIARIO,AVANCADO',
            ]);

            Log::info('BFF: Registrando frequência de treino', [
                'usuarioId' => $validated['usuarioId'],
                'treino' => $validated['treino'],
                'duracao' => $validated['duracao'],
            ]);

            // Obter token do header Authorization
            $token = $request->bearerToken();

            // Mapear para o formato do backend
            // O backend espera criar um treino com clienteId, titulo, descricao, nivel
            $dadosBackend = [
                'clienteId' => $validated['usuarioId'],
                'titulo' => 'Treino #' . $validated['treino'],
                'descricao' => 'Treino realizado em ' . $validated['data'] . ' com duração de ' . $validated['duracao'] . ' minutos',
                'nivel' => $validated['nivel'],
            ];

            // Verificar se já existe método para criar treino no CoreBackendClient
            // Se não existir, precisaremos adicionar
            $resultado = $this->coreBackendClient->criarTreino($dadosBackend, $token);

            if ($resultado === null) {
                Log::error('BFF: Backend retornou null ao registrar frequência');
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao registrar frequência no sistema'
                ], 500);
            }

            Log::info('BFF: Frequência registrada com sucesso via backend', [
                'treinoId' => $resultado['id'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Frequência registrada com sucesso'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('BFF: Erro ao registrar frequência', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar frequência',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
