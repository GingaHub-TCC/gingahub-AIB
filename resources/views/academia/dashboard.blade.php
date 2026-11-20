@extends('layouts.painel')

@section('titulo', 'Painel da Academia')

@section('conteudo')
    <div class="mb-8">
        <h1 class="font-serif text-3xl md:text-4xl font-bold mb-2">{{ $academia->nome_fantasia }}</h1>
        <p class="text-muted-foreground">Alunos, assinaturas, planos e grade de treinos da academia.</p>
    </div>

    <!-- Primeiros passos (some quando tudo estiver cadastrado) -->
    @if(in_array(true, $pendencias, true))
        <div class="bg-card border border-border rounded-2xl p-6 mb-8">
            <h2 class="font-serif text-xl font-semibold mb-1">Primeiros passos</h2>
            <p class="text-sm text-muted-foreground mb-4">Siga a ordem abaixo para deixar sua academia pronta para receber assinaturas.</p>
            <ol class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach([
                    ['locais', '1. Cadastre os locais', 'academia.locais.create'],
                    ['instrutores', '2. Cadastre os instrutores', 'academia.instrutores.create'],
                    ['treinos', '3. Monte os treinos', 'academia.treinos.create'],
                    ['planos', '4. Crie os planos', 'academia.planos.create'],
                ] as [$chave, $texto, $rota])
                    <li>
                        @if($pendencias[$chave])
                            <a href="{{ route($rota) }}" class="block border border-border rounded-lg px-4 py-3 hover:border-primary hover:bg-primary/10 transition-colors font-medium text-sm">{{ $texto }}</a>
                        @else
                            <span class="block border border-border bg-muted text-muted-foreground rounded-lg px-4 py-3 font-medium text-sm">Feito: {{ substr($texto, 3) }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    @endif

    <!-- Números gerais -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach([
            ['Alunos ativos', $stats['alunos'], route('academia.alunos')],
            ['Assinaturas ativas', $stats['assinaturas'], route('academia.alunos')],
            ['Planos à venda', $stats['planos'], route('academia.planos.index')],
            ['Treinos semanais', $stats['treinos'], route('academia.treinos.index')],
        ] as [$rotulo, $valor, $link])
            <a href="{{ $link }}" class="bg-card border border-border rounded-2xl p-5 hover:border-primary transition-colors">
                <p class="text-sm text-muted-foreground">{{ $rotulo }}</p>
                <p class="font-serif text-3xl font-bold mt-1">{{ $valor }}</p>
            </a>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Grade semanal -->
        <section class="lg:col-span-2 bg-card border border-border rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h2 class="font-serif text-xl font-semibold">Grade semanal</h2>
                <a href="{{ route('academia.treinos.create') }}" class="btn btn-primario btn-pequeno">+ Novo treino</a>
            </div>

            @forelse($treinos as $dia => $treinosDoDia)
                <div class="px-6 py-4 border-b border-border last:border-0">
                    <p class="text-sm font-semibold text-foreground mb-2">{{ \App\Models\Treino::DIAS_SEMANA[$dia] }}</p>
                    <ul class="space-y-2">
                        @foreach($treinosDoDia as $treino)
                            <li class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
                                <span class="numeros font-medium w-28">{{ $treino->horario }}</span>
                                <span class="font-medium">{{ $treino->tema }}</span>
                                <span class="text-muted-foreground">com {{ $treino->instrutor->nome }}, em {{ $treino->local->nome }}</span>
                                @if($treino->planos_count == 0)
                                    <span class="text-xs border border-border text-foreground px-2 py-0.5 rounded">Fora de todos os planos</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p class="px-6 py-10 text-center text-muted-foreground">Nenhum treino cadastrado ainda.</p>
            @endforelse
        </section>

        <!-- Últimas assinaturas -->
        <section class="bg-card border border-border rounded-2xl overflow-hidden self-start">
            <div class="px-6 py-4 border-b border-border">
                <h2 class="font-serif text-xl font-semibold">Últimas assinaturas</h2>
                <p class="text-sm text-muted-foreground">Receita ativa: <strong class="text-foreground">R$ {{ number_format($stats['receita'], 2, ',', '.') }}</strong></p>
            </div>
            <ul class="divide-y divide-border">
                @forelse($ultimasAssinaturas as $assinatura)
                    <li class="px-6 py-3 text-sm">
                        <div class="flex justify-between gap-2">
                            <span class="font-medium">{{ $assinatura->matricula->aluno->pessoa->nome }} {{ $assinatura->matricula->aluno->sobrenome }}</span>
                            <span class="numeros {{ $assinatura->status === 'ativa' ? 'text-secondary' : 'text-muted-foreground line-through' }}">R$ {{ number_format($assinatura->valor, 2, ',', '.') }}</span>
                        </div>
                        <div class="text-muted-foreground">{{ $assinatura->plano->nome }}, desde {{ $assinatura->created_at->format('d/m/Y') }}</div>
                    </li>
                @empty
                    <li class="px-6 py-10 text-center text-muted-foreground">Nenhuma assinatura ainda.</li>
                @endforelse
            </ul>
        </section>
    </div>
@endsection
