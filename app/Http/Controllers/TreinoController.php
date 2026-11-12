<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TreinoController extends Controller
{
    public function index() {
        $treinos = $this->academiaLogada()->treinos()
            ->with('instrutor', 'local', 'planos')
            ->orderBy('dia_semana')->orderBy('hora_inicio')
            ->get();

        return view('academia.treinos.index', compact('treinos'));
    }

    public function create() {
        return $this->formulario(null);
    }

    public function store(Request $request) {
        $this->academiaLogada()->treinos()->create($this->validar($request));

        return redirect()->route('academia.treinos.index')->with('success', 'Treino cadastrado com sucesso!');
    }

    public function edit($id) {
        return $this->formulario($this->academiaLogada()->treinos()->findOrFail($id));
    }

    public function update(Request $request, $id) {
        $treino = $this->academiaLogada()->treinos()->findOrFail($id);
        $treino->update($this->validar($request));

        return redirect()->route('academia.treinos.index')->with('success', 'Treino atualizado com sucesso!');
    }

    public function destroy($id) {
        $treino = $this->academiaLogada()->treinos()->findOrFail($id);

        // O vínculo com os planos (plano_treino) é apagado em cascata
        $treino->delete();

        return redirect()->back()->with('success', 'Treino excluído e removido dos planos.');
    }

    private function formulario($treino) {
        $academia = $this->academiaLogada();
        $instrutores = $academia->instrutores()->orderBy('nome')->get();
        $locais = $academia->locais()->orderBy('nome')->get();

        // Sem instrutor ou local não dá para montar um treino
        if ($instrutores->isEmpty() || $locais->isEmpty()) {
            return redirect()->route('academia.treinos.index')
                ->with('warning', 'Cadastre ao menos um instrutor e um local antes de criar treinos.');
        }

        return view('academia.treinos.form', compact('treino', 'instrutores', 'locais'));
    }

    private function validar(Request $request) {
        $academiaId = $this->academiaLogada()->id;

        return $request->validate([
            'tema' => 'required|string|max:100',
            'descricao' => 'nullable|string|max:255',
            // Instrutor e local precisam ser DESTA academia
            'instrutor_id' => ['required', Rule::exists('instrutores', 'id')->where('pessoa_juridica_id', $academiaId)],
            'local_id' => ['required', Rule::exists('locais', 'id')->where('pessoa_juridica_id', $academiaId)],
            'dia_semana' => 'required|integer|between:0,6',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i|after:hora_inicio',
        ], [
            'hora_fim.after' => 'O horário de término deve ser depois do início.',
            'instrutor_id.exists' => 'Instrutor inválido.',
            'local_id.exists' => 'Local inválido.',
        ]);
    }
}
