<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PerfilControllerTest extends TestCase
{
    public function test_perfil_agrega_cliente_treinos_pagamentos(): void
    {
        $base = rtrim(config('services.core_backend.url'), '/');
        Http::fake([
            $base . '/api/clientes/1' => Http::response([
                'success' => true,
                'data' => ['id' => 1, 'nome' => 'João'],
                'message' => 'ok'
            ], 200),
            $base . '/api/treinos/cliente/1' => Http::response([
                'success' => true,
                'data' => [
                    ['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4], ['id' => 5], ['id' => 6],
                ],
                'message' => 'ok'
            ], 200),
            $base . '/api/pagamentos/cliente/1' => Http::response([
                'success' => true,
                'data' => [ ['id' => 10], ['id' => 11] ],
                'message' => 'ok'
            ], 200),
        ]);

        $this->getJson('/api/bff/perfil/1')
            ->assertStatus(200)
            ->assertJsonPath('data.cliente.id', 1)
            ->assertJsonPath('data.treinosRecentes.0.id', 1)
            ->assertJsonPath('data.historicoPagamentos.0.id', 10)
            ->assertJsonCount(5, 'data.treinosRecentes'); // limitado a 5
    }
}
