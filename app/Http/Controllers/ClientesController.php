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
     * Listar todos os clientes
     */
    public function index(): JsonResponse
    {
        $clientes = $this->coreBackendClient->listarClientes();

        Log::info('BFF ClientesController@index', [
            'core_base_url' => config('services.core_backend.url'),
            'clientes_count' => is_array($clientes) ? count($clientes) : null,
        ]);

        $payload = [
            'success' => true,
            'data' => $clientes,
            'message' => 'Lista de clientes obtida com sucesso',
        ];

        if (app()->environment('local') && request()->boolean('debug', false)) {
            $payload['debug'] = [
                'core_base_url' => config('services.core_backend.url'),
                'count' => is_array($clientes) ? count($clientes) : null,
            ];
            if (request()->query('debug') === '2') {
                $testUrl = rtrim(config('services.core_backend.url'), '/') . '/api/clientes';
                $stream = @file_get_contents($testUrl);
                $payload['debug']['stream_status'] = $stream !== false ? 'ok' : 'fail';
                $payload['debug']['stream_snippet'] = $stream !== false ? substr($stream, 0, 200) : null;
            }
        }

        return response()->json($payload);
    }

    /**
     * Buscar cliente por ID
     */
    public function show(int $id): JsonResponse
    {
        $cliente = $this->coreBackendClient->getCliente($id);

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente não encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $cliente,
            'message' => 'Cliente obtido com sucesso',
        ]);
    }

    /**
     * Criar novo cliente
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string',
            'email' => 'required|email',
            'telefone' => 'required|string',
            'cpf' => 'required|string',
            'endereco' => 'required|string',
            'dataNascimento' => 'required|date_format:Y-m-d',
            'premium' => 'boolean',
            'avatarDataUrl' => 'nullable|string',
        ]);

        $cliente = $this->coreBackendClient->criarCliente($validated);

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Falha ao criar cliente',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $cliente,
            'message' => 'Cliente criado com sucesso',
        ], 201);
    }

    /**
     * Atualizar cliente
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'nome' => 'string',
            'email' => 'email',
            'telefone' => 'string',
            'cpf' => 'string',
            'endereco' => 'string',
            'dataNascimento' => 'date_format:Y-m-d',
            'premium' => 'boolean',
            'premiumAte' => 'nullable|date_format:Y-m-d',
            'avatarDataUrl' => 'nullable|string',
        ]);

        $cliente = $this->coreBackendClient->atualizarCliente($id, $validated);

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Falha ao atualizar cliente',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $cliente,
            'message' => 'Cliente atualizado com sucesso',
        ]);
    }
}
