<?php

declare(strict_types=1);

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FornecedorController;
use Illuminate\Support\Facades\Route;

// Exercício 1: Rota com múltiplos parâmetros e constraints compostas
Route::get('/clientes/{cliente}/contratos/{ano}', [ClienteController::class, 'contratosPorAno'])
    ->whereNumber('cliente')
    ->where('ano', '[0-9]{4}')
    ->name('clientes.contratos.ano');

// Exercício 2: Resource Controller parcial somente leitura para fornecedores
Route::resource('fornecedores', 'App\Http\Controllers\FornecedorController')
    ->only(['index', 'show']);

// Exercício 3: Rota de telemetria de rede com leitura de cabeçalhos HTTP
Route::get('/clientes/telemetria/origem', [ClienteController::class, 'origemRequisicao'])
    ->name('clientes.origem');

// Exercício 4: Grupo de rotas administrativas com prefixo, nome e middleware
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function (): void {
        Route::get('/auditoria/clientes', [ClienteController::class, 'auditoriaAtivos'])
            ->name('auditoria.clientes');
    });

// Exercício 5: Alteração parcial de status utilizando verbo PATCH
Route::patch('/clientes/{id}/status', [ClienteController::class, 'alterarStatus'])
    ->whereNumber('id')
    ->name('clientes.status.alterar');