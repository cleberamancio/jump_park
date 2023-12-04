<?php

use App\Http\Controllers\Api\AuthenticatedSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceOrderController;
use App\Http\Controllers\Api\RegisteredUserController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    #Uma requisição de listagem com filtro e paginação.
    Route::get('/service-order', [ServiceOrderController::class, 'index']);
    #Uma requisição de criação de ordem de serviço.
    Route::post('/service-order', [ServiceOrderController::class, 'store']);
    #Uma requisição de edição de ordem de serviço.
    Route::post('/service-order/edit', [ServiceOrderController::class, 'update']);
    #Uma requisição de remoção lógica da ordem de serviço.
    Route::post('/service-order/delete', [ServiceOrderController::class, 'destroy']);
});
Route::post('/user', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'auth']);