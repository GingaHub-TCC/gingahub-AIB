@extends('layouts.painel')

@section('titulo', 'Instrutores')

@section('conteudo')
    <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
        <div>
            <h1 class="font-serif text-3xl md:text-4xl font-bold mb-2">Instrutores</h1>
            <p class="text-muted-foreground">Mestres, professores e monitores que conduzem os treinos.</p>
        </div>
        <a href="{{ route('academia.instrutores.create') }}" class="btn btn-primario">+ Novo instrutor</a>
    </div>

    <div class="bg-card rounded-2xl border border-border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-muted text-foreground font-semibold text-sm border-b border-border">
                        <th class="px-6 py-4">Nome</th>
                        <th class="px-6 py-4">Graduação</th>
                        <th class="px-6 py-4">Contato</th>
                        <th class="px-6 py-4">Treinos</th>
                        <th class="px-6 py-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($instrutores as $instrutor)
                        <tr class="hover:bg-input/10 transition-colors">
                            <td class="px-6 py-4 font-medium">{{ $instrutor->nome }}</td>
                            <td class="px-6 py-4 text-sm">{{ $instrutor->graduacao ?: '-' }}</td>
                            <td class="px-6 py-4 text-sm text-muted-foreground">{{ $instrutor->contato ?: '-' }}</td>
                            <td class="px-6 py-4 text-sm">{{ $instrutor->treinos_count }}</td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('academia.instrutores.edit', $instrutor->id) }}" class="btn btn-contorno btn-pequeno">Editar</a>
                                <form action="{{ route('academia.instrutores.destroy', $instrutor->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Excluir este instrutor?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-perigo btn-pequeno">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-16 text-center text-muted-foreground">Nenhum instrutor cadastrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
