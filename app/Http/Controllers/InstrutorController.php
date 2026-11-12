<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstrutorController extends Controller
{
    public function index() {
        $instrutores = $this->academiaLogada()->instrutores()->withCount('treinos')->orderBy('nome')->get();

        return view('academia.instrutores.index', compact('instrutores'));
    }

    public function create() {
        return view('academia.instrutores.form', ['instrutor' => null]);
    }

    public function store(Request $request) {
        $dados = $this->validar($request);

        // A academia é definida pelo servidor, nunca pelo formulário
        $this->academiaLogada()->instrutores()->create($dados);

        return redirect()->route('academia.instrutores.index')->with('success', 'Instrutor cadastrado com sucesso!');
    }

    public function edit($id) {
        $instrutor = $this->academiaLogada()->instrutores()->findOrFail($id);

        return view('academia.instrutores.form', compact('instrutor'));
    }

    public function update(Request $request, $id) {
        $instrutor = $this->academiaLogada()->instrutores()->findOrFail($id);
        $instrutor->update($this->validar($request));

        return redirect()->route('academia.instrutores.index')->with('success', 'Instrutor atualizado com sucesso!');
    }

    public function destroy($id) {
        $instrutor = $this->academiaLogada()->instrutores()->findOrFail($id);

        if ($instrutor->treinos()->exists()) {
            return redirect()->back()->with('error', 'Este instrutor está em treinos cadastrados. Troque o instrutor desses treinos antes de excluir.');
        }

        $instrutor->delete();

        return redirect()->back()->with('success', 'Instrutor excluído.');
    }

    private function validar(Request $request) {
        $dados = $request->validate([
            'nome' => 'required|string|max:100',
            'graduacao' => 'nullable|string|max:45',
            'contato' => ['nullable', 'string', 'regex:/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/'],
        ], [
            'contato.regex' => 'O formato do telefone deve ser (99) 99999-9999.',
        ]);

        // Remove pontos, traços e parênteses antes de salvar
        $dados['contato'] = $dados['contato'] ? preg_replace('/[^0-9]/', '', $dados['contato']) : null;

        return $dados;
    }
}
