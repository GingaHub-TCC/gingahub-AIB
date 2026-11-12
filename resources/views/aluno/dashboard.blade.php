@extends('layouts.painel')

@section('titulo', 'Meu painel')

@section('conteudo')
    <div class="mb-8">
        <p class="text-sm uppercase tracking-wider text-muted-foreground mb-1">Meu painel</p>
        <h1 class="font-serif text-3xl md:text-4xl font-bold">Olá, {{ Auth::user()->nome }}!</h1>
    </div>

    <!-- Números gerais -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <div class="bg-card border border-border rounded-2xl p-5">
            <p class="text-sm text-muted-foreground">Academias ativas</p>
            <p class="font-serif text-3xl font-bold mt-1">{{ $ativas->count() }}</p>
        </div>
        <div class="bg-card border border-border rounded-2xl p-5">
            <p class="text-sm text-muted-foreground">Planos ativos</p>
            <p class="font-serif text-3xl font-bold mt-1">{{ $assinaturasAtivas->count() }}</p>
        </div>
        <div class="bg-card border border-border rounded-2xl p-5 col-span-2 lg:col-span-1">
            <p class="text-sm text-muted-foreground">Treinos liberados por semana</p>
            <p class="font-serif text-3xl font-bold mt-1">{{ $treinosLiberados->count() }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-5 gap-6 items-start">

        <!-- Minhas academias -->
        <section class="lg:col-span-3 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-serif text-2xl font-semibold">Minhas academias</h2>
                <a href="{{ route('academias.listar') }}" class="btn btn-primario btn-pequeno">Encontrar academias</a>
            </div>

            @forelse($matriculas as $matricula)
                @php $assinaturasDaMatricula = $matricula->assinaturas->where('status', 'ativa'); @endphp
                <article class="bg-card border border-border rounded-2xl p-6 {{ $matricula->status === 'ativa' ? '' : 'opacity-70' }}">
                    <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                        <div>
                            <h3 class="font-serif text-xl font-semibold">{{ $matricula->academia->nome_fantasia }}</h3>
                            <p class="text-sm text-muted-foreground">
                                {{ $matricula->academia->pessoa->endereco->cidade }}/{{ $matricula->academia->pessoa->endereco->estado }}
                                ·
                                @if($matricula->status === 'ativa')
                                    <span class="text-secondary font-medium">Matrícula ativa</span>
                                @else
                                    Matrícula inativa desde {{ $matricula->data_cancelamento?->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                        @if($matricula->status === 'ativa')
                            <a href="{{ route('aluno.academia', $matricula->pessoa_juridica_id) }}" class="btn btn-secundario btn-pequeno">Abrir academia</a>
                        @else
                            <form action="{{ route('aluno.matricular', $matricula->pessoa_juridica_id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-contorno btn-pequeno">Rematricular-se</button>
                            </form>
                        @endif
                    </div>

                    @if($matricula->status === 'ativa')
                        @forelse($assinaturasDaMatricula as $assinatura)
                            <div class="flex items-center justify-between text-sm border-t border-border py-2">
                                <span><span class="font-medium">{{ $assinatura->plano->nome }}</span> <span class="text-muted-foreground">· R$ {{ number_format($assinatura->valor, 2, ',', '.') }} {{ strtolower(\App\Models\Plano::PERIODICIDADES[$assinatura->plano->periodicidade]) }}</span></span>
                                <span class="text-muted-foreground">desde {{ $assinatura->data_inicio->format('d/m/Y') }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-muted-foreground border-t border-border pt-3">Nenhum plano assinado. <a href="{{ route('aluno.academia', $matricula->pessoa_juridica_id) }}" class="text-secondary font-medium underline">Ver planos</a></p>
                        @endforelse
                    @endif
                </article>
            @empty
                <div class="bg-card border border-border rounded-2xl py-16 px-4 text-center">
                    <h3 class="font-serif text-xl font-semibold mb-1">Você ainda não está em nenhuma roda</h3>
                    <p class="text-muted-foreground mb-4">Encontre uma academia e faça sua matrícula.</p>
                    <a href="{{ route('academias.listar') }}" class="btn btn-primario">Ver academias</a>
                </div>
            @endforelse
        </section>

        <!-- Grade semanal -->
        <section class="lg:col-span-2 bg-card border border-border rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-border">
                <h2 class="font-serif text-xl font-semibold">Minha semana</h2>
                <p class="text-sm text-muted-foreground">Treinos liberados pelos seus planos</p>
            </div>
            <ul class="divide-y divide-border">
                @forelse($treinosLiberados as $item)
                    <li class="px-6 py-3 text-sm">
                        <div class="flex justify-between gap-2">
                            <span class="font-medium">{{ $item['treino']->tema }}</span>
                            <span class="font-mono text-muted-foreground">{{ substr($item['treino']->hora_inicio, 0, 5) }}</span>
                        </div>
                        <div class="text-muted-foreground">{{ $item['treino']->dia_semana_nome }} · {{ $item['treino']->local->nome }} · {{ $item['treino']->instrutor->nome }}</div>
                        <div class="text-xs text-accent">{{ $item['academia'] }}</div>
                    </li>
                @empty
                    <li class="px-6 py-10 text-center text-sm text-muted-foreground">Assine um plano para ver seus treinos aqui.</li>
                @endforelse
            </ul>
        </section>
    </div>
@endsection
