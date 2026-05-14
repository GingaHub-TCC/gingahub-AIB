<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Endereco;
use App\Models\Pessoa;
use App\Models\PessoaFisica;
use App\Models\PessoaJuridica;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Rules\Cpf;
use Illuminate\Support\Facades\DB;

class AlunoController extends Controller
{
    // Mostra o formulário
    public function create() {
        return view('cadastro_aluno');
    }

    // Recebe e salva os dados
    public function store(Request $request) {
        $request->merge(['email' => strtolower($request->email)]);
        
        // Validação: Se algo estiver errado, o Laravel volta para o formulário com o erro
        $request->validate([
            'nome'     => 'required|string|max:45',
            'cpf' => ['required', 'unique:pessoa_fisicas,cpf', new Cpf()],
            'contato'  => ['required', 'string', 'regex:/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/'], // Aceita (27) 99999-9999 ou 27999999999
            'email'    => 'required|email|unique:pessoas,email',
            'senha'    => 'required|min:6|confirmed', // 'confirmed' exige um campo senha_confirmation no HTML
        ], [
            'contato.regex' => 'O formato do telefone deve ser (99) 99999-9999.',
            'email.unique'  => 'Este e-mail já está cadastrado em nossa base.',
            'senha.confirmed' => 'As senhas digitadas não são iguais.',
            'senha.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
        ]);

        // Remove pontos, traços e barras antes de salvar
        $cpfLimpo = preg_replace('/[^0-9]/', '', $request->cpf);
        $contatoLimpo = preg_replace('/[^0-9]/', '', $request->contato);
        $cepLimpo = preg_replace('/[^0-9]/', '', $request->cep);

        try {
            // Inicia a "sacola" de operações
            DB::beginTransaction();

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
            $aluno = new PessoaFisica([
                'sobrenome' => $request->sobrenome,
                'cpf'       => $cpfLimpo,
                'pessoa_id' => $pessoa->id
            ]);

            // Como 'role' NÃO é fillable, você define manualmente aqui:
            $aluno->role = 'aluno'; 

            $aluno->save();

            DB::commit();
            return redirect()->back()->with('success', 'Aluno cadastrado com sucesso!');
        } catch (\Exception $e) {
            // Se QUALQUER erro acontecer (SQL, PHP, etc.), desfaz tudo o que foi feito acima
            DB::rollBack();

            // Logar o erro para saber o que aconteceu
            return redirect()->back()->with('error', 'Falha ao cadastrar: ' . $e->getMessage());
        }
    }

    // Lista todas as academias
    public function listarAcademias() {
        $academias = PessoaJuridica::all();

        $pessoaFisica = Auth::user()->pessoaFisica;

        // Em vez de pluck, pegamos a coleção completa e usamos keyBy('id')
        // Isso cria um array onde a chave é o ID da academia e o valor são os dados (incluindo o pivot)
        $minhasMatriculas = $pessoaFisica->academias->keyBy('id');

        return view('lista_academias', compact('academias', 'minhasMatriculas'));
    }

    // Realiza o vínculo (Matrícula)
    public function matricular($academia_id) {
        $aluno = Auth::user()->pessoaFisica;

        if (!$aluno) {
            return redirect()->back()->with('error', 'Aluno não encontrado.');
        }

        // 1. Verifica se já existe qualquer registro (Ativo ou Inativo)
        $registroExistente = $aluno->academias()->where('pessoa_juridica_id', $academia_id)->first();

        if ($registroExistente) {
            // Se já estiver ativo, não faz nada
            if ($registroExistente->pivot->status === 'ativa') {
                return redirect()->back()->with('warning', 'Você já possui uma matrícula ativa nesta academia.');
            }

            // 2. REATIVAÇÃO: Se estava inativo, volta para 'ativa'
            $aluno->academias()->updateExistingPivot($academia_id, [
                'status' => 'ativa',
                'data_cancelamento' => null
            ]);

            return redirect()->back()->with('success', 'Bem-vindo de volta! Sua matrícula foi reativada.');
        }

        // 3. NOVA MATRÍCULA: Se nunca existiu, cria com status padrão
        $aluno->academias()->attach($academia_id, ['status' => 'ativa']);

        return redirect()->back()->with('success', 'Matrícula realizada com sucesso na academia ') . $academia_id;
    }

    public function desmatricular($id) {
        $aluno = Auth::user()->pessoaFisica;

        // Estado: Não apagar apenas mudar a 'flag' de ativado para inativado
        $aluno->academias()->updateExistingPivot($id, [
            'status' => 'inativa',
            'data_cancelamento' => now()
        ]);

        return redirect()->back()->with('success', 'Vínculo encerrado com sucesso.');
    }
}