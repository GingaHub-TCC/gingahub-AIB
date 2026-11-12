@extends('layouts.painel')

@section('titulo', $treino ? 'Editar treino' : 'Novo treino')

@section('conteudo')
    <a href="{{ route('academia.treinos.index') }}" class="text-sm text-secondary font-medium hover:underline">&larr; Voltar para treinos</a>
    <h1 class="font-serif text-3xl font-bold mt-2 mb-6">{{ $treino ? 'Editar treino' : 'Novo treino' }}</h1>

    <form action="{{ $treino ? route('academia.treinos.update', $treino->id) : route('academia.treinos.store') }}" method="POST" class="bg-card border border-border rounded-2xl p-6 md:p-8 space-y-5">
        @csrf
        @if($treino) @method('PUT') @endif

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label for="tema" class="rotulo">Tema *</label>
                <input type="text" id="tema" name="tema" value="{{ old('tema', $treino->tema ?? '') }}" placeholder="Ex: Capoeira Angola, Jiu-Jitsu Kids" required maxlength="100" class="campo">
            </div>
            <div>
                <label for="descricao" class="rotulo">Descrição</label>
                <input type="text" id="descricao" name="descricao" value="{{ old('descricao', $treino->descricao ?? '') }}" placeholder="Ex: Para iniciantes, a partir de 7 anos" maxlength="255" class="campo">
            </div>

            <div>
                <label for="instrutor_id" class="rotulo">Instrutor *</label>
                <select id="instrutor_id" name="instrutor_id" required class="campo">
                    @foreach($instrutores as $instrutor)
                        <option value="{{ $instrutor->id }}" @selected(old('instrutor_id', $treino->instrutor_id ?? null) == $instrutor->id)>{{ $instrutor->nome }}{{ $instrutor->graduacao ? ' (' . $instrutor->graduacao . ')' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="local_id" class="rotulo">Local *</label>
                <select id="local_id" name="local_id" required class="campo">
                    @foreach($locais as $local)
                        <option value="{{ $local->id }}" @selected(old('local_id', $treino->local_id ?? null) == $local->id)>{{ $local->nome }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="dia_semana" class="rotulo">Dia da semana *</label>
                <select id="dia_semana" name="dia_semana" required class="campo">
                    @foreach(\App\Models\Treino::DIAS_SEMANA as $numero => $nome)
                        <option value="{{ $numero }}" @selected((string) old('dia_semana', $treino->dia_semana ?? 1) === (string) $numero)>{{ $nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="hora_inicio" class="rotulo">Início *</label>
                    <input type="time" id="hora_inicio" name="hora_inicio" value="{{ old('hora_inicio', isset($treino) ? substr($treino->hora_inicio, 0, 5) : '') }}" required class="campo">
                </div>
                <div>
                    <label for="hora_fim" class="rotulo">Término *</label>
                    <input type="time" id="hora_fim" name="hora_fim" value="{{ old('hora_fim', isset($treino) ? substr($treino->hora_fim, 0, 5) : '') }}" required class="campo">
                </div>
            </div>
        </div>

        <p class="text-sm text-muted-foreground">Para incluir este treino em um plano, edite o plano em <a href="{{ route('academia.planos.index') }}" class="text-secondary underline">Planos</a>.</p>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn btn-primario">{{ $treino ? 'Salvar alterações' : 'Cadastrar treino' }}</button>
            <a href="{{ route('academia.treinos.index') }}" class="btn btn-contorno">Cancelar</a>
        </div>
    </form>
@endsection
