<?php

namespace App\Http\Controllers;

use App\Services\CoreBackendClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PagamentosController extends Controller
{
    private CoreBackendClient $coreBackendClient;

    public function __construct(CoreBackendClient $coreBackendClient)
    {
        $this->coreBackendClient = $coreBackendClient;
    }

    /**
     * Listar todos os pagamentos
     */
    public function index(): JsonResponse
    {
        $pagamentos = $this->coreBackendClient->listarPagamentos();

        return response()->json([
            'success' => true,
            'data' => $pagamentos,
            'message' => 'Lista de pagamentos obtida com sucesso',
        ]);
    }

    /**
     * Buscar pagamento por ID
     */
    public function show(int $id): JsonResponse
    {
        $pagamento = $this->coreBackendClient->getPagamento($id);

        if (!$pagamento) {
            return response()->json([
                'success' => false,
                'message' => 'Pagamento não encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pagamento,
            'message' => 'Pagamento obtido com sucesso',
        ]);
    }

    /**
     * Listar pagamentos por cliente
     */
    public function porCliente(int $clienteId): JsonResponse
    {
        $pagamentos = $this->coreBackendClient->listarPagamentosPorCliente($clienteId);

        return response()->json([
            'success' => true,
            'data' => $pagamentos,
            'message' => 'Pagamentos do cliente obtidos com sucesso',
        ]);
    }

    /**
     * Registrar novo pagamento
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'clienteId' => 'required|integer',
            'valor' => 'required|numeric|min:0.01',
            'metodo' => 'required|string|in:CARTAO,PIX,BOLETO',
            // Aceita os valores do Core (PENDENTE, APROVADO, FALHOU) e mantém compatibilidade com PAGO/CANCELADO
            'status' => 'required|string|in:PENDENTE,APROVADO,FALHOU,PAGO,CANCELADO',
            'referenciaExterna' => 'nullable|string',
        ]);

        // Mapear status legados do frontend para o domínio do Core
        $validated['status'] = match ($validated['status']) {
            'PAGO' => 'APROVADO',
            'CANCELADO' => 'FALHOU',
            default => $validated['status'],
        };

        $pagamento = $this->coreBackendClient->registrarPagamento($validated);

        if (!$pagamento) {
            return response()->json([
                'success' => false,
                'message' => 'Falha ao registrar pagamento',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $pagamento,
            'message' => 'Pagamento registrado com sucesso',
        ], 201);
    }
}
