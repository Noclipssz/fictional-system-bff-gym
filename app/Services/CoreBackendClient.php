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
    public function getCliente(int $clienteId): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                ->retry(2, 200)
                ->get("{$this->baseUrl}/api/clientes/{$clienteId}");
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
    public function listarClientes(): array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                ->retry(2, 200)
                ->get("{$this->baseUrl}/api/clientes");
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
    public function criarCliente(array $dados): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                ->retry(2, 200)
                ->post("{$this->baseUrl}/api/clientes", $dados);
            
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
    public function atualizarCliente(int $clienteId, array $dados): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                ->retry(2, 200)
                ->put("{$this->baseUrl}/api/clientes/{$clienteId}", $dados);
            
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
    public function criarTreino(array $dados): ?array
    {
        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                ->retry(2, 200)
                ->post("{$this->baseUrl}/api/treinos", $dados);
            
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
}
