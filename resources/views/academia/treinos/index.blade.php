@extends('layouts.painel')

@section('titulo', 'Treinos')

@section('conteudo')
    <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
        <div>
            <h1 class="font-serif text-3xl md:text-4xl font-bold mb-2">Treinos</h1>
            <p class="text-muted-foreground">Aulas da semana com tema, horário, instrutor e local.</p>
        </div>
        <a href="{{ route('academia.treinos.create') }}" class="btn btn-primario">+ Novo treino</a>
    </div>

    <div class="bg-card rounded-2xl border border-border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-muted text-foreground font-semibold text-sm border-b border-border">
                        <th class="px-6 py-4">Tema</th>
                        <th class="px-6 py-4">Dia e horário</th>
                        <th class="px-6 py-4">Instrutor</th>
                        <th class="px-6 py-4">Local</th>
                        <th class="px-6 py-4">Planos</th>
                        <th class="px-6 py-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($treinos as $treino)
                        <tr class="hover:bg-input/10 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-medium">{{ $treino->tema }}</p>
                                @if($treino->descricao)<p class="text-sm text-muted-foreground">{{ $treino->descricao }}</p>@endif
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">{{ $treino->dia_semana_nome }}<br><span class="text-muted-foreground">{{ $treino->horario }}</span></td>
                            <td class="px-6 py-4 text-sm">{{ $treino->instrutor->nome }}</td>
                            <td class="px-6 py-4 text-sm">{{ $treino->local->nome }}</td>
                            <td class="px-6 py-4 text-sm">
                                @forelse($treino->planos as $plano)
                                    <span class="inline-block border border-border px-2 py-0.5 rounded mr-1 mb-1">{{ $plano->nome }}</span>
                                @empty
                                    <span class="text-yellow-800">Nenhum</span>
                                @endforelse
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('academia.treinos.edit', $treino->id) }}" class="btn btn-contorno btn-pequeno">Editar</a>
                                <form action="{{ route('academia.treinos.destroy', $treino->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Excluir este treino? Ele também sairá dos planos.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-perigo btn-pequeno">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-muted-foreground">
                                Nenhum treino cadastrado. Lembre-se: antes é preciso ter ao menos um
                                <a href="{{ route('academia.instrutores.index') }}" class="text-secondary font-medium underline">instrutor</a> e um
                                <a href="{{ route('academia.locais.index') }}" class="text-secondary font-medium underline">local</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
