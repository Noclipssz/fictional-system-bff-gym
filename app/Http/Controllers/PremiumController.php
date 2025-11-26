<?php

namespace App\Http\Controllers;

use App\Services\CoreBackendClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PremiumController extends Controller
{
    private CoreBackendClient $coreBackendClient;

    public function __construct(CoreBackendClient $coreBackendClient)
    {
        $this->coreBackendClient = $coreBackendClient;
    }

    /**
     * Criar checkout para assinatura premium
     * Requer token JWT no header Authorization
     */
    public function checkout(Request $request): JsonResponse
    {
        $token = $this->extractToken($request);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token de autenticação não fornecido',
            ], 401);
        }

        $result = $this->coreBackendClient->criarCheckoutPremium($token);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Falha ao criar checkout premium',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => 'Checkout criado com sucesso',
        ]);
    }

    /**
     * Webhook do AbacatePay
     * Este endpoint é público e repassa para o core backend
     */
    public function webhook(Request $request): JsonResponse
    {
        $signature = $request->header('X-Webhook-Signature');
        $webhookSecret = $request->query('webhookSecret');
        $payload = $request->all();

        Log::info('PremiumController::webhook recebido', [
            'has_signature' => !empty($signature),
            'has_secret_param' => !empty($webhookSecret),
            'event' => $payload['event'] ?? 'unknown',
        ]);

        $result = $this->coreBackendClient->processarWebhookPremium(
            $payload,
            $signature,
            $webhookSecret
        );

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Falha ao processar webhook',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Webhook processado com sucesso',
        ]);
    }

    /**
     * Extrair token JWT do header Authorization
     */
    private function extractToken(Request $request): ?string
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader) {
            return null;
        }

        if (str_starts_with($authHeader, 'Bearer ')) {
            return substr($authHeader, 7);
        }

        return $authHeader;
    }
}
