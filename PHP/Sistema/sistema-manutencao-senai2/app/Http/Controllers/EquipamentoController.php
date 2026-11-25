<?php

namespace App\Http\Controllers;

use App\Models\Equipamento;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EquipamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View {
        return view('equipamentos.index', [
            'equipamentos' => Equipamento::query()->latest()->get(),
            'titulo'       => 'Parque de Máquinas e Equipamentos Industriais',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View {
        return view('equipamentos.create', [
            'titulo' => 'Cadastrar Equipamento',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse {
        Equipamento::create($this->validar($request));

        return redirect()->route('equipamentos.index')
            ->with('mensagem_sucesso', 'Equipamento cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipamento $equipamento): View {
        return view('equipamentos.edit', [
            'equipamento' => $equipamento,
            'titulo'      => 'Editar Equipamento',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
     public function destroy(Equipamento $equipamento): RedirectResponse {
        $equipamento->delete();

        return redirect()->route('equipamentos.index')
            ->with('mensagem_sucesso', 'Equipamento removido com sucesso!');
    }

    private function validar(Request $request, ?int $id = null): array {
        $regraTag = 'required|string|max:255|unique:equipamentos,tag' . ($id ? ",{$id}" : '');

        return $request->validate([
            'tag'         => $regraTag,
            'nome'        => 'required|string|max:255',
            'setor'       => 'required|string|max:255',
            'criticidade' => 'required|in:BAIXA,MEDIA,ALTA,CRITICA',
            'status'      => 'required|in:OPERANDO,MANUTENCAO,PARADO',
        ]);
    }
}
