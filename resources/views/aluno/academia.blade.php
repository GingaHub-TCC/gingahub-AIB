@extends('layouts.painel')

@section('titulo', $academia->nome_fantasia)

@section('conteudo')
    <a href="{{ route('academias.listar') }}" class="text-sm text-secondary font-medium hover:underline">&larr; Voltar para academias</a>

    <div class="flex flex-wrap items-end justify-between gap-4 mt-2 mb-8">
        <div>
            <h1 class="font-serif text-3xl md:text-4xl font-bold mb-1">{{ $academia->nome_fantasia }}</h1>
            <p class="text-muted-foreground">
                {{ $academia->pessoa->endereco->cidade }}/{{ $academia->pessoa->endereco->estado }}
                · Matriculado desde {{ $matricula->data_matricula?->format('d/m/Y') }}
            </p>
        </div>
        <form action="{{ route('aluno.desmatricular', $academia->id) }}" method="POST" onsubmit="return confirm('Desmatricular-se desta academia? Todas as suas assinaturas aqui serão inativadas.')">
            @csrf
            <button type="submit" class="btn btn-contorno btn-pequeno">Desmatricular-se</button>
        </form>
    </div>

    <div class="grid lg:grid-cols-3 gap-6 items-start">

        <!-- Vitrine de planos -->
        <section class="lg:col-span-2 space-y-6">
            <h2 class="font-serif text-2xl font-semibold">Planos disponíveis</h2>

            @forelse($planos as $plano)
                @php
                    $assinado = in_array($plano->id, $planosAssinados);
                    $noCarrinho = $carrinho->contains('id', $plano->id);
                @endphp
                <article class="bg-card border rounded-2xl p-6 {{ $assinado ? 'border-secondary' : ($noCarrinho ? 'border-primary' : 'border-border') }}">
                    <div class="flex flex-wrap items-start justify-between gap-4 mb-3">
                        <div>
                            <h3 class="font-serif text-xl font-semibold">{{ $plano->nome }}</h3>
                            @if($plano->descricao)
                                <p class="text-sm text-muted-foreground">{{ $plano->descricao }}</p>
                            @endif
                        </div>
                        <p class="text-right">
                            <span class="font-serif text-2xl font-bold">{{ $plano->valor_formatado }}</span><br>
                            <span class="text-sm text-muted-foreground">{{ strtolower(\App\Models\Plano::PERIODICIDADES[$plano->periodicidade]) }}</span>
                        </p>
                    </div>

                    <p class="text-xs font-semibold uppercase tracking-wider text-accent mb-2">Treinos incluídos</p>
                    @if($plano->treinos->isEmpty())
                        <p class="text-sm text-muted-foreground mb-4">A academia ainda não vinculou treinos a este plano.</p>
                    @else
                        <ul class="grid sm:grid-cols-2 gap-2 mb-4">
                            @foreach($plano->treinos as $treino)
                                <li class="text-sm bg-background border border-border rounded-lg px-3 py-2">
                                    <span class="font-medium block">{{ $treino->tema }}</span>
                                    <span class="text-muted-foreground">{{ $treino->dia_semana_nome }}, {{ $treino->horario }}</span><br>
                                    <span class="text-muted-foreground">{{ $treino->instrutor->nome }} · {{ $treino->local->nome }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if($assinado)
                        <span class="inline-flex items-center gap-1 text-sm font-medium text-secondary">✓ Você já assina este plano</span>
                    @elseif($noCarrinho)
                        <form action="{{ route('carrinho.remover', [$academia->id, $plano->id]) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-contorno btn-pequeno">Remover do carrinho</button>
                        </form>
                    @else
                        <form action="{{ route('carrinho.adicionar', [$academia->id, $plano->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primario btn-pequeno">+ Adicionar ao carrinho</button>
                        </form>
                    @endif
                </article>
            @empty
                <div class="bg-card border border-border rounded-2xl py-16 text-center text-muted-foreground">
                    Esta academia ainda não tem planos à venda. Volte mais tarde!
                </div>
            @endforelse
        </section>

        <!-- Carrinho e assinaturas -->
        <aside class="space-y-6 lg:sticky lg:top-8">
            <div class="bg-card border border-border rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h2 class="font-serif text-xl font-semibold">Carrinho</h2>
                    <span class="text-sm text-muted-foreground">{{ $carrinho->count() }} item(ns)</span>
                </div>

                @if($carrinho->isEmpty())
                    <p class="px-6 py-8 text-sm text-center text-muted-foreground">Seu carrinho está vazio. Escolha um ou mais planos ao lado.</p>
                @else
                    <ul class="divide-y divide-border">
                        @foreach($carrinho as $item)
                            <li class="px-6 py-3 flex items-center justify-between gap-3 text-sm">
                                <span>
                                    <span class="font-medium block">{{ $item->nome }}</span>
                                    <span class="text-muted-foreground">{{ \App\Models\Plano::PERIODICIDADES[$item->periodicidade] }}</span>
                                </span>
                                <span class="flex items-center gap-2">
                                    <span class="font-medium">{{ $item->valor_formatado }}</span>
                                    <form action="{{ route('carrinho.remover', [$academia->id, $item->id]) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-700 hover:underline" title="Remover">✕</button>
                                    </form>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="px-6 py-4 border-t border-border bg-muted/50">
                        <div class="flex justify-between font-semibold mb-4">
                            <span>Total</span>
                            <span>R$ {{ number_format($total, 2, ',', '.') }}</span>
                        </div>
                        <form action="{{ route('carrinho.finalizar', $academia->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-secundario w-full">Concluir pedido</button>
                        </form>
                        <p class="text-xs text-muted-foreground mt-2 text-center">Pagamento simulado: o pedido é aprovado na hora.</p>
                    </div>
                @endif
            </div>

            <div class="bg-card border border-border rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                    <h2 class="font-serif text-xl font-semibold">Minhas assinaturas aqui</h2>
                </div>
                <ul class="divide-y divide-border">
                    @forelse($assinaturasAtivas as $assinatura)
                        <li class="px-6 py-3 text-sm flex items-center justify-between gap-3">
                            <span>
                                <span class="font-medium block">{{ $assinatura->plano->nome }}</span>
                                <span class="text-muted-foreground">desde {{ $assinatura->data_inicio->format('d/m/Y') }}</span>
                            </span>
                            <form action="{{ route('aluno.assinatura.cancelar', $assinatura->id) }}" method="POST" onsubmit="return confirm('Cancelar a assinatura do plano {{ addslashes($assinatura->plano->nome) }}?')">
                                @csrf
                                <button type="submit" class="text-red-700 hover:underline text-xs">Cancelar</button>
                            </form>
                        </li>
                    @empty
                        <li class="px-6 py-6 text-sm text-center text-muted-foreground">Nenhuma assinatura ativa nesta academia.</li>
                    @endforelse
                </ul>
            </div>
        </aside>
    </div>
@endsection
