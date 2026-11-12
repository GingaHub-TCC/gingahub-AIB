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

    // Painel do aluno: suas matrículas e assinaturas em todas as academias
    public function dashboard() {
        $aluno = Auth::user()->pessoaFisica;

        $matriculas = $aluno->matriculas()
            ->with(['academia.pessoa.endereco', 'assinaturas' => fn ($q) => $q->with('plano.treinos.instrutor', 'plano.treinos.local')->latest()])
            ->orderByRaw("status = 'ativa' DESC")
            ->latest()
            ->get();

        $ativas = $matriculas->where('status', 'ativa');
        $assinaturasAtivas = $ativas->flatMap->assinaturas->where('status', 'ativa');

        // Grade semanal com os treinos liberados pelos planos ativos (sem repetir)
        $treinosLiberados = collect();
        foreach ($ativas as $matricula) {
            foreach ($matricula->assinaturas->where('status', 'ativa') as $assinatura) {
                foreach ($assinatura->plano->treinos as $treino) {
                    $treinosLiberados->put($treino->id, ['treino' => $treino, 'academia' => $matricula->academia->nome_fantasia]);
                }
            }
        }
        $treinosLiberados = $treinosLiberados->sortBy(fn ($item) => $item['treino']->dia_semana . $item['treino']->hora_inicio);

        return view('aluno.dashboard', compact('aluno', 'matriculas', 'ativas', 'assinaturasAtivas', 'treinosLiberados'));
    }

    // Lista todas as academias
    public function listarAcademias() {
        $academias = PessoaJuridica::with('pessoa.endereco')->get();

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

        // Garante que a academia existe (evita matrícula em ID inventado)
        $academia = PessoaJuridica::findOrFail($academia_id);

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

            // As assinaturas antigas continuam inativas: o aluno escolhe os planos de novo
            return redirect()->back()->with('success', 'Bem-vindo de volta! Sua matrícula na ' . $academia->nome_fantasia . ' foi reativada.');
        }

        // 3. NOVA MATRÍCULA: Se nunca existiu, cria com status padrão
        $aluno->academias()->attach($academia_id, ['status' => 'ativa']);

        return redirect()->back()->with('success', 'Matrícula realizada com sucesso na ' . $academia->nome_fantasia . '!');
    }

    public function desmatricular($id) {
        $aluno = Auth::user()->pessoaFisica;

        $matricula = $aluno->matriculaAtivaEm($id);

        if (!$matricula) {
            return redirect()->back()->with('warning', 'Você não possui matrícula ativa nesta academia.');
        }

        // A matrícula e as assinaturas mudam juntas: ou tudo, ou nada
        DB::transaction(function () use ($matricula) {
            // Estado: Não apagar apenas mudar a 'flag' de ativado para inativado
            $matricula->update([
                'status' => 'inativa',
                'data_cancelamento' => now()
            ]);

            // Toda assinatura ativa daquela matrícula também é inativada
            $matricula->assinaturasAtivas()->update([
                'status' => 'inativa',
                'data_cancelamento' => now()
            ]);
        });

        // Esvazia o carrinho daquela academia, se houver
        session()->forget('carrinho.' . $id);

        return redirect()->back()->with('success', 'Vínculo encerrado com sucesso. Suas assinaturas nesta academia foram inativadas.');
    }

    // Cancela apenas UMA assinatura, mantendo a matrícula
    public function cancelarAssinatura($id) {
        $aluno = Auth::user()->pessoaFisica;

        // Só encontra se a assinatura for do próprio aluno logado
        $assinatura = $aluno->assinaturas()->where('assinaturas.id', $id)->firstOrFail();

        if ($assinatura->status !== 'ativa') {
            return redirect()->back()->with('warning', 'Esta assinatura já está inativa.');
        }

        $assinatura->status = 'inativa';
        $assinatura->data_cancelamento = now();
        $assinatura->save();

        return redirect()->back()->with('success', 'Assinatura do plano "' . $assinatura->plano->nome . '" cancelada.');
    }
}