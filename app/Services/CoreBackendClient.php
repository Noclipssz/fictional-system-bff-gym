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
     * Buscar cliente por ID
     */
    public function getCliente(int $clienteId, ?string $token = null): ?array
    {
        try {
            $request = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                ->retry(2, 200);

            // Adicionar token se fornecido
            if ($token) {
                $request = $request->withToken($token);
            }

            $response = $request->get("{$this->baseUrl}/api/clientes/{$clienteId}");

            Log::info('CoreBackendClient::getCliente response', [
                'id' => $clienteId,
                'status' => $response->status(),
                'body_snippet' => substr($response->body(), 0, 200),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? null;
            }

            Log::warning("Falha ao buscar cliente {$clienteId}: {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao buscar cliente {$clienteId}: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Listar todos os clientes
     */
    public function listarClientes(?string $token = null): array
    {
        try {
            $request = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                ->retry(2, 200);

            // Adicionar token se fornecido
            if ($token) {
                $request = $request->withToken($token);
            }

            $response = $request->get("{$this->baseUrl}/api/clientes");

            Log::info('CoreBackendClient::listarClientes response', [
                'status' => $response->status(),
                'body_snippet' => substr($response->body(), 0, 200),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }

            Log::warning("Falha ao listar clientes: {$response->status()}");
            return [];
        } catch (\Exception $e) {
            Log::error("Erro ao listar clientes: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Criar cliente
     */
    public function criarCliente(array $dados, ?string $token = null): ?array
    {
        try {
            $request = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                ->retry(2, 200);

            // Adicionar token se fornecido
            if ($token) {
                $request = $request->withToken($token);
            }

            $response = $request->post("{$this->baseUrl}/api/clientes", $dados);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? null;
            }

            Log::warning("Falha ao criar cliente: {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao criar cliente: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Atualizar cliente
     */
    public function atualizarCliente(int $clienteId, array $dados, ?string $token = null): ?array
    {
        try {
            $request = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                ->retry(2, 200);

            // Adicionar token se fornecido
            if ($token) {
                $request = $request->withToken($token);
            }

            $response = $request->put("{$this->baseUrl}/api/clientes/{$clienteId}", $dados);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? null;
            }

            Log::warning("Falha ao atualizar cliente {$clienteId}: {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar cliente {$clienteId}: {$e->getMessage()}");
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
                ->retry(2, 200)
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
    public function listarTreinosPorCliente(int $clienteId): array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                ->retry(2, 200)
                ->get("{$this->baseUrl}/api/treinos/cliente/{$clienteId}");
            
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
                ->retry(2, 200)
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
                ->retry(2, 200);

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
                ->retry(2, 200)
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
                ->retry(2, 200)
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
                ->retry(2, 200)
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
                ->retry(2, 200)
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
     */
    public function registrarUsuario(array $dados): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                ->retry(2, 200)
                ->post("{$this->baseUrl}/api/auth/register", $dados);

            Log::info('CoreBackendClient::registrarUsuario response', [
                'status' => $response->status(),
                'body_snippet' => substr($response->body(), 0, 200),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? $data;
            }

            Log::warning("Falha ao registrar usuário: {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao registrar usuário: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Autenticar usuário no core backend
     */
    public function autenticarUsuario(string $email, string $password): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                ->retry(2, 200)
                ->post("{$this->baseUrl}/api/auth/login", [
                    'email' => $email,
                    'password' => $password,
                ]);

            Log::info('CoreBackendClient::autenticarUsuario response', [
                'email' => $email,
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
                ->retry(2, 200)
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
                ->retry(2, 200)
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
}
