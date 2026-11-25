<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    /**
     * Exercício 1: Exibe os contratos de um cliente filtrados por ano.
     */
    public function contratosPorAno(int $cliente, int $ano): View
    {
        $registro = Cliente::findOrFail($cliente);

        $contratos = $registro->contratos()
            ->whereYear('created_at', $ano)
            ->get();

        return view('clientes.contratos', [
            'cliente' => $registro,
            'ano' => $ano,
            'contratos' => $contratos,
        ]);
    }

    /**
     * Exercício 3: Retorna metadados de telemetria da requisição atual em JSON.
     */
    public function origemRequisicao(Request $request): JsonResponse
    {
        return response()->json([
            'ip_cliente' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Exercício 4: Retorna a quantidade total de parceiros com status ATIVO.
     */
    public function auditoriaAtivos(): JsonResponse
    {
        $totalAtivos = Cliente::where('status', 'ATIVO')->count();

        return response()->json([
            'parceiros_ativos' => $totalAtivos,
        ]);
    }

    /**
     * Exercício 5: Alterna o status do cliente entre ATIVO e SUSPENSO.
     */
    public function alterarStatus(Request $request, int $id): RedirectResponse
    {
        $cliente = Cliente::findOrFail($id);

        $cliente->status = $cliente->status === 'ATIVO' ? 'SUSPENSO' : 'ATIVO';
        $cliente->save();

        return redirect()
            ->route('clientes.index')
            ->with('sucesso', "Status do cliente alterado para {$cliente->status}.");
    }
}