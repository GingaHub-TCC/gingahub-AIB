@extends('layouts.painel')

@section('titulo', 'Locais')

@section('conteudo')
    <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
        <div>
            <h1 class="font-serif text-3xl md:text-4xl font-bold mb-2">Locais</h1>
            <p class="text-muted-foreground">Unidades, salas ou espaços onde os treinos acontecem.</p>
        </div>
        <a href="{{ route('academia.locais.create') }}" class="btn btn-primario">+ Novo local</a>
    </div>

    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($locais as $local)
            <div class="bg-card border border-border rounded-2xl p-6 flex flex-col">
                <h2 class="font-serif text-xl font-semibold mb-2">{{ $local->nome }}</h2>
                <p class="text-sm text-muted-foreground flex-1">
                    {{ $local->endereco->logradouro }}, {{ $local->endereco->numero }}{{ $local->endereco->complemento ? ' - ' . $local->endereco->complemento : '' }}<br>
                    {{ $local->endereco->bairro }} · {{ $local->endereco->cidade }}/{{ $local->endereco->estado }}<br>
                    CEP {{ substr($local->endereco->cep, 0, 5) }}-{{ substr($local->endereco->cep, 5) }}
                </p>
                <p class="text-sm mt-3 mb-4">{{ $local->treinos_count }} treino(s) neste local</p>
                <div class="flex gap-2">
                    <a href="{{ route('academia.locais.edit', $local->id) }}" class="btn btn-contorno btn-pequeno">Editar</a>
                    <form action="{{ route('academia.locais.destroy', $local->id) }}" method="POST" onsubmit="return confirm('Excluir este local?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-perigo btn-pequeno">Excluir</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 xl:col-span-3 bg-card border border-border rounded-2xl py-16 text-center text-muted-foreground">Nenhum local cadastrado.</div>
        @endforelse
    </div>
@endsection
