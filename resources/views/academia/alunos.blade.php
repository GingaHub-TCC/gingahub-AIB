@extends('layouts.painel')

@section('titulo', 'Alunos')

@section('conteudo')
    <div class="mb-8">
        <h1 class="font-serif text-3xl md:text-4xl font-bold mb-2">Alunos</h1>
        <p class="text-muted-foreground">Todos os alunos que já se matricularam na {{ $academia->nome_fantasia }}, com os planos que assinam.</p>
    </div>

    <div class="bg-card rounded-2xl border border-border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-muted text-foreground font-semibold text-sm border-b border-border">
                        <th class="px-6 py-4">Aluno</th>
                        <th class="px-6 py-4">Contato</th>
                        <th class="px-6 py-4">Matrícula</th>
                        <th class="px-6 py-4">Planos ativos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($matriculas as $matricula)
                        @php $ativas = $matricula->assinaturas->where('status', 'ativa'); @endphp
                        <tr class="{{ $matricula->status === 'ativa' ? '' : 'opacity-60' }}">
                            <td class="px-6 py-4">
                                <p class="font-medium">{{ $matricula->aluno->pessoa->nome }} {{ $matricula->aluno->sobrenome }}</p>
                                <p class="text-sm text-muted-foreground">{{ $matricula->aluno->pessoa->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-muted-foreground">{{ $matricula->aluno->pessoa->contato ?: '-' }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($matricula->status === 'ativa')
                                    <span class="inline-block border border-border text-foreground font-medium px-2 py-0.5 rounded">Ativa</span>
                                    <p class="text-muted-foreground mt-1">desde {{ $matricula->data_matricula?->format('d/m/Y') }}</p>
                                @else
                                    <span class="inline-block bg-muted text-muted-foreground font-medium px-2 py-0.5 rounded">Inativa</span>
                                    <p class="text-muted-foreground mt-1">saiu em {{ $matricula->data_cancelamento?->format('d/m/Y') }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @forelse($ativas as $assinatura)
                                    <span class="inline-block border border-border text-foreground px-2 py-0.5 rounded mr-1 mb-1">{{ $assinatura->plano->nome }}</span>
                                @empty
                                    <span class="text-muted-foreground">Nenhum</span>
                                @endforelse
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center text-muted-foreground">Nenhum aluno matriculado ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
