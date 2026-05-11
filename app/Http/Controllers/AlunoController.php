<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Endereco;
use App\Models\Pessoa;
use App\Models\PessoaFisica;
use App\Models\PessoaJuridica;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Rules\CpfOuCnpj;

class AlunoController extends Controller
{
    // Mostra o formulário
    public function create() {
        return view('cadastro_aluno');
    }

    // Recebe e salva os dados
    public function store(Request $request) {

        // Validação: Se algo estiver errado, o Laravel volta para o formulário com o erro
        $request->validate([
            'nome'     => 'required|string|max:45',
            'cpf' => ['required', 'unique:pessoa_fisicas,cpf', new CpfOuCnpj],
            'contato'  => ['required', 'string', 'regex:/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/'], // Aceita (27) 99999-9999 ou 27999999999
            'email'    => 'required|email|unique:pessoas,email',
            'senha'    => 'required|min:6|confirmed', // 'confirmed' exige um campo senha_confirmation no HTML
        ], [
            'contato.regex' => 'O formato do telefone deve ser (99) 99999-9999.',
            'email.unique'  => 'Este e-mail já está cadastrado em nossa base.',
            'senha.confirmed' => 'As senhas digitadas não são iguais.',
            'senha.min' => 'A senha deve ter pelo menos 6 caracteres.',
        ]);

        // Remove pontos, traços e barras antes de salvar
        $cpfLimpo = preg_replace('/[^0-9]/', '', $request->cpf);
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
        PessoaFisica::create([
            'sobrenome' => $request->sobrenome,
            'cpf' => $cpfLimpo,
            'role' => 'aluno', // Role padrão
            'pessoa_id' => $pessoa->id
        ]);

        return "Aluno cadastrado com sucesso nas 3 tabelas!";
    }

    // Lista todas as academias
    public function listarAcademias() {
        $academias = PessoaJuridica::all();

        // Pega o ID da pessoa logada e busca a ficha física dela
        //$pessoaFisica = PessoaFisica::find(1);  escolhe uma pessoa fisica especifica
        $pessoaFisica = Auth::user()->pessoaFisica;
        $minhasMatriculas = $pessoaFisica->academias()->pluck('pessoa_juridica_id')->toArray();

        return view('lista_academias', compact('academias', 'minhasMatriculas'));
    }

    // Realiza o vínculo (Matrícula)
    public function matricular($academia_id) {
        // Por enquanto, como não temos login, vamos simular que somos o Aluno ID 1
        $aluno = Auth::user()->pessoaFisica;
        // $aluno = PessoaFisica::find(1); Especifica um aluno para mostrar

        if (!$aluno) {
        return redirect()->back()->with('error', 'Aluno não encontrado.');
    }

        // O método syncWithoutDetaching evita duplicar a mesma matrícula se clicar duas vezes
        $aluno->academias()->syncWithoutDetaching([$academia_id]);

        return "Matrícula na academia " . $academia_id . " realizada com sucesso!";
    }

    
}