<?php

namespace App\Http\Controllers;

use App\Models\PessoaJuridica;
use App\Rules\Cnpj;
use Illuminate\Http\Request;
use App\Models\Endereco;
use App\Models\Pessoa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class AcademiaController extends Controller
{
    // Painel da academia: números gerais e grade semanal de treinos
    public function dashboard() {
        $academia = $this->academiaLogada();

        $stats = [
            'alunos' => $academia->matriculas()->where('status', 'ativa')->count(),
            'assinaturas' => $academia->assinaturas()->where('assinaturas.status', 'ativa')->count(),
            'planos' => $academia->planos()->where('ativo', true)->count(),
            'treinos' => $academia->treinos()->count(),
            'receita' => $academia->assinaturas()->where('assinaturas.status', 'ativa')->sum('assinaturas.valor'),
        ];

        $treinos = $academia->treinos()->with('instrutor', 'local')->withCount('planos')
            ->orderBy('dia_semana')->orderBy('hora_inicio')->get()
            ->groupBy('dia_semana');

        $ultimasAssinaturas = $academia->assinaturas()
            ->with('plano', 'matricula.aluno.pessoa')
            ->latest('assinaturas.created_at')
            ->take(5)
            ->get();

        // Passo a passo para a academia nova saber por onde começar
        $pendencias = [
            'locais' => $academia->locais()->doesntExist(),
            'instrutores' => $academia->instrutores()->doesntExist(),
            'treinos' => $stats['treinos'] == 0,
            'planos' => $academia->planos()->doesntExist(),
        ];

        return view('academia.dashboard', compact('academia', 'stats', 'treinos', 'ultimasAssinaturas', 'pendencias'));
    }

    // Lista os alunos matriculados (ativos e inativos) com suas assinaturas
    public function alunos() {
        $academia = $this->academiaLogada();

        $matriculas = $academia->matriculas()
            ->with(['aluno.pessoa', 'assinaturas' => fn ($q) => $q->with('plano')->latest()])
            ->orderByRaw("status = 'ativa' DESC")
            ->latest()
            ->get();

        return view('academia.alunos', compact('academia', 'matriculas'));
    }

    // Mostra o formulário
    public function create() {
        return view('cadastro_academia');
    }

    // Recebe e salva os dados
    public function store(Request $request) {
        // CNPJ sem máscara e em maiúsculas antes de validar, para a checagem de unicidade
        // comparar com o que está gravado no banco.
        $request->merge([
            'email' => strtolower($request->email),
            'cnpj'  => Cnpj::normalizar($request->cnpj),
            'estado' => strtoupper((string) $request->estado),
        ]);

        // Validação: Se algo estiver errado, o Laravel volta para o formulário com o erro
        $request->validate([
            'nome' => 'required|max:45',
            'email' => 'required|email|unique:pessoas,email',
            'senha'    => 'required|min:6|confirmed', // 'confirmed' exige um campo senha_confirmation no HTML
            'cnpj' => ['required', 'unique:pessoa_juridicas,cnpj', new Cnpj()],
            'razao_social' => 'required|unique:pessoa_juridicas,razao_social',
            'nome_fantasia' => 'required|max:100',
            // RF02 (mesmo formato do RF01): (27) 99999-9999, (27) 9999-9999 ou só dígitos com DDD
            'contato' => ['required', 'string', 'regex:/^(?:\(\d{2}\) \d{4,5}-\d{4}|\d{10,11})$/'],
            'cep' => ['required', 'regex:/^\d{5}-?\d{3}$/'],
            'logradouro' => 'required|string|max:150',
            'numero' => 'required|string|max:20',
            'bairro' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|alpha|size:2',
        ], [
            'cep.regex' => 'O CEP deve ter 8 dígitos, com ou sem hífen (ex.: 29000-000 ou 29000000).',
            'contato.regex' => 'O telefone deve estar no formato (27) 99999-9999, (27) 9999-9999 ou só números com DDD (27999999999).',
            'email.unique'  => 'Este e-mail já está cadastrado em nossa base.',
            'senha.confirmed' => 'As senhas digitadas não são iguais.',
            'senha.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado.',
        ]);

        try {
            // Inicia a "sacola" de operações
            DB::beginTransaction();

            // Remove só pontos, traços e barras (mantém letras, em maiúsculas) antes de salvar
            $cnpjLimpo = strtoupper(preg_replace('/[.\/-]/', '', trim($request->cnpj)));
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

            // Registra o detalhe técnico no log; a tela recebe só uma mensagem clara
            Log::error('Falha ao cadastrar academia', ['erro' => $e->getMessage()]);

            $entrada = $request->except('senha', 'senha_confirmation');

            // Duplicidade recusada pelo banco (índice único): aponta o campo certo
            if ($e instanceof QueryException && ($e->errorInfo[1] ?? null) == 1062) {
                $detalhe = (string) ($e->errorInfo[2] ?? ''); // "Duplicate entry ... for key '...'"
                $campos = [
                    'cnpj'         => ['cnpj', 'Este CNPJ já está cadastrado.'],
                    'razao_social' => ['razao_social', 'Esta razão social já está cadastrada.'],
                    'email'        => ['email', 'Este e-mail já está cadastrado em nossa base.'],
                ];
                foreach ($campos as $indice => [$campo, $mensagem]) {
                    if (str_contains($detalhe, $indice)) {
                        return redirect()->back()->withInput($entrada)->withErrors([$campo => $mensagem]);
                    }
                }
            }

            return redirect()->back()->withInput($entrada)
                ->with('error', 'Não foi possível concluir o cadastro. Confira os dados e tente novamente.');
        }
    }  
}