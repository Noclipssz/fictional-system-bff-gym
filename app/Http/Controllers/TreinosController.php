<?php

namespace App\Http\Controllers;

use App\Services\CoreBackendClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TreinosController extends Controller
{
    private CoreBackendClient $coreBackendClient;

    public function __construct(CoreBackendClient $coreBackendClient)
    {
        $this->coreBackendClient = $coreBackendClient;
    }

    /**
     * Listar todos os treinos
     */
    public function index(): JsonResponse
    {
        $treinos = $this->coreBackendClient->listarTreinos();

        return response()->json([
            'success' => true,
            'data' => $treinos,
            'message' => 'Lista de treinos obtida com sucesso',
        ]);
    }

    /**
     * Buscar treino por ID
     */
    public function show(int $id): JsonResponse
    {
        $treino = $this->coreBackendClient->getTreino($id);

        if (!$treino) {
            return response()->json([
                'success' => false,
                'message' => 'Treino não encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $treino,
            'message' => 'Treino obtido com sucesso',
        ]);
    }

    /**
     * Listar treinos por cliente
     */
    public function porCliente(int $clienteId): JsonResponse
    {
        $treinos = $this->coreBackendClient->listarTreinosPorCliente($clienteId);

        return response()->json([
            'success' => true,
            'data' => $treinos,
            'message' => 'Treinos do cliente obtidos com sucesso',
        ]);
    }

    /**
     * Criar novo treino
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'clienteId' => 'required|integer',
            'titulo' => 'required|string',
            'descricao' => 'required|string',
            'nivel' => 'required|string|in:INICIANTE,INTERMEDIARIO,AVANCADO',
        ]);

        $treino = $this->coreBackendClient->criarTreino($validated);

        if (!$treino) {
            return response()->json([
                'success' => false,
                'message' => 'Falha ao criar treino',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $treino,
            'message' => 'Treino criado com sucesso',
        ], 201);
    }
}
