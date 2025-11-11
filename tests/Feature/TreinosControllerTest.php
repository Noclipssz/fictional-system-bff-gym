<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TreinosControllerTest extends TestCase
{
    public function test_lista_treinos(): void
    {
        Http::fake([
            rtrim(config('services.core_backend.url'), '/') . '/api/treinos' => Http::response([
                'success' => true,
                'data' => [
                    ['id' => 1, 'titulo' => 'A'],
                    ['id' => 2, 'titulo' => 'B'],
                ],
                'message' => 'ok'
            ], 200)
        ]);

        $this->getJson('/api/bff/treinos')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    ['id' => 1, 'titulo' => 'A'],
                    ['id' => 2, 'titulo' => 'B'],
                ],
            ]);
    }

    public function test_treinos_por_cliente(): void
    {
        Http::fake([
            rtrim(config('services.core_backend.url'), '/') . '/api/treinos/cliente/5' => Http::response([
                'success' => true,
                'data' => [ ['id' => 11, 'clienteId' => 5] ],
                'message' => 'ok'
            ], 200)
        ]);

        $this->getJson('/api/bff/treinos/cliente/5')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [ ['id' => 11, 'clienteId' => 5] ],
            ]);
    }

    public function test_cria_treino(): void
    {
        Http::fake([
            rtrim(config('services.core_backend.url'), '/') . '/api/treinos' => Http::response([
                'success' => true,
                'data' => ['id' => 100, 'titulo' => 'Novo Treino'],
                'message' => 'ok'
            ], 201)
        ]);

        $payload = [
            'clienteId' => 1,
            'titulo' => 'Novo Treino',
            'descricao' => 'Desc',
            'nivel' => 'INTERMEDIARIO',
        ];

        $this->postJson('/api/bff/treinos', $payload)
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => ['id' => 100, 'titulo' => 'Novo Treino'],
            ]);
    }
}

