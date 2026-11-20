@extends('layouts.painel')

@section('titulo', 'Planos')

@section('conteudo')
    <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
        <div>
            <h1 class="font-serif text-3xl md:text-4xl font-bold mb-2">Planos</h1>
            <p class="text-muted-foreground">Os planos que seus alunos podem assinar e quais treinos cada um libera.</p>
        </div>
        <a href="{{ route('academia.planos.create') }}" class="btn btn-primario">+ Novo plano</a>
    </div>

    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($planos as $plano)
            <div class="bg-card border border-border rounded-2xl p-6 flex flex-col {{ $plano->ativo ? '' : 'opacity-70' }}">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <h2 class="font-serif text-xl font-semibold">{{ $plano->nome }}</h2>
                    @if($plano->ativo)
                        <span class="text-xs border border-border text-foreground font-medium px-2 py-0.5 rounded">À venda</span>
                    @else
                        <span class="text-xs bg-muted text-muted-foreground font-medium px-2 py-0.5 rounded">Desativado</span>
                    @endif
                </div>
                <p class="mb-3"><span class="font-serif text-2xl font-bold">{{ $plano->valor_formatado }}</span> <span class="text-sm text-muted-foreground">/ {{ strtolower(\App\Models\Plano::PERIODICIDADES[$plano->periodicidade]) }}</span></p>
                @if($plano->descricao)
                    <p class="text-sm text-muted-foreground mb-3">{{ $plano->descricao }}</p>
                @endif

                <p class="text-sm font-semibold text-foreground mb-2">Treinos liberados ({{ $plano->treinos->count() }})</p>
                <ul class="text-sm space-y-1 mb-4 flex-1">
                    @forelse($plano->treinos as $treino)
                        <li>{{ $treino->tema }} <span class="text-muted-foreground numeros">({{ \Illuminate\Support\Str::limit($treino->dia_semana_nome, 3, '') }} {{ substr($treino->hora_inicio, 0, 5) }})</span></li>
                    @empty
                        <li class="text-yellow-800">Nenhum treino selecionado.</li>
                    @endforelse
                </ul>

                <p class="text-sm text-muted-foreground mb-4">{{ $plano->assinaturas_ativas_count }} assinatura(s) ativa(s)</p>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('academia.planos.edit', $plano->id) }}" class="btn btn-contorno btn-pequeno">Editar</a>
                    <form action="{{ route('academia.planos.status', $plano->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-pequeno {{ $plano->ativo ? 'btn-contorno' : 'btn-secundario' }}">{{ $plano->ativo ? 'Desativar' : 'Ativar' }}</button>
                    </form>
                    <form action="{{ route('academia.planos.destroy', $plano->id) }}" method="POST" onsubmit="return confirm('Excluir o plano {{ addslashes($plano->nome) }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-perigo btn-pequeno">Excluir</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 xl:col-span-3 bg-card border border-border rounded-2xl py-16 text-center">
                <h3 class="font-serif text-xl font-semibold mb-1">Nenhum plano cadastrado</h3>
                <p class="text-muted-foreground mb-4">Crie o primeiro plano para os alunos poderem assinar.</p>
                <a href="{{ route('academia.planos.create') }}" class="btn btn-primario">+ Novo plano</a>
            </div>
        @endforelse
    </div>
@endsection
