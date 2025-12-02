<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CoreBackendClient
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.core_backend.url');
    }

    /**
     * Atualizar perfil do cliente
     * IMPORTANTE: O backend valida que o usuário só pode atualizar o próprio perfil
     */
    public function atualizarCliente(int $clienteId, array $dados, ?string $token = null): ?array
    {
        try {
            $request = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                ;

            // Adicionar token se fornecido
            if ($token) {
                $request = $request->withToken($token);
            }

            $response = $request->put("{$this->baseUrl}/api/clientes/{$clienteId}", $dados);

            Log::info('CoreBackendClient::atualizarCliente response', [
                'id' => $clienteId,
                'status' => $response->status(),
                'body_snippet' => substr($response->body(), 0, 200),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? null;
            }

            Log::warning("Falha ao atualizar perfil {$clienteId}: {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar perfil {$clienteId}: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Buscar treino por ID
     */
    public function getTreino(int $treinoId): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                
                ->get("{$this->baseUrl}/api/treinos/{$treinoId}");
            
            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? null;
            }
            
            Log::warning("Falha ao buscar treino {$treinoId}: {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao buscar treino {$treinoId}: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Listar treinos por cliente
     */
    public function listarTreinosPorCliente(int $clienteId, ?string $token = null): array
    {
        try {
            $request = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                ;

            // Adicionar token se fornecido
            if ($token) {
                $request = $request->withToken($token);
            }

            $response = $request->get("{$this->baseUrl}/api/treinos/cliente/{$clienteId}");

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }

            Log::warning("Falha ao listar treinos do cliente {$clienteId}: {$response->status()}");
            return [];
        } catch (\Exception $e) {
            Log::error("Erro ao listar treinos do cliente {$clienteId}: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Listar todos os treinos
     */
    public function listarTreinos(): array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                
                ->get("{$this->baseUrl}/api/treinos");
            
            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }
            
            Log::warning("Falha ao listar treinos: {$response->status()}");
            return [];
        } catch (\Exception $e) {
            Log::error("Erro ao listar treinos: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Criar treino
     */
    public function criarTreino(array $dados, ?string $token = null): ?array
    {
        try {
            $request = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                ;

            // Adicionar token se fornecido
            if ($token) {
                $request = $request->withToken($token);
            }

            $response = $request->post("{$this->baseUrl}/api/treinos", $dados);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? null;
            }

            Log::warning("Falha ao criar treino: {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao criar treino: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Buscar pagamento por ID
     */
    public function getPagamento(int $pagamentoId): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                
                ->get("{$this->baseUrl}/api/pagamentos/{$pagamentoId}");
            
            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? null;
            }
            
            Log::warning("Falha ao buscar pagamento {$pagamentoId}: {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao buscar pagamento {$pagamentoId}: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Listar pagamentos por cliente
     */
    public function listarPagamentosPorCliente(int $clienteId): array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                
                ->get("{$this->baseUrl}/api/pagamentos/cliente/{$clienteId}");
            
            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }
            
            Log::warning("Falha ao listar pagamentos do cliente {$clienteId}: {$response->status()}");
            return [];
        } catch (\Exception $e) {
            Log::error("Erro ao listar pagamentos do cliente {$clienteId}: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Listar todos os pagamentos
     */
    public function listarPagamentos(): array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                
                ->get("{$this->baseUrl}/api/pagamentos");
            
            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }
            
            Log::warning("Falha ao listar pagamentos: {$response->status()}");
            return [];
        } catch (\Exception $e) {
            Log::error("Erro ao listar pagamentos: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Registrar pagamento
     */
    public function registrarPagamento(array $dados): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                
                ->post("{$this->baseUrl}/api/pagamentos", $dados);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? null;
            }

            Log::warning("Falha ao registrar pagamento: {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao registrar pagamento: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Registrar usuário no core backend
     * @throws \Exception quando o registro falha com mensagem do backend
     */
    public function registrarUsuario(array $dados): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)

                ->post("{$this->baseUrl}/api/auth/register", $dados);

            Log::info('CoreBackendClient::registrarUsuario response', [
                'status' => $response->status(),
                'body_snippet' => substr($response->body(), 0, 200),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? $data;
            }

            // Extrair mensagem de erro do backend
            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? "Erro ao registrar usuário (status {$response->status()})";

            Log::warning("Falha ao registrar usuário: {$response->status()} - {$errorMessage}");
            throw new \Exception($errorMessage);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Erro de conexão ao registrar usuário: {$e->getMessage()}");
            throw new \Exception("Erro de conexão com o servidor");
        }
    }

    /**
     * Autenticar usuário no core backend
     */
    public function autenticarUsuario(string $emailOrUsername, string $password): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                
                ->post("{$this->baseUrl}/api/auth/login", [
                    'username' => $emailOrUsername, // Backend Spring Boot usa 'username'
                    'password' => $password,
                ]);

            Log::info('CoreBackendClient::autenticarUsuario response', [
                'username' => $emailOrUsername,
                'status' => $response->status(),
                'body_snippet' => substr($response->body(), 0, 200),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? $data;
            }

            Log::warning("Falha ao autenticar usuário: {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao autenticar usuário: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Validar token no core backend
     */
    public function validarToken(string $token): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->withToken($token)
                ->connectTimeout(3)
                ->timeout(10)
                
                ->get("{$this->baseUrl}/api/auth/me");

            Log::info('CoreBackendClient::validarToken response', [
                'status' => $response->status(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? $data;
            }

            Log::warning("Falha ao validar token: {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao validar token: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Obter dados do usuário autenticado
     */
    public function obterUsuarioAutenticado(string $token): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->withToken($token)
                ->connectTimeout(3)
                ->timeout(10)
                
                ->get("{$this->baseUrl}/api/auth/me");

            Log::info('CoreBackendClient::obterUsuarioAutenticado response', [
                'status' => $response->status(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? $data;
            }

            Log::warning("Falha ao obter usuário autenticado: {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao obter usuário autenticado: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Fazer logout no core backend
     */
    public function logoutUsuario(string $token): bool
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->withToken($token)
                ->connectTimeout(3)
                ->timeout(10)
                
                ->post("{$this->baseUrl}/api/auth/logout");

            Log::info('CoreBackendClient::logoutUsuario response', [
                'status' => $response->status(),
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Erro ao fazer logout: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Criar checkout premium no core backend
     */
    public function criarCheckoutPremium(string $token): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->withToken($token)
                ->connectTimeout(5)
                ->timeout(30)
                ->post("{$this->baseUrl}/api/premium/checkout");

            Log::info('CoreBackendClient::criarCheckoutPremium response', [
                'status' => $response->status(),
                'body_snippet' => substr($response->body(), 0, 300),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? null;
            }

            Log::warning("Falha ao criar checkout premium: {$response->status()} - {$response->body()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao criar checkout premium: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Processar webhook premium no core backend
     */
    public function processarWebhookPremium(array $payload, ?string $signature, ?string $webhookSecret): bool
    {
        try {
            $url = "{$this->baseUrl}/api/premium/webhook";

            // Adicionar webhookSecret como query param se fornecido
            if ($webhookSecret) {
                $url .= "?webhookSecret=" . urlencode($webhookSecret);
            }

            $request = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(5)
                ->timeout(30);

            // Adicionar header de assinatura se fornecido
            if ($signature) {
                $request = $request->withHeaders([
                    'X-Webhook-Signature' => $signature,
                ]);
            }

            $response = $request->post($url, $payload);

            Log::info('CoreBackendClient::processarWebhookPremium response', [
                'status' => $response->status(),
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Erro ao processar webhook premium: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Ativar/desativar premium para um cliente (desenvolvimento/testes)
     */
    public function ativarPremium(int $clienteId, bool $ativar = true, int $meses = 1): ?array
    {
        try {
            $url = "{$this->baseUrl}/api/clientes/{$clienteId}/premium";
            $url .= "?ativar=" . ($ativar ? 'true' : 'false');
            $url .= "&meses={$meses}";

            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                ->post($url);

            Log::info('CoreBackendClient::ativarPremium response', [
                'clienteId' => $clienteId,
                'ativar' => $ativar,
                'status' => $response->status(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? null;
            }

            Log::warning("Falha ao ativar premium: {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao ativar premium: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Alterar senha do cliente
     */
    public function alterarSenha(int $clienteId, string $senhaAtual, string $novaSenha, string $token): bool
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->withToken($token)
                ->connectTimeout(3)
                ->timeout(10)
                ->put("{$this->baseUrl}/api/clientes/{$clienteId}/senha", [
                    'senhaAtual' => $senhaAtual,
                    'novaSenha' => $novaSenha,
                ]);

            Log::info('CoreBackendClient::alterarSenha response', [
                'clienteId' => $clienteId,
                'status' => $response->status(),
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Erro ao alterar senha: {$e->getMessage()}");
            return false;
        }
    }

    // ==================== MÉTODOS DO CHAT ====================

    /**
     * Buscar conversas do usuário
     */
    public function getChatConversas(string $token): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->withToken($token)
                ->connectTimeout(3)
                ->timeout(10)
                ->get("{$this->baseUrl}/api/chat/conversas");

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }

            Log::warning("Falha ao buscar conversas: {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao buscar conversas: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Buscar mensagens de uma conversa
     */
    public function getChatMensagens(string $token, int $conversaId, int $page = 0, int $size = 50): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->withToken($token)
                ->connectTimeout(3)
                ->timeout(10)
                ->get("{$this->baseUrl}/api/chat/conversas/{$conversaId}/mensagens", [
                    'page' => $page,
                    'size' => $size,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }

            Log::warning("Falha ao buscar mensagens: {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao buscar mensagens: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Iniciar conversa com outro usuário
     */
    public function iniciarConversa(string $token, int $outroUsuarioId): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->withToken($token)
                ->connectTimeout(3)
                ->timeout(10)
                ->post("{$this->baseUrl}/api/chat/conversas/iniciar/{$outroUsuarioId}");

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? null;
            }

            Log::warning("Falha ao iniciar conversa: {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao iniciar conversa: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Marcar mensagens como lidas
     */
    public function marcarMensagensComoLidas(string $token, int $conversaId): bool
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->withToken($token)
                ->connectTimeout(3)
                ->timeout(10)
                ->post("{$this->baseUrl}/api/chat/conversas/{$conversaId}/lidas");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Erro ao marcar mensagens: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Listar usuários Premium disponíveis para chat
     */
    public function getChatUsuarios(string $token): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->withToken($token)
                ->connectTimeout(3)
                ->timeout(10)
                ->get("{$this->baseUrl}/api/chat/usuarios");

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }

            Log::warning("Falha ao buscar usuários: {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao buscar usuários: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Listar usuários online
     */
    public function getChatUsuariosOnline(string $token): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->withToken($token)
                ->connectTimeout(3)
                ->timeout(10)
                ->get("{$this->baseUrl}/api/chat/usuarios/online");

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }

            Log::warning("Falha ao buscar usuários online: {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao buscar usuários online: {$e->getMessage()}");
            return null;
        }
    }
}
