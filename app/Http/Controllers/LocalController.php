<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocalController extends Controller
{
    public function index() {
        $locais = $this->academiaLogada()->locais()->with('endereco')->withCount('treinos')->orderBy('nome')->get();

        return view('academia.locais.index', compact('locais'));
    }

    public function create() {
        return view('academia.locais.form', ['local' => null]);
    }

    public function store(Request $request) {
        $dados = $this->validar($request);

        DB::transaction(function () use ($dados) {
            $endereco = Endereco::create($dados['endereco']);

            $local = $this->academiaLogada()->locais()->make(['nome' => $dados['nome']]);
            $local->endereco_id = $endereco->id;
            $local->save();
        });

        return redirect()->route('academia.locais.index')->with('success', 'Local cadastrado com sucesso!');
    }

    public function edit($id) {
        $local = $this->academiaLogada()->locais()->with('endereco')->findOrFail($id);

        return view('academia.locais.form', compact('local'));
    }

    public function update(Request $request, $id) {
        $local = $this->academiaLogada()->locais()->findOrFail($id);
        $dados = $this->validar($request);

        DB::transaction(function () use ($local, $dados) {
            $local->update(['nome' => $dados['nome']]);
            $local->endereco->update($dados['endereco']);
        });

        return redirect()->route('academia.locais.index')->with('success', 'Local atualizado com sucesso!');
    }

    public function destroy($id) {
        $local = $this->academiaLogada()->locais()->findOrFail($id);

        if ($local->treinos()->exists()) {
            return redirect()->back()->with('error', 'Existem treinos neste local. Mude o local desses treinos antes de excluir.');
        }

        DB::transaction(function () use ($local) {
            $endereco = $local->endereco;
            $local->delete();
            $endereco?->delete();
        });

        return redirect()->back()->with('success', 'Local excluído.');
    }

    private function validar(Request $request) {
        $request->validate([
            'nome' => 'required|string|max:100',
            'cep' => ['required', 'regex:/^\d{5}-?\d{3}$/'],
            'logradouro' => 'required|string|max:150',
            'numero' => 'required|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|alpha|size:2',
        ], [
            'cep.regex' => 'O CEP deve ter 8 dígitos, com ou sem hífen (ex.: 29000-000 ou 29000000).',
            'estado.size' => 'Use a sigla do estado (ex: ES).',
        ]);

        return [
            'nome' => $request->nome,
            'endereco' => [
                'cep' => preg_replace('/[^0-9]/', '', $request->cep),
                'logradouro' => $request->logradouro,
                'numero' => $request->numero,
                'complemento' => $request->complemento,
                'bairro' => $request->bairro,
                'cidade' => $request->cidade,
                'estado' => strtoupper($request->estado),
            ],
        ];
    }
}
