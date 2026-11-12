@extends('layouts.painel')

@section('titulo', $instrutor ? 'Editar instrutor' : 'Novo instrutor')

@section('conteudo')
    <a href="{{ route('academia.instrutores.index') }}" class="text-sm text-secondary font-medium hover:underline">&larr; Voltar para instrutores</a>
    <h1 class="font-serif text-3xl font-bold mt-2 mb-6">{{ $instrutor ? 'Editar instrutor' : 'Novo instrutor' }}</h1>

    <form action="{{ $instrutor ? route('academia.instrutores.update', $instrutor->id) : route('academia.instrutores.store') }}" method="POST" class="bg-card border border-border rounded-2xl p-6 md:p-8 space-y-5 max-w-2xl">
        @csrf
        @if($instrutor) @method('PUT') @endif

        <div>
            <label for="nome" class="rotulo">Nome *</label>
            <input type="text" id="nome" name="nome" value="{{ old('nome', $instrutor->nome ?? '') }}" placeholder="Ex: Mestre Pastinha" required maxlength="100" class="campo">
        </div>
        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label for="graduacao" class="rotulo">Graduação</label>
                <input type="text" id="graduacao" name="graduacao" value="{{ old('graduacao', $instrutor->graduacao ?? '') }}" placeholder="Ex: Contramestre, Faixa Preta" maxlength="45" class="campo">
            </div>
            <div>
                <label for="contato" class="rotulo">Telefone</label>
                <input type="text" id="contato" name="contato" value="{{ old('contato', $instrutor->contato ?? '') }}" placeholder="(27) 99999-9999" class="campo">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn btn-primario">{{ $instrutor ? 'Salvar alterações' : 'Cadastrar instrutor' }}</button>
            <a href="{{ route('academia.instrutores.index') }}" class="btn btn-contorno">Cancelar</a>
        </div>
    </form>
@endsection
