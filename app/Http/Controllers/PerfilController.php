<?php

namespace App\Http\Controllers;

use App\Services\CoreBackendClient;
use Illuminate\Http\JsonResponse;

class PerfilController extends Controller
{
    private CoreBackendClient $coreBackendClient;

    public function __construct(CoreBackendClient $coreBackendClient)
    {
        $this->coreBackendClient = $coreBackendClient;
    }

    /**
     * Obter dados agregados da tela de Perfil
     * Combina dados do cliente, treinos recentes e histórico de pagamentos
     */
    public function show(int $clienteId): JsonResponse
    {
        $cliente = $this->coreBackendClient->getCliente($clienteId);

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente não encontrado',
            ], 404);
        }

        $treinos = $this->coreBackendClient->listarTreinosPorCliente($clienteId);
        $pagamentos = $this->coreBackendClient->listarPagamentosPorCliente($clienteId);

        return response()->json([
            'success' => true,
            'data' => [
                'cliente' => $cliente,
                'treinosRecentes' => array_slice($treinos, 0, 5), // Últimos 5 treinos
                'historicoPagamentos' => $pagamentos,
            ],
            'message' => 'Dados do perfil obtidos com sucesso',
        ]);
    }
}
