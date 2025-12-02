<?php

namespace App\Http\Controllers;

use App\Services\CoreBackendClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    private CoreBackendClient $coreBackendClient;

    public function __construct(CoreBackendClient $coreBackendClient)
    {
        $this->coreBackendClient = $coreBackendClient;
    }

    /**
     * Lista todas as conversas do usuário
     */
    public function listarConversas(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token não fornecido',
            ], 401);
        }

        $result = $this->coreBackendClient->getChatConversas($token);

        if ($result === null) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar conversas',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Busca mensagens de uma conversa
     */
    public function buscarMensagens(Request $request, int $conversaId): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token não fornecido',
            ], 401);
        }

        $page = $request->input('page', 0);
        $size = $request->input('size', 50);

        $result = $this->coreBackendClient->getChatMensagens($token, $conversaId, $page, $size);

        if ($result === null) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar mensagens',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Inicia uma conversa com outro usuário
     */
    public function iniciarConversa(Request $request, int $outroUsuarioId): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token não fornecido',
            ], 401);
        }

        $result = $this->coreBackendClient->iniciarConversa($token, $outroUsuarioId);

        if ($result === null) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao iniciar conversa. Verifique se ambos são Premium.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Marca mensagens como lidas
     */
    public function marcarComoLidas(Request $request, int $conversaId): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token não fornecido',
            ], 401);
        }

        $success = $this->coreBackendClient->marcarMensagensComoLidas($token, $conversaId);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Mensagens marcadas como lidas' : 'Erro ao marcar mensagens',
        ]);
    }

    /**
     * Lista usuários Premium para iniciar conversas
     */
    public function listarUsuarios(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token não fornecido',
            ], 401);
        }

        $result = $this->coreBackendClient->getChatUsuarios($token);

        if ($result === null) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar usuários',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Lista usuários online
     */
    public function listarUsuariosOnline(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token não fornecido',
            ], 401);
        }

        $result = $this->coreBackendClient->getChatUsuariosOnline($token);

        if ($result === null) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar usuários online',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}
