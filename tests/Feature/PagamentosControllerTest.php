<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PagamentosControllerTest extends TestCase
{
    public function test_lista_pagamentos(): void
    {
        Http::fake([
            rtrim(config('services.core_backend.url'), '/') . '/api/pagamentos' => Http::response([
                'success' => true,
                'data' => [ ['id' => 1], ['id' => 2] ],
                'message' => 'ok'
            ], 200)
        ]);

        $this->getJson('/api/bff/pagamentos')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [ ['id' => 1], ['id' => 2] ],
            ]);
    }

    public function test_registra_pagamento_mapeando_status_legacy(): void
    {
        Http::fake([
            rtrim(config('services.core_backend.url'), '/') . '/api/pagamentos' => function (Request $request) {
                // Garante que o BFF mapeou PAGO -> APROVADO antes de enviar ao Core
                $data = $request->data();
                if (($data['status'] ?? null) !== 'APROVADO') {
                    return Http::response(['success' => false, 'data' => null, 'message' => 'status incorreto'], 400);
                }
                return Http::response([
                    'success' => true,
                    'data' => ['id' => 99, 'status' => 'APROVADO'],
                    'message' => 'ok'
                ], 201);
            }
        ]);

        $payload = [
            'clienteId' => 1,
            'valor' => 10.5,
            'metodo' => 'PIX',
            'status' => 'PAGO', // legacy do frontend
            'referenciaExterna' => 'ABC',
        ];

        $this->postJson('/api/bff/pagamentos', $payload)
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => ['id' => 99, 'status' => 'APROVADO'],
            ]);
    }
}

