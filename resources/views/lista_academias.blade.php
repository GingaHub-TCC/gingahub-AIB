@extends('layouts.painel')

@section('titulo', 'Academias Disponíveis')

@section('conteudo')
    <!-- Título da Página -->
    <div class="mb-8">
        <h1 class="font-serif text-3xl md:text-4xl font-bold text-foreground mb-2">Academias Parceiras</h1>
        <p class="text-muted-foreground text-lg">Encontre onde treinar. Escolha uma academia abaixo para se matricular.</p>
    </div>

    <!-- Container da Tabela -->
    <div class="bg-card rounded-2xl border border-border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-muted text-foreground font-semibold text-sm border-b border-border">
                        <th class="px-6 py-4">Nome Fantasia</th>
                        <th class="px-6 py-4">Cidade</th>
                        <th class="px-6 py-4 text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">

                    @foreach($academias as $academia)
                    <tr class="hover:bg-input/10 transition-colors group">
                        <td class="px-6 py-4 font-medium text-foreground">
                            {{ $academia->nome_fantasia }}
                        </td>
                        <td class="px-6 py-4 text-muted-foreground">
                            <div class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ $academia->pessoa->endereco->cidade }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            @php
                                $matricula = $minhasMatriculas->get($academia->id);
                            @endphp

                            @if(!$matricula)
                                <!-- Botão Matricular -->
                                <form action="{{ route('aluno.matricular', $academia->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="btn btn-primario">Matricular-se</button>
                                </form>
                            @elseif($matricula->pivot->status === 'ativa')
                                <!-- Abrir a academia para ver os planos -->
                                <a href="{{ route('aluno.academia', $academia->id) }}" class="btn btn-secundario">Abrir academia</a>

                                <!-- Botão Desmatricular -->
                                <form action="{{ route('aluno.desmatricular', $academia->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Desmatricular-se? Suas assinaturas nesta academia serão inativadas.')">
                                    @csrf
                                    <button type="submit" class="btn btn-contorno">Desmatricular-se</button>
                                </form>
                            @else
                                <!-- Botão Rematricular -->
                                <form action="{{ route('aluno.matricular', $academia->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="btn btn-secundario">Rematricular-se</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        @if(count($academias) == 0)
            <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                <h3 class="font-serif text-xl font-semibold text-foreground mb-1">Nenhuma academia encontrada</h3>
                <p class="text-muted-foreground max-w-sm">Ainda não há academias parceiras cadastradas no sistema. Volte novamente mais tarde!</p>
            </div>
        @endif
    </div>
@endsection
