<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ClientesControllerTest extends TestCase
{
    public function test_lista_clientes_agrega_resposta_do_core(): void
    {
        Http::fake([
            rtrim(config('services.core_backend.url'), '/') . '/api/clientes' => Http::response([
                'success' => true,
                'data' => [
                    ['id' => 1, 'nome' => 'João'],
                    ['id' => 2, 'nome' => 'Maria'],
                ],
                'message' => 'ok'
            ], 200)
        ]);

        $resp = $this->getJson('/api/bff/clientes');
        $resp->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    ['id' => 1, 'nome' => 'João'],
                    ['id' => 2, 'nome' => 'Maria'],
                ],
            ]);
    }

    public function test_busca_cliente_por_id(): void
    {
        Http::fake([
            rtrim(config('services.core_backend.url'), '/') . '/api/clientes/1' => Http::response([
                'success' => true,
                'data' => ['id' => 1, 'nome' => 'João'],
                'message' => 'ok'
            ], 200)
        ]);

        $resp = $this->getJson('/api/bff/clientes/1');
        $resp->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ['id' => 1, 'nome' => 'João'],
            ]);
    }

    public function test_cria_cliente_repasse_ao_core(): void
    {
        Http::fake([
            rtrim(config('services.core_backend.url'), '/') . '/api/clientes' => Http::response([
                'success' => true,
                'data' => ['id' => 10, 'nome' => 'Novo'],
                'message' => 'ok'
            ], 201)
        ]);

        $payload = [
            'nome' => 'Novo',
            'email' => 'novo@example.com',
            'telefone' => '11999990000',
            'cpf' => '12345678900',
            'endereco' => 'Rua X, 123',
            'dataNascimento' => '1990-01-01',
            'premium' => false,
            'avatarDataUrl' => null,
        ];

        $resp = $this->postJson('/api/bff/clientes', $payload);
        $resp->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => ['id' => 10, 'nome' => 'Novo'],
            ]);
    }
}

