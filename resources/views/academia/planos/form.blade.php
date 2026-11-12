@extends('layouts.painel')

@section('titulo', $plano ? 'Editar plano' : 'Novo plano')

@section('conteudo')
    <a href="{{ route('academia.planos.index') }}" class="text-sm text-secondary font-medium hover:underline">&larr; Voltar para planos</a>
    <h1 class="font-serif text-3xl font-bold mt-2 mb-6">{{ $plano ? 'Editar plano' : 'Novo plano' }}</h1>

    <form action="{{ $plano ? route('academia.planos.update', $plano->id) : route('academia.planos.store') }}" method="POST" class="bg-card border border-border rounded-2xl p-6 md:p-8 space-y-5">
        @csrf
        @if($plano) @method('PUT') @endif

        <div class="grid md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label for="nome" class="rotulo">Nome do plano *</label>
                <input type="text" id="nome" name="nome" value="{{ old('nome', $plano->nome ?? '') }}" placeholder="Ex: Capoeira Completo" required maxlength="100" class="campo">
            </div>
            <div>
                <label for="valor" class="rotulo">Valor (R$) *</label>
                <input type="text" inputmode="decimal" id="valor" name="valor" value="{{ old('valor', isset($plano) ? number_format($plano->valor, 2, ',', '') : '') }}" placeholder="149,90" required class="campo">
            </div>
            <div>
                <label for="periodicidade" class="rotulo">Cobrança *</label>
                <select id="periodicidade" name="periodicidade" class="campo">
                    @foreach(\App\Models\Plano::PERIODICIDADES as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected(old('periodicidade', $plano->periodicidade ?? 'mensal') === $valor)>{{ $rotulo }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="descricao" class="rotulo">Descrição</label>
                <input type="text" id="descricao" name="descricao" value="{{ old('descricao', $plano->descricao ?? '') }}" placeholder="Ex: Acesso a todas as rodas da semana" maxlength="255" class="campo">
            </div>
        </div>

        <!-- Seleção dos treinos que o plano libera -->
        <fieldset>
            <legend class="rotulo">Treinos que este plano permite praticar</legend>
            @if($treinos->isEmpty())
                <p class="text-sm bg-yellow-50 border-l-4 border-yellow-500 text-yellow-800 p-3 rounded">
                    Nenhum treino cadastrado ainda. Você pode salvar o plano agora e incluir os treinos depois.
                    <a href="{{ route('academia.treinos.create') }}" class="underline font-medium">Cadastrar treino</a>
                </p>
            @else
                <div class="grid sm:grid-cols-2 gap-2 mt-2">
                    @foreach($treinos as $treino)
                        <label class="flex items-start gap-3 border border-border rounded-lg p-3 cursor-pointer hover:border-primary has-[:checked]:border-primary has-[:checked]:bg-primary/10 transition-colors">
                            <input type="checkbox" name="treinos[]" value="{{ $treino->id }}" @checked(in_array($treino->id, $selecionados)) class="mt-1 accent-[#f0ad15]">
                            <span class="text-sm">
                                <span class="font-medium block">{{ $treino->tema }}</span>
                                <span class="text-muted-foreground">{{ $treino->dia_semana_nome }}, {{ $treino->horario }} · {{ $treino->instrutor->nome }} · {{ $treino->local->nome }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            @endif
        </fieldset>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn btn-primario">{{ $plano ? 'Salvar alterações' : 'Cadastrar plano' }}</button>
            <a href="{{ route('academia.planos.index') }}" class="btn btn-contorno">Cancelar</a>
        </div>
    </form>
@endsection
