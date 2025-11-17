<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\TreinosController;
use App\Http\Controllers\PagamentosController;
use App\Http\Controllers\PerfilController;
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

// Autenticação
Route::prefix('/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/revoke-all', [AuthController::class, 'revokeAll']);
    });
});

// Rotas protegidas por autenticação
Route::middleware('auth:sanctum')->group(function () {

    // Perfil - Agregação de dados (Cliente + Treinos + Pagamentos)
    Route::get('/bff/perfil/{clienteId}', [PerfilController::class, 'show']);

    // Clientes
    Route::prefix('/bff/clientes')->group(function () {
        Route::get('/', [ClientesController::class, 'index']);
        Route::get('/{id}', [ClientesController::class, 'show']);
        Route::post('/', [ClientesController::class, 'store']);
        Route::put('/{id}', [ClientesController::class, 'update']);
    });

    // Treinos
    Route::prefix('/bff/treinos')->group(function () {
        Route::get('/', [TreinosController::class, 'index']);
        Route::get('/{id}', [TreinosController::class, 'show']);
        Route::get('/cliente/{clienteId}', [TreinosController::class, 'porCliente']);
        Route::post('/', [TreinosController::class, 'store']);
    });

    // Pagamentos
    Route::prefix('/bff/pagamentos')->group(function () {
        Route::get('/', [PagamentosController::class, 'index']);
        Route::get('/{id}', [PagamentosController::class, 'show']);
        Route::get('/cliente/{clienteId}', [PagamentosController::class, 'porCliente']);
        Route::post('/', [PagamentosController::class, 'store']);
    });

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
