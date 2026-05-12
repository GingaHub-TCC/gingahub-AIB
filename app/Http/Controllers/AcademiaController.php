<?php

namespace App\Http\Controllers;

use App\Models\PessoaJuridica;
use App\Rules\Cnpj;
use Illuminate\Http\Request;
use App\Models\Endereco;
use App\Models\Pessoa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AcademiaController extends Controller
{
    // Mostra o formulário
    public function create() {
        return view('cadastro_academia');
    }

    // Recebe e salva os dados
    public function store(Request $request) {
        $request->merge(['email' => strtolower($request->email)]);

        // Validação: Se algo estiver errado, o Laravel volta para o formulário com o erro
        $request->validate([
            'nome' => 'required|max:45',
            'email' => 'required|email|unique:pessoas,email',
            'senha'    => 'required|min:6|confirmed', // 'confirmed' exige um campo senha_confirmation no HTML
            'cnpj' => ['required', 'unique:pessoa_juridicas,cnpj', new Cnpj()],
            'razao_social' => 'required|unique:pessoa_juridicas,razao_social',
            'nome_fantasia' => 'required|max:100',
            'cep' => 'required',
        ], [
            'contato.regex' => 'O formato do telefone deve ser (99) 99999-9999.',
            'email.unique'  => 'Este e-mail já está cadastrado em nossa base.',
            'senha.confirmed' => 'As senhas digitadas não são iguais.',
            'senha.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado.',
        ]);

        try {
            // Inicia a "sacola" de operações
            DB::beginTransaction();

            // Remove pontos, traços e barras antes de salvar
            $cnpjLimpo = preg_replace('/[^0-9]/', '', $request->cnpj);
            $contatoLimpo = preg_replace('/[^0-9]/', '', $request->contato);
            $cepLimpo = preg_replace('/[^0-9]/', '', $request->cep);

            // Criar o Endereço
            $endereco = Endereco::create(array_merge($request->all(), ['cep' => $cepLimpo]));

            // Criar a Pessoa vinculada ao endereço
            $pessoa = Pessoa::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'senha' => Hash::make($request->senha), // Criptografa a senha!
                'contato' => $contatoLimpo,
                'endereco_id' => $endereco->id
            ]);

            // Criar a Pessoa Física vinculada à Pessoa
            PessoaJuridica::create([
                'nome_fantasia' => $request->nome_fantasia,
                'razao_social' => $request->razao_social,
                'cnpj' => $cnpjLimpo,
                'pessoa_id' => $pessoa->id
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Academia cadastrada com sucesso!');
        } catch (\Exception $e) {
            // Se QUALQUER erro acontecer (SQL, PHP, etc.), desfaz tudo o que foi feito acima
            DB::rollBack();

            // Logar o erro para saber o que aconteceu
            return redirect()->back()->with('error', 'Falha ao cadastrar: ' . $e->getMessage());
        }
    }  
}