<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BffAuthController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\TreinosController;
use App\Http\Controllers\PagamentosController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\FrequenciaController;
use App\Http\Controllers\PremiumController;
use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rotas do BFF Unificado

// Autenticação (antiga - mantida para compatibilidade, mas não recomendada)
// NOTA: Use as rotas /bff/auth/* que fazem proxy correto para o backend
Route::prefix('/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/revoke-all', [AuthController::class, 'revokeAll']);
    });
});

// BFF - Autenticação (FAZ PROXY PARA O BACKEND - USE ESTAS ROTAS!)
Route::prefix('/bff/auth')->group(function () {
    Route::post('/cadastro', [BffAuthController::class, 'cadastro']);
    Route::post('/login', [BffAuthController::class, 'login']);
    Route::get('/me', [BffAuthController::class, 'me']);
    Route::post('/validar-token', [BffAuthController::class, 'validarToken']);
    Route::post('/logout', [BffAuthController::class, 'logout']);
});

// BFF - Rotas de Perfil (SEM autenticação Sanctum - o backend valida)
// O frontend envia o token JWT do backend no header Authorization
// IMPORTANTE: O backend valida que o usuário só pode atualizar o próprio perfil
Route::prefix('/bff/clientes')->group(function () {
    Route::put('/{id}', [ClientesController::class, 'update']);
    // Rota de desenvolvimento para ativar premium manualmente
    Route::post('/{id}/premium', [ClientesController::class, 'ativarPremium']);
    // Alterar senha
    Route::put('/{id}/senha', [ClientesController::class, 'alterarSenha']);
});

// BFF - Rotas de Frequências (Registro de treinos realizados)
Route::prefix('/bff/frequencias')->group(function () {
    Route::post('/', [FrequenciaController::class, 'store']);
});

// BFF - Treinos (SEM autenticação Sanctum - o backend valida)
Route::prefix('/bff/treinos')->group(function () {
    Route::get('/', [TreinosController::class, 'index']);
    Route::get('/{id}', [TreinosController::class, 'show']);
    Route::get('/cliente/{clienteId}', [TreinosController::class, 'porCliente']);
    Route::post('/', [TreinosController::class, 'store']);
});

// BFF - Pagamentos (SEM autenticação Sanctum - o backend valida)
Route::prefix('/bff/pagamentos')->group(function () {
    Route::get('/', [PagamentosController::class, 'index']);
    Route::get('/{id}', [PagamentosController::class, 'show']);
    Route::get('/cliente/{clienteId}', [PagamentosController::class, 'porCliente']);
    Route::post('/', [PagamentosController::class, 'store']);
});

// BFF - Premium (Assinatura via AbacatePay)
// checkout: requer JWT no header Authorization
// webhook: público, validado pelo core via secret
Route::prefix('/bff/premium')->group(function () {
    Route::post('/checkout', [PremiumController::class, 'checkout']);
    Route::post('/webhook', [PremiumController::class, 'webhook']);
});

// BFF - Chat (requer JWT no header Authorization)
// Apenas para operações REST (histórico, conversas)
// WebSocket vai direto para o backend Java
Route::prefix('/bff/chat')->group(function () {
    Route::get('/conversas', [ChatController::class, 'listarConversas']);
    Route::get('/conversas/{conversaId}/mensagens', [ChatController::class, 'buscarMensagens']);
    Route::post('/conversas/iniciar/{outroUsuarioId}', [ChatController::class, 'iniciarConversa']);
    Route::post('/conversas/{conversaId}/lidas', [ChatController::class, 'marcarComoLidas']);
    Route::get('/usuarios', [ChatController::class, 'listarUsuarios']);
    Route::get('/usuarios/online', [ChatController::class, 'listarUsuariosOnline']);
});

// Rotas protegidas por autenticação Sanctum (antigas - manter por enquanto)
Route::middleware('auth:sanctum')->group(function () {

    // Perfil - Agregação de dados (Cliente + Treinos + Pagamentos)
    Route::get('/bff/perfil/{clienteId}', [PerfilController::class, 'show']);

    // Dados do usuário autenticado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Health Check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'BFF Unificado está operacional',
        'timestamp' => now(),
    ]);
});
