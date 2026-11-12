<?php

namespace App\Http\Controllers;

use App\Models\Plano;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PlanoController extends Controller
{
    public function index() {
        $planos = $this->academiaLogada()->planos()
            ->with(['treinos' => fn ($q) => $q->orderBy('dia_semana')->orderBy('hora_inicio')])
            ->withCount(['assinaturas as assinaturas_ativas_count' => fn ($q) => $q->where('status', 'ativa')])
            ->orderByDesc('ativo')->orderBy('valor')
            ->get();

        return view('academia.planos.index', compact('planos'));
    }

    public function create() {
        return $this->formulario(null);
    }

    public function store(Request $request) {
        $dados = $this->validar($request);

        DB::transaction(function () use ($dados) {
            $plano = $this->academiaLogada()->planos()->create($dados);

            // Quais treinos o plano libera
            $plano->treinos()->sync($dados['treinos'] ?? []);
        });

        return redirect()->route('academia.planos.index')->with('success', 'Plano cadastrado com sucesso!');
    }

    public function edit($id) {
        return $this->formulario($this->academiaLogada()->planos()->with('treinos')->findOrFail($id));
    }

    public function update(Request $request, $id) {
        $plano = $this->academiaLogada()->planos()->findOrFail($id);
        $dados = $this->validar($request);

        DB::transaction(function () use ($plano, $dados) {
            $plano->update($dados);
            $plano->treinos()->sync($dados['treinos'] ?? []);
        });

        return redirect()->route('academia.planos.index')->with('success', 'Plano atualizado com sucesso!');
    }

    // Ativa/desativa: plano desativado some da vitrine, mas não mexe em quem já assinou
    public function alternarStatus($id) {
        $plano = $this->academiaLogada()->planos()->findOrFail($id);
        $plano->ativo = !$plano->ativo;
        $plano->save();

        return redirect()->back()->with('success', 'Plano "' . $plano->nome . '" ' . ($plano->ativo ? 'ativado' : 'desativado') . '.');
    }

    public function destroy($id) {
        $plano = $this->academiaLogada()->planos()->findOrFail($id);

        // Plano com histórico de assinaturas não pode sumir: só ser desativado
        if ($plano->assinaturas()->exists()) {
            return redirect()->back()->with('error', 'Este plano já foi assinado por alunos. Desative-o em vez de excluir.');
        }

        $plano->delete();

        return redirect()->back()->with('success', 'Plano excluído.');
    }

    private function formulario($plano) {
        $treinos = $this->academiaLogada()->treinos()->with('instrutor', 'local')
            ->orderBy('dia_semana')->orderBy('hora_inicio')->get();

        return view('academia.planos.form', [
            'plano' => $plano,
            'treinos' => $treinos,
            'selecionados' => old('treinos', $plano ? $plano->treinos->pluck('id')->all() : []),
        ]);
    }

    private function validar(Request $request) {
        // Aceita o formato brasileiro "1.149,90" além de "1149.90"
        if (str_contains((string) $request->valor, ',')) {
            $request->merge(['valor' => str_replace(['.', ','], ['', '.'], $request->valor)]);
        }

        return $request->validate([
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string|max:255',
            'valor' => 'required|numeric|min:0|max:999999',
            'periodicidade' => ['required', Rule::in(array_keys(Plano::PERIODICIDADES))],
            'treinos' => 'nullable|array',
            // Só treinos DESTA academia
            'treinos.*' => [Rule::exists('treinos', 'id')->where('pessoa_juridica_id', $this->academiaLogada()->id)],
        ], [
            'treinos.*.exists' => 'Um dos treinos selecionados é inválido.',
        ]);
    }
}
